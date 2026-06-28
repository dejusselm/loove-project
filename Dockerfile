FROM php:8.3-apache
RUN docker-php-ext-install mysqli pdo pdo_mysql

RUN apt-get update && apt-get install -y msmtp mailutils

RUN echo "account default\nhost mailhog\nport 1025\nfrom app@chat.local\ntls off\nsyslog off" > /etc/msmtprc

RUN echo "sendmail_path = \"/usr/bin/msmtp -t\"" > /usr/local/etc/php/conf.d/mail.ini

RUN a2enmod rewrite

EXPOSE 80