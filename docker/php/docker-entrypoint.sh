#!/bin/sh
set -e

export DB_PASS=`cat $DB_PASS_FILE`
export DATABASE_URL="mysql://$DB_USER:$DB_PASS@$DB_HOST/$DB_NAME"

export JWT_PASS_PHRASE=`cat $JWT_PASS_PHRASE_FILE`

export CORS_ALLOW_ORIGIN="$CORS_ALLOW_ORIGIN"
export AM_PLATFORM_ENV="$AM_PLATFORM_ENV"

if [ "$APP_ENV" != "test" ]; then
    export MESSENGER_TRANSPORT_DSN_USER_ACCOUNT="$MESSENGER_TRANSPORT_DSN.user-account"
else
    export MESSENGER_TRANSPORT_DSN_USER_ACCOUNT="$MESSENGER_TRANSPORT_DSN"
fi

# first arg is `-f` or `--some-option`
if [ "${1#-}" != "$1" ]; then
	set -- php-fpm "$@"
fi

if [ "$1" = '/usr/bin/supervisord' ] || [ "$1" = 'php' ] || [ "$1" = 'bin/console' ]; then
	mkdir -p var/cache var/log
	setfacl -R -m u:www-data:rwX -m u:"$(whoami)":rwX var
	setfacl -dR -m u:www-data:rwX -m u:"$(whoami)":rwX var

	if [ ! -d config/jwt ]; then
        mkdir config/jwt
    fi

    if [ ! -f "config/jwt/private.pem" ]; then
        openssl genrsa -passout env:JWT_PASS_PHRASE -out config/jwt/private.pem -aes256 4096 && \
        openssl rsa -passin env:JWT_PASS_PHRASE -pubout -in config/jwt/private.pem -out config/jwt/public.pem
    fi

    if [ "$APP_ENV" = 'dev' ]; then
        composer install --prefer-dist --no-progress --no-suggest --no-interaction
        composer dump-env dev
	    bin/console cache:clear
	else
	    composer dump-env prod --empty
	    APP_ENV=prod APP_DEBUG=0 php bin/console cache:clear
	    rm -f .env
	fi

    echo "Waiting for db to be ready..."
    until bin/console doctrine:query:sql "SELECT 1" > /dev/null 2>&1; do
    	sleep 1
    done

    bin/console doctrine:migrations:migrate --no-debug --no-interaction

    bin/console doctrine:fixtures:load --append --no-interaction --no-debug
fi

if [ "$APP_ENV" != "dev" ]; then
    export APP_ENV=prod
fi

exec docker-php-entrypoint "$@"
