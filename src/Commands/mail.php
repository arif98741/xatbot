<?php

use Xatbot\Bot\Models\Mail;
use Xatbot\Bot\Models\Userinfo;

$mail = function (int $who, array $message, int $type) {

    $bot = Xatbot\Bot\API\ActionAPI::getBot();

    if (!$bot->minrank($who, 'mail')) {
        return $bot->network->sendMessageAutoDetection($who, $bot->botlang('not.enough.rank'), $type);
    }

    if (isset($message[2]) && (isset($message[3]))) {
        if (empty($message[2]) && (in_array(substr($message[3], 0, 1), ['(', ':', ')']))) {
            unset($message[2]);
            $message = array_values($message);
        }
    }

    if (!isset($message[1]) || empty($message[1])) {
        return $bot->network->sendMessageAutoDetection(
            $who,
            'Usage: !mail [xatid/regname/read/check/store/unstore/staff/delete] [info]',
            $type
        );
    }

    switch ($message[1]) {
        case 'read':
            if (!isset($message[2]) || empty($message[2]) || !in_array($message[2], ['old', 'new', 'stored'])) {
                return $bot->network->sendMessageAutoDetection($who, 'Usage: !mail read [old/new/stored]', $type);
            }

            if ($message[2] == 'old') {
                $infos = ['touser' => $who, 'read' => true, 'store' => false];
            } elseif ($message[2] == 'new') {
                $infos = ['touser' => $who, 'read' => false, 'store' => false];
            } else {
                $infos = ['touser' => $who, 'read' => true, 'store' => true];
            }

            $mails = Mail::where($infos)->get();
            if (sizeof($mails) == 0) {
                return $bot->network->sendMessageAutoDetection($who, $bot->botlang('cmd.mail.nomessage'), $type);
            }

            if ($type == 1) {
                $type = 2;
            }

            foreach ($mails as $mail) {
                $user = Userinfo::where('xatid', $mail['fromuser'])->first();
                $displayInfo = is_object($user) ? $user->regname . '(' . $user->xatid . ')' : $mail['fromuser'];
                $newMessage = 'Time: ' . gmdate('d/m/Y', $mail->created_at->timestamp) . ' ID: ' . $mail->id .
                    ' From: ' . $displayInfo . ' Message: ' . $mail->message;

                if ($message[2] == 'new') {
                    $mail->read = true;
                    $mail->save();
                }

                if (sizeof($bot->packetsinqueue) > 0) {
                    $bot->packetsinqueue[max(array_keys($bot->packetsinqueue)) + 2000] = [
                        'who' => $who,
                        'message' => $newMessage,
                        'type' => $type
                    ];
                } else {
                    $bot->packetsinqueue[round(microtime(true) * 1000) + 2000] = [
                        'who' => $who,
                        'message' => $newMessage,
                        'type' => $type
                    ];
                }
            }

            if (sizeof($bot->packetsinqueue) > 0) {
                $bot->packetsinqueue[max(array_keys($bot->packetsinqueue)) + 2000] = [
                    'who' => $who,
                    'message' => $bot->botlang('cmd.mail.endmessage'),
                    'type' => $type
                ];
            } else {
                $bot->packetsinqueue[round(microtime(true) * 1000) + 2000] = [
                    'who' => $who,
                    'message' => $bot->botlang('cmd.mail.endmessage'),
                    'type' => $type
                ];
            }
            return;

        case 'empty':
            $mails = Mail::where(['touser' => $who, 'store' => false])->get();
            foreach ($mails as $mail) {
                $mail->delete();
            }
            return $bot->network->sendMessageAutoDetection($who, $bot->botlang('cmd.mail.inboxempty'), $type);
            break;

        case 'check':
            $mails['old'] = Mail::where(['touser' => $who, 'read' => true, 'store' => false])->get();
            $mails['new'] = Mail::where(['touser' => $who, 'read' => false, 'store' => false])->get();
            $mails['stored'] = Mail::where(['touser' => $who, 'read' => false, 'store' => true])->get();

            if (sizeof($mails['old']) == 0 && sizeof($mails['new']) == 0 && sizeof($mails['stored']) == 0) {
                return $bot->network->sendMessageAutoDetection($who, $bot->botlang('cmd.mail.nomessage'), $type);
            }

            return $bot->network->sendMessageAutoDetection(
                $who,
                $bot->botlang('cmd.mail.youhave', [
                    sizeof($mails['new']),
                    sizeof($mails['old']),
                    sizeof($mails['stored'])
                ]),
                $type
            );

        case 'delete':
            if (!isset($message[2]) || empty($message[2]) || !is_numeric($message[2])) {
                return $bot->network->sendMessageAutoDetection($who, 'Usage: !mail delete [mailID]', $type);
            }

            $id = (int)$message[2];
            $mail = Mail::where(['touser' => $who, 'id' => $id])->first();
            if (!empty($mail)) {
                $mail->delete();
                return $bot->network->sendMessageAutoDetection($who, $bot->botlang('cmd.mail.maildeleted'), $type);
            } else {
                return $bot->network->sendMessageAutoDetection(
                    $who,
                    $bot->botlang('cmd.mail.doesnotexit'),
                    $type
                );
            }

        case 'store':
            if (!isset($message[2]) || empty($message[2]) || !is_numeric($message[2])) {
                return $bot->network->sendMessageAutoDetection($who, 'Usage: !mail store [mailID]', $type);
            }

            $id = (int)$message[2];
            $mail = Mail::where(['touser' => $who, 'id' => $id])->first();
            if (!empty($mail)) {
                $mail->store = true;
                $mail->save();
                return $bot->network->sendMessageAutoDetection($who, 'Mail stored!', $type);
            } else {
                return $bot->network->sendMessageAutoDetection(
                    $who,
                    $bot->botlang('cmd.mail.doesnotexit'),
                    $type
                );
            }

        case 'unstore':
            if (!isset($message[2]) || empty($message[2]) || !is_numeric($message[2])) {
                return $bot->network->sendMessageAutoDetection($who, 'Usage: !mail unstore [mailID]', $type);
            }

            $id = (int)$message[2];
            $mail = Mail::where(['touser' => $who, 'id' => $id])->first();
            if (!empty($mail)) {
                $mail->store = false;
                $mail->save();
                return $bot->network->sendMessageAutoDetection($who, $bot->botlang('cmd.mail.mailunstored'), $type);
            } else {
                return $bot->network->sendMessageAutoDetection(
                    $who,
                    $bot->botlang('cmd.mail.doesnotexit'),
                    $type
                );
            }

        case 'staff':
            unset($message[0]);
            unset($message[1]);
            $message = implode(' ', $message);
            $message = trim($message);

            if (!isset($message) || empty($message)) {
                return $bot->network->sendMessageAutoDetection($who, 'Usage: !mail staff [message]', $type);
            }

            if (!$bot->users[$who]->isMain()) {
                return $bot->network->sendMessageAutoDetection($who, $bot->botlang('cmd.mail.onlymain'), $type);
            }

            foreach ($bot->stafflist as $id => $level) {
                if ($who != $id) {
                    $mail = new Mail;
                    $mail->touser = $id;
                    $mail->fromuser = $who;
                    $mail->message = $message;
                    $mail->save();
                }
            }
            return $bot->network->sendMessageAutoDetection($who, $bot->botlang('cmd.mail.sentstaff'), $type);

        default:
            if (!isset($message[1]) || !isset($message[2]) || empty($message[1]) || empty($message[2])) {
                return $bot->network->sendMessageAutoDetection(
                    $who,
                    'Usage: !mail [xatid/regname] [message]',
                    $type
                );
            }

            unset($message[0]);
            $toUser = $message[1];
            unset($message[1]);
            $message = implode(' ', $message);
            $message = trim($message);

            $xatAdmins = [
                7, 'darren',
                100, 'sam',
                101, 'chris',
                804, 'bot',
                42, 'xat',
                225248065, 'tom2',
                69211656, 'tomflash',
                137312609, 'bignum',
                1480868749, 'bignum2'
            ];

            if (in_array(strtolower($toUser), $xatAdmins)) {
                return $bot->network->sendMessageAutoDetection(
                    $who,
                    $bot->botlang('cmd.mail.notallowed'),
                    $type
                );
            }

            if (is_numeric($toUser)) {
                $toUser = (int)$toUser;
                $user = Userinfo::where('xatid', $toUser)->first();
            } else {
                $user = Userinfo::where('regname', $toUser)->first();
            }

            if (isset($user) && sizeof($user) > 0) {
                if ($who != $user->xatid) {
                    $mails = Mail::where(['touser' => $user->xatid, 'read' => false])->get()->toArray();

                    if (sizeof($mails) > 10) {
                        return $bot->network->sendMessageAutoDetection(
                            $who,
                            $bot->botlang('cmd.mail.toomanyunread', [$user->regname]),
                            $type
                        );
                    }

                    $mail = new Mail;
                    $mail->touser = $user->xatid;
                    $mail->fromuser = $who;
                    $mail->message = $message;
                    $mail->save();
                    return $bot->network->sendMessageAutoDetection(
                        $who,
                        $bot->botLang('cmd.mail.messagesent', [$user->regname, $user->xatid]),
                        $type
                    );
                }

                return $bot->network->sendMessageAutoDetection(
                    $who,
                    $bot->botLang('cmd.mail.cantmailyourself'),
                    $type
                );
            }

            return $bot->network->sendMessageAutoDetection($who, $bot->botlang('user.notindatabase'), $type);
    }
};
