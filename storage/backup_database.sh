#!/bin/bash

# Variables
DB_NAME="aura"
BACKUP_DIR="/var/www/laravel/storage/backups"
DATE=$(date +\%F)

# Create backup directory if it doesn't exist
mkdir -p $BACKUP_DIR

# Backup command
# mysqldump -u $DB_USER -p$DB_PASSWORD $DB_NAME > $BACKUP_DIR/${DB_NAME}_$DATE.sql
mysqldump $DB_NAME > $BACKUP_DIR/${DB_NAME}_$DATE.sql


# Optional: delete backups older than 7 days to save space
find $BACKUP_DIR -type f -name "*.sql" -mtime +7 -exec rm {} \;
