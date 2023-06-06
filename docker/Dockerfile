FROM ubuntu:20.04

ARG DEBIAN_FRONTEND=noninteractive

RUN ln -sf /usr/share/zoneinfo/America/Bogota /etc/localtime

RUN apt-get update
RUN apt-get install -y apache2 php libapache2-mod-php \
      php-cli php-xml php-xmlrpc php-gd php-imap php-ldap \
      php-pgsql php-mbstring php-soap php-zip zip unzip git curl \
      default-jre-headless

RUN apt-get install -y --no-install-recommends libreoffice

RUN a2enmod rewrite
RUN a2enmod proxy_http

COPY ./php.ini /etc/php/7.4/apache2/conf.d/custom.ini
COPY ./apache.conf /etc/apache2/sites-available/

RUN a2dissite 000-default
RUN a2ensite apache

WORKDIR /var/www/html

RUN curl -sS https://getcomposer.org/installer | \
      php -- --install-dir=/usr/local/bin --filename=composer \
      --version=1.10.19

EXPOSE 80

CMD ["/usr/sbin/apache2ctl", "-D", "FOREGROUND"]
