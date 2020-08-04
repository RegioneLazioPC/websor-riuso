FROM webdevops/php-apache:7.1

RUN apt-get update && apt-get install nano
RUN apt-get install autoconf automake libtool m4 -y
RUN apt-get install --reinstall procps -y



#Rabbit

RUN apt-get -y install gcc make autoconf libc-dev pkg-config
RUN apt-get -y install libssl-dev
RUN apt-get -y install librabbitmq-dev
#RUN pecl install amqp

RUN echo extension=amqp.so >> /usr/local/etc/php/conf.d/20-amqp.ini


COPY ./ /app

RUN cd /app
