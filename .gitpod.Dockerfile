FROM gitpod/workspace-full:latest

RUN sudo update-alternatives --set php /usr/bin/php8.2

RUN sudo install-packages \
    php-codesniffer \
    php-common \
    php-dev \
    php-gd \
    php-json \
    php-memcached \
    php-mongodb \
    php-pear \
    php-xdebug \
    php8.2-cli \
    php8.2-common \
    php8.2-curl \
    php8.2-dba \
    php8.2-dev \
    php8.2-gd \
    php8.2-igbinary \
    php8.2-imagick \
    php8.2-mbstring \
    php8.2-memcached \
    php8.2-mongodb \
    php8.2-msgpack \
    php8.2-mysql \
    php8.2-opcache \
    php8.2-pgsql \
    php8.2-phpdbg \
    php8.2-readline \
    php8.2-redis \
    php8.2-soap \
    php8.2-sqlite3 \
    php8.2-xdebug \
    php8.2-xml \
    pkg-php-tools

COPY .config /home/gitpod/.config

RUN curl -sS https://starship.rs/install.sh > /tmp/install.sh && \
    sh /tmp/install.sh --yes && \
    echo 'eval "$(starship init bash)"' >> ${HOME}/.bashrc
