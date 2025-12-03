#!/bin/bash
# Railway startup script

echo "Starting PHP server on port $PORT"
echo "DB_HOST: $DB_HOST"
echo "DB_NAME: $DB_NAME"
echo "DB_USER: $DB_USER"

# Start PHP server
php -S 0.0.0.0:${PORT:-8080} -t .
