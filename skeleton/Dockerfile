FROM ubuntu
MAINTAINER GreensterRox

# Base Folders
RUN mkdir -p /var/www/gwc
RUN mkdir /var/log/green_framework
RUN chown www-data /var/log/green_framework
RUn chmod g+w /var/log/green_framework/

# Install Apache/PHP
RUN apt-get -y install apache2
RUN apt-get -y install php5
RUN apt-get -y install libapache2-mod-php5

# Install MariaDB
RUN apt-get -y install software-properties-common
RUN apt-key adv --recv-keys --keyserver hkp://keyserver.ubuntu.com:80 0xcbcb082a1bb943db
RUN add-apt-repository 'deb http://mirror.jmu.edu/pub/mariadb/repo/5.5/ubuntu trusty main'
RUN apt-get -y update
RUN apt-get -y install mariadb-client mariadb-server
RUN update-rc.d mysql defaults

# Install git
RUN apt-get -y install git

# Add code
WORKDIR /var/www/
RUN git clone https://github.com/GreensterRox/gwc.git gwc

ADD apache/ubuntu-vhost-example.conf /etc/apache2/sites-available/000-default.conf 
RUN sed -i '$ a include_path = ".:/var/www/gwc/classes:/var/www/gwc/tests"' /etc/php5/apache2/php.ini
RUN ln -s /etc/apache2/mods-available/rewrite.load /etc/apache2/mods-enabled/rewrite.load

RUN apt-get -y install wget

RUN mkdir -p /var/www/gwc-skeleton
ADD htdocs /var/www/gwc-skeleton/htdocs
ADD lib /var/www/gwc-skeleton/lib
ADD conf /var/www/gwc-skeleton/conf
ADD sites /var/www/gwc/sites

# docker run -p 8080:80 -h gwc-skeleton.local -i -t --entrypoint /bin/bash test