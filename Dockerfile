ARG PHP_VERSION=7.4

FROM php:${PHP_VERSION}-fpm-alpine AS i_help_shopping

RUN apk add --no-cache \
		acl \
		file \
		gettext \
		mysql \
		git \
		shadow \
		nginx   \
		supervisor \
	;

RUN set -ex \
  	&& apk update \
    && apk add --no-cache libsodium \
    && apk add --no-cache --virtual build-dependencies g++ make autoconf libsodium-dev\
    && docker-php-source extract \
    && pecl install libsodium \
    && docker-php-ext-enable sodium \
    && docker-php-source delete \
    && cd  / && rm -fr /src \
    && apk del build-dependencies \
    && rm -rf /tmp/*

ARG APCU_VERSION=5.1.18
RUN apk add --no-cache libzip-dev && docker-php-ext-configure zip && docker-php-ext-install zip
RUN set -eux; \
	apk add --no-cache --virtual .build-deps \
		$PHPIZE_DEPS \
		icu-dev \
		zlib-dev \
	; \
	\
	docker-php-ext-install -j "$(getconf _NPROCESSORS_ONLN)" \
		intl \
		pdo \
		pdo_mysql \
	; \
	pecl install \
		apcu-${APCU_VERSION} \
	; \
	pecl clear-cache; \
	docker-php-ext-enable \
		apcu \
		opcache \
	; \
	\
	runDeps="$( \
		scanelf --needed --nobanner --format '%n#p' --recursive /usr/local/lib/php/extensions \
			| tr ',' '\n' \
			| sort -u \
			| awk 'system("[ -e /usr/local/lib/" $1 " ]") == 0 { next } { print "so:" $1 }' \
	)"; \
	apk add --no-cache --virtual .api-phpexts-rundeps $runDeps; \
	\
	apk del .build-deps

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer
COPY docker/php/php.ini /usr/local/etc/php/php.ini

RUN apk update
RUN apk add curl bash gzip
RUN curl -sS https://get.symfony.com/cli/installer | bash

RUN mv /root/.symfony/bin/symfony /usr/local/bin/symfony

COPY docker/nginx/conf.d/default.conf /etc/nginx/conf.d/default.conf
COPY docker/nginx/nginx.conf /etc/nginx/nginx.conf
COPY docker/supervisor.d/supervisor.ini /etc/supervisor.d/supervisor.ini

RUN chmod 644 /etc/supervisord.conf && touch /var/log/supervisord.log && chmod 777 /var/log/supervisord.log

ENV COMPOSER_ALLOW_SUPERUSER=1
RUN set -eux; \
	composer global require "hirak/prestissimo:^0.3" --prefer-dist --no-progress --no-suggest --classmap-authoritative; \
	composer clear-cache
ENV PATH="${PATH}:/root/.composer/vendor/bin"

RUN mkdir -p /var/log/newrelic /run/nginx/

RUN set -eux; \
	composer global require "symfony/flex" --prefer-dist --no-progress --no-suggest --classmap-authoritative; \
	composer clear-cache

WORKDIR /srv/api

ARG APP_ENV=prod

COPY composer.json composer.lock symfony.lock .env ./

RUN set -eux; \
    docker-php-ext-install -j "$(getconf _NPROCESSORS_ONLN)" \
		bcmath \
		sockets \
    ;

RUN set -eux; \
	composer install --prefer-dist --no-dev --no-autoloader --no-scripts --no-progress --no-suggest; \
	composer clear-cache

COPY bin bin/
COPY config config/
COPY public public/
COPY src src/
COPY templates templates/
COPY translations translations/

RUN set -eux; \
	mkdir -p var/cache var/log; \
	composer dump-autoload --classmap-authoritative --no-dev; \
	composer run-script --no-dev post-install-cmd; \
	chmod +x bin/console; sync
VOLUME /srv/api/var

RUN usermod -u 1000 www-data

COPY docker/php/docker-entrypoint.sh /usr/local/bin/docker-entrypoint
RUN chmod +x /usr/local/bin/docker-entrypoint

ENTRYPOINT ["docker-entrypoint"]

CMD ["/usr/bin/supervisord", "--nodaemon", "--configuration", "/etc/supervisord.conf"]
