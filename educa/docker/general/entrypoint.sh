#!/bin/sh
service nginx start
php-fpm -D

# Test crontab
supercronic -test /etc/crontab.txt || exit 1

# Start supercronic
if [ -n "${SUPERCRONIC_EXTRA_FLAGS}" ]; then
  echo "The variable SUPERCRONIC_EXTRA_FLAGS is not empty, using extra flags"
  supercronic $SUPERCRONIC_EXTRA_FLAGS /etc/crontab.txt
else
  echo "The variable SUPERCRONIC_EXTRA_FLAGS is empty, starting normally"
  supercronic /etc/crontab.txt
fi
