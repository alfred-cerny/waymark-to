# Use Ubuntu Slim as the base image
FROM ubuntu:25.04

# Build arguments
ARG PHP_VERSION=8.4
ENV DEBIAN_FRONTEND=noninteractive

# Install PHP, extensions, and essential tools in one layer
RUN apt-get update && \
    apt-get upgrade -y && \
    apt-get install -y --no-install-recommends \
        php${PHP_VERSION} \
        php${PHP_VERSION}-cli \
        php${PHP_VERSION}-dev \
        php${PHP_VERSION}-pdo \
        php${PHP_VERSION}-pdo-pgsql \
        php${PHP_VERSION}-pgsql \
        php${PHP_VERSION}-dom \
        php${PHP_VERSION}-iconv \
        php${PHP_VERSION}-opcache \
        php${PHP_VERSION}-imap \
        php${PHP_VERSION}-gd \
        php${PHP_VERSION}-zip \
        php${PHP_VERSION}-curl \
        php${PHP_VERSION}-phar \
        php${PHP_VERSION}-tokenizer \
        php${PHP_VERSION}-mbstring \
        ca-certificates \
        curl \
        unzip \
        git && \
    apt-get clean && rm -rf /var/lib/apt/lists/*

# Install Composer globally
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Set working directory
WORKDIR /www