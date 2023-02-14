FROM ubuntu:latest

COPY *.php /var/www/html/
COPY images/ /var/www/html/images
COPY database.sql .

ENV TZ=Asia/Karachi
RUN ln -snf /usr/share/zoneinfo/$TZ /etc/localtime && echo $TZ > /etc/timezone

RUN apt-get update
RUN apt-get -y install php mysql-server php-mysql libapache2-mod-php libapache2-mod-security2
RUN cp /etc/modsecurity/modsecurity.conf-recommended /etc/modsecurity/modsecurity.conf
RUN service mysql start && mysql -e "source database.sql"

EXPOSE 80
EXPOSE 3306

CMD ["bash", "-c", "service mysql start && apache2ctl -D FOREGROUND"]