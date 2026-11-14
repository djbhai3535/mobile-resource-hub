# Shared Hosting Disaster Recovery & Backup / Restore Manual

## 1. Overview & Scope
A reliable disaster recovery strategy for Mobile Resource Hub on Hostinger Shared Hosting requires backing up both the relational database and physical files stored on disk.

### Backup Scope
1. **Relational Database (MySQL 8.0+):** Schema, migrations, users, RBAC, mobile devices, dynamic specifications, firmware catalog, flash files, drivers, search index, themes, advertisements, and audit logs.
2. **Private File Storage (`mrh_app/storage/app/private_downloads/`):** Locally hosted firmware stock ROMs, scatter files, flashtools, and USB driver archives.
3. **Public Media Storage (`mrh_app/storage/app/public/`):** Brand logos, device render images, editorial thumbnails, and ad banners.
4. **Application Configuration (`mrh_app/.env`):** Environment secrets, application keys, and database connection strings.

---

## 2. Automated Backup Execution via CLI / SSH

### Database Dump Script
Run daily via scheduled cron or SSH:
```bash
#!/bin/bash
# Hostinger automated MySQL dump script
BACKUP_DIR="/home/u123456789/backups"
TIMESTAMP=$(date +"%Y%m%d_%H%M%S")
DB_NAME="u123456789_mrh"
DB_USER="u123456789_dbuser"
DB_PASS="YourSecureDatabasePassword"

mkdir -p $BACKUP_DIR/db

# Execute mysqldump with single transaction to avoid table locking
mysqldump --single-transaction --quick --routines --triggers \
    -u "$DB_USER" -p"$DB_PASS" "$DB_NAME" | gzip > "$BACKUP_DIR/db/mrh_db_${TIMESTAMP}.sql.gz"

# Retain last 14 days of database dumps
find "$BACKUP_DIR/db" -name "mrh_db_*.sql.gz" -mtime +14 -delete
```

### File Storage Archival Script
Run weekly via cron:
```bash
#!/bin/bash
BACKUP_DIR="/home/u123456789/backups"
TIMESTAMP=$(date +"%Y%m%d_%H%M%S")
APP_STORAGE="/home/u123456789/mrh_app/storage/app"

mkdir -p $BACKUP_DIR/files

# Archive private downloads and public media outside public_html
tar -czf "$BACKUP_DIR/files/mrh_storage_${TIMESTAMP}.tar.gz" \
    -C "$APP_STORAGE" private_downloads public

# Retain last 30 days of storage archives
find "$BACKUP_DIR/files" -name "mrh_storage_*.tar.gz" -mtime +30 -delete
```

---

## 3. Hostinger hPanel GUI Backup (No-SSH Option)
If SSH access is disabled on the shared hosting plan:
1. **Database Export:**
   - Log into **Hostinger hPanel** &rarr; **Databases** &rarr; **phpMyAdmin**.
   - Select database `u123456789_mrh`.
   - Click the **Export** tab &rarr; Choose **Quick** method &rarr; Format: **SQL** &rarr; Click **Export**.
2. **File Backups:**
   - Navigate to **Files** &rarr; **Backups** in hPanel.
   - Hostinger provides automated daily/weekly snapshots of the entire home directory. Click **Generate New Backup** &rarr; Select files and MySQL database.
   - Download the generated archive and store securely in off-site encrypted storage.

---

## 4. Disaster Recovery & Restoration Procedure

### Step 1: Database Restoration
In an isolated staging or recovery environment:
```bash
# Decompress and import database dump
gunzip < /home/u123456789/backups/db/mrh_db_20261114_080000.sql.gz | mysql -u "$DB_USER" -p"$DB_PASS" "$DB_NAME"
```

### Step 2: Storage Restoration
```bash
# Extract storage archive back to private application storage
tar -xzf /home/u123456789/backups/files/mrh_storage_20261114_080000.tar.gz -C /home/u123456789/mrh_app/storage/app/

# Re-link public storage symlink if missing
cd /home/u123456789/mrh_app
php artisan storage:link
```

### Step 3: Search Index Synchronization & Cache Priming
```bash
cd /home/u123456789/mrh_app

# Clear any stale compiled views or cached configuration
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Rebuild search token documents idempotently
php artisan search:index-rebuild

# Prime theme styles
php artisan optimize
```

---

## 5. Security & Isolation Safeguards
- **Zero Public Access:** Backup directories must NEVER be created under `public_html/`. The backup path `/home/uXXXX/backups/` resides strictly above the web server document root.
- **No Direct Download Links:** Backups must never be served via HTTP endpoints. Download files via SFTP or encrypted rsync to secure off-site storage.
- **Never Restore on Production Without Verification:** Always verify SQL dump integrity in an isolated test environment before executing `mysql < backup.sql` against the live production database.
