#!/bin/sh
set -e

# Navigate to the correct working directory
cd /var/www/html

# Start the SSH service
service ssh start

# Start Apache in the foreground
exec apache2-foreground
