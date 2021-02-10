FROM php:7.2-apache
RUN docker-php-ext-install mysqli
RUN docker-php-ext-enable mysqli
WORKDIR /usr/local/etc/php
RUN cp php.ini-production php.ini
RUN sed -ri -e 's!expose_php = On!expose_php = Off!g' php.ini
WORKDIR /etc/apache2/
RUN sed -ri -e 's!ServerTokens OS!ServerTokens Prod!g' ./conf-available/security.conf
RUN sed -ri -e 's!ServerSignature On!ServerSignature Off!g' ./conf-available/security.conf
WORKDIR /var/www/html/
COPY accueil/js ./js
COPY accueil/bootstrap-3.2.0-dist  ./bootstrap-3.2.0-dist
COPY accueil/img ./img
COPY accueil/jquery-ui-1.11.1.custom ./jquery-ui-1.11.1.custom
COPY accueil/multipleCAS.php ./multipleCAS.php
COPY accueil/favicon.ico ./favicon.ico
VOLUME ["/var/www/html/demo/pain"]
EXPOSE 80
