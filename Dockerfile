FROM php:8.3-fpm

# Arguments defined in docker-compose.yml
ARG user
ARG uid
ARG ugroup
ARG gid

# Install system dependencies
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    libzip-dev \
    libjpeg-dev \
    libfreetype6-dev \
    libjpeg62-turbo-dev \
    zip \
    unzip \
    libpq-dev \
    cron \
    supervisor \
    libaio1 \
    libaio-dev

# nvm install dir and env variables
RUN mkdir /usr/local/nvm
ENV NVM_DIR /usr/local/nvm
ENV NODE_VERSION 20.19.2

# install node and npm
RUN curl https://raw.githubusercontent.com/nvm-sh/nvm/v0.39.1/install.sh | bash \
    && . $NVM_DIR/nvm.sh \
    && nvm install $NODE_VERSION \
    && nvm alias default $NODE_VERSION \
    && nvm use default

# add node and npm to path so the commands are available
ENV NODE_PATH $NVM_DIR/v$NODE_VERSION/lib/node_modules
ENV PATH $NVM_DIR/versions/node/v$NODE_VERSION/bin:$PATH

# confirm installation
RUN node -v
RUN npm -v

# Clear cache
RUN apt-get clean && rm -rf /var/lib/apt/lists/*

# Install PHP extensions
RUN docker-php-ext-configure gd --enable-gd --with-freetype --with-jpeg
RUN docker-php-ext-install pdo pdo_mysql mysqli pdo_pgsql pgsql mbstring exif pcntl bcmath gd zip intl

# Install redis extension
RUN pecl install -o -f redis \
          &&  rm -rf /tmp/pear \
          &&  docker-php-ext-enable redis

# Copy supervisor conf
COPY ./docker-compose/supervisor/horizon.conf /etc/supervisor/conf.d/horizon.conf

# Copy start entrypoint
COPY ./docker-compose/start.sh /usr/local/bin/start

# Get latest Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /opt

# Create system user to run Composer and Artisan Commands
#RUN useradd -G www-data,root -u $uid -d /home/$user $user

RUN usermod -u $uid www-data

RUN groupmod -g $gid www-data

#RUN mkdir -p /home/$user/.composer && \
#    chown -R $user:$user /home/$user && \
#    chown -R $user:$user /usr/local/bin/start && \
#    chmod u+x /usr/local/bin/start

RUN chown -R www-data:www-data /usr/local/bin/start && \
    chmod u+x /usr/local/bin/start

# Set working directory
WORKDIR /var/www

# Copy project
COPY . /var/www
RUN chown -R www-data:www-data /var/www && \
    chmod 775 /var/www/storage && \
    chmod u+x /var/www/docker-compose/scheduler.sh

# Use the default production configuration
RUN mv "$PHP_INI_DIR/php.ini-production" "$PHP_INI_DIR/php.ini"

# Copy custom php ini configs
COPY ./docker-compose/php/custom.ini "$PHP_INI_DIR/conf.d/custom.ini"

CMD ["/usr/local/bin/start"]

