#!/bin/sh
set -eu
export APP_KEY="base64:$(php -r 'echo base64_encode(random_bytes(32));')"
exec php vendor/bin/phpunit
