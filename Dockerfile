FROM php:8.4 AS php

ARG UID=1000
ARG GID=1000

RUN apt-get update -y
RUN apt-get install -y unzip libpq-dev libcurl4-gnutls-dev 
RUN docker-php-ext-install pdo pdo_mysql bcmath

RUN pecl install -o -f redis \
    && rm -rf /tmp/pear \
    && docker-php-ext-enable redis

# Create non-root user matching host UID/GID
RUN groupadd -g ${GID} appgroup && \
    useradd -u ${UID} -g appgroup -m -s /bin/bash appuser

# Copy composer binary as root
COPY --from=composer:2.8.12 /usr/bin/composer /usr/bin/composer

# Copy and make entrypoint executable as root (before user switch)
COPY .docker/entrypoint.sh /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/entrypoint.sh

WORKDIR /var/www
COPY . .

# Chown the app directory to the new user (fixes ownership from build steps)
RUN chown -R appuser:appgroup /var/www

# Switch to non-root user for runtime
USER appuser

ENV PORT=8000
ENTRYPOINT ["/usr/local/bin/entrypoint.sh"]
