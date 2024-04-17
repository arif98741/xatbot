FROM php:8.1-alpine
RUN docker-php-ext-install sockets pcntl
