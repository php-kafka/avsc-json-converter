FROM php:7.4-cli-alpine3.14

ARG HOST_USER_ID
ARG HOST_USER

# PHP: Copy configuration files & remove dist files
RUN mkdir /phpIni
COPY files/bin/ /usr/local/bin/
COPY files/php/ /phpIni

# SYS: Install required packages
RUN apk --no-cache upgrade && \
    apk --no-cache add bash git sudo make

RUN adduser -u $HOST_USER_ID -D -H $HOST_USER && \
    echo "ALL ALL=NOPASSWD: ALL" >> /etc/sudoers && \
    addgroup $HOST_USER www-data

# COMPOSER: install binary
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/bin --filename=composer

# PHP: Install php extensions
RUN pecl channel-update pecl.php.net && \
    pecl install pcov && \
    php-ext-enable pcov

USER $HOST_USER

WORKDIR /var/www/html

