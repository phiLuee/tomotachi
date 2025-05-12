# --- Base Stage ---
FROM php:8.4-cli AS base
WORKDIR /app

COPY --from=ghcr.io/mlocati/php-extension-installer /usr/bin/install-php-extensions /usr/local/bin/
RUN apt-get update && apt-get install -y --no-install-recommends \
        git \
        unzip \
        zip \
        # Füge hier weitere *notwendige* Pakete hinzu
    && rm -rf /var/lib/apt/lists/* \
    && install-php-extensions gd intl mbstring mysqli pdo_mysql zip # Nur notwendige Prod-Extensions

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Kopiere nur notwendige Composer-Dateien für die Installation
COPY composer.json composer.lock ./

# --- Production Stage ---
FROM base AS production

# Installiere nur Produktionsabhängigkeiten
RUN composer install --no-interaction --no-plugins --no-scripts --no-dev --prefer-dist --optimize-autoloader

COPY . .

# TODO: Add production-specific configurations here

# --- Development Stage ---
FROM base AS development

# Installiere alle Abhängigkeiten (inkl. Dev)
RUN apt-get update && apt-get install -y --no-install-recommends \
        nodejs \
        npm \
        openssh-client \
    && rm -rf /var/lib/apt/lists/* \
    && install-php-extensions xdebug


RUN groupadd --gid 1000 user && \
    useradd --uid 1000 --gid 1000 --create-home --shell /bin/bash user
ENV HOME=/home/user

USER user
WORKDIR /home/user/workspace

# Installiere alle Abhängigkeiten (inkl. Dev)
# COPY --chown=user:user composer.json composer.lock ./
# RUN composer install --no-interaction

# Setze den Standardbefehl für den Dev-Container
CMD ["sh"]