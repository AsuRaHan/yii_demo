FROM php:apache-bullseye

RUN a2enmod rewrite

RUN apt-get update && apt-get install -y \
		libfreetype6-dev \
		libjpeg62-turbo-dev \
		libpng-dev \
		libmcrypt-dev \
        curl \
        zip \
        unzip \
	&& docker-php-ext-configure gd --with-freetype --with-jpeg \
	&& docker-php-ext-install -j$(nproc) gd

RUN docker-php-ext-install mysqli pdo_mysql exif pcntl bcmath gd gettext 

RUN docker-php-ext-install exif
RUN apt-get install libmagickwand-dev libmagickcore-dev -y
RUN pecl install imagick
RUN docker-php-ext-enable imagick
RUN PHP_OPENSSL=yes
RUN docker-php-ext-install xml
RUN docker-php-ext-install filter
RUN apt-get install libzip-dev -y
RUN docker-php-ext-install zip
RUN docker-php-ext-install bcmath
RUN apt install libwebp-dev
RUN docker-php-ext-configure gd --with-freetype --with-jpeg --with-webp
RUN docker-php-ext-install gd
RUN docker-php-ext-install intl

ARG user
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer
RUN mkdir -p /home/$user/.composer && \
    chown -R $user:$user /home/$user