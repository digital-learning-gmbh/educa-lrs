FROM php:8.2-fpm
RUN echo "deb http://deb.debian.org/debian bullseye-backports main contrib non-free" > /etc/apt/sources.list.d/bullseye-backports.list
RUN apt -yqq update
RUN apt -yqq install libxml2-dev zlib1g-dev libpng-dev libldb-dev libldap2-dev libsodium-dev libzip-dev git freetype* libpng-dev libjpeg-dev libfreetype6-dev libwebp-dev imagemagick cron libreoffice
RUN docker-php-ext-configure gd --enable-gd --with-freetype --with-jpeg --with-webp
RUN docker-php-ext-install pdo_mysql xml gd ldap sodium zip
#Node Install
RUN curl -fsSL https://deb.nodesource.com/setup_18.x | bash -
RUN apt-get update && apt-get install -y nodejs && apt-get clean

#Install yarn
RUN curl -sS https://dl.yarnpkg.com/debian/pubkey.gpg | apt-key add -
RUN echo "deb https://dl.yarnpkg.com/debian/ stable main" | tee /etc/apt/sources.list.d/yarn.list
RUN apt update && apt install yarn


# Install Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer


#Workdir
WORKDIR /usr/share/nginx/html
