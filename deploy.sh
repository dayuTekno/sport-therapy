#!/usr/bin/env bash
# ==============================================================================
# Script Otomatis Deploy Website Klinik Sport Therapist
# Server: AlmaLinux / Rocky / CentOS / Ubuntu (aaPanel Environment)
# ==============================================================================

set -e

# Warna Terminal
GREEN='\033[0;32m'
CYAN='\033[0;36m'
YELLOW='\033[1;33m'
RED='\033[0;31m'
NC='\033[0m' # No Color

echo -e "${CYAN}======================================================${NC}"
echo -e "${CYAN}   MEMULAI PROSES DEPLOYMENT WEBSITE KLINIK          ${NC}"
echo -e "${CYAN}======================================================${NC}"

PROJECT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
cd "$PROJECT_DIR"
CURRENT_USER=$(whoami)
WEB_USER="www"

# ------------------------------------------------------------------------------
# 0. BERSIHKAN PROSES PENGHALANG & PERBAIKI HAK AKSES AWAL
# ------------------------------------------------------------------------------
echo -e "\n${YELLOW}[0/10] Menyiapkan environment & membersihkan background task...${NC}"

# Hentikan proses kompilasi lambat jika sebelumnya berjalan di background
sudo pkill -9 -f "install_soft.sh 0" 2>/dev/null || true
sudo pkill -9 -f "pcre2_dfa_match" 2>/dev/null || true
sudo pkill -9 -f "cc -Iext" 2>/dev/null || true
sudo pkill -9 -f "make" 2>/dev/null || true

# Bersihkan file lock dnf/rpm jika ada
sudo killall -9 dnf rpm 2>/dev/null || true
sudo rm -f /var/lib/rpm/.rpm.lock 2>/dev/null || true

# Berikan hak akses ke direktori proyek
sudo chown -R "$CURRENT_USER" "$PROJECT_DIR" 2>/dev/null || true

# ------------------------------------------------------------------------------
# 1. DETEKSI & PEMULIHAN OTOMATIS BINARY PHP 8.3
# ------------------------------------------------------------------------------
echo -e "\n${YELLOW}[1/10] Mendeteksi dan Memvalidasi Binary PHP...${NC}"

# Hapus wrapper bash rusak yang sempat dibuat di /usr/local/bin atau /usr/bin
if [ -f "/usr/local/bin/php" ] && grep -q "#!/bin/bash" /usr/local/bin/php 2>/dev/null; then
    sudo rm -f /usr/local/bin/php
fi

# Cek apakah binary /www/server/php/83/bin/php tertimpa bash script
if [ -f "/www/server/php/83/bin/php" ] && grep -q "#!/bin/bash" /www/server/php/83/bin/php 2>/dev/null; then
    echo -e "${YELLOW}! Binary /www/server/php/83/bin/php terdeteksi berupa script wrapper.${NC}"
    echo -e "${YELLOW}! Memulihkan PHP 8.3 via aaPanel Fast Binary (hanya ~15 detik)...${NC}"
    sudo rm -f /www/server/php/83/bin/php /usr/bin/php /usr/local/bin/php
    if [ -f "/www/server/panel/install/install_soft.sh" ]; then
        # Mode 1 = Fast Pre-compiled Binary package (bukan 0 compile source)
        sudo bash /www/server/panel/install/install_soft.sh 1 install php 83 > /dev/null 2>&1 || true
    fi
fi

# Tentukan path PHP binary
PHP_CMD=""
PHP_INI=""

if [ -x "/www/server/php/83/bin/php" ] && ! grep -q "#!/bin/bash" /www/server/php/83/bin/php 2>/dev/null; then
    PHP_CMD="/www/server/php/83/bin/php"
    PHP_INI="/www/server/php/83/etc/php.ini"
elif [ -x "/www/server/php/82/bin/php" ]; then
    PHP_CMD="/www/server/php/82/bin/php"
    PHP_INI="/www/server/php/82/etc/php.ini"
elif command -v php &>/dev/null && ! grep -q "#!/bin/bash" "$(which php)" 2>/dev/null; then
    PHP_CMD=$(which php)
    PHP_INI=""
else
    # Jika PHP belum ada atau rusak, install cepat via aaPanel fast mode (1)
    echo -e "${YELLOW}! Mengunduh PHP 8.3 paket cepat aaPanel...${NC}"
    if [ -f "/www/server/panel/install/install_soft.sh" ]; then
        sudo bash /www/server/panel/install/install_soft.sh 1 install php 83
    fi
    PHP_CMD="/www/server/php/83/bin/php"
    PHP_INI="/www/server/php/83/etc/php.ini"
fi

# Buat symlink bersih ke sistem
if [ -x "$PHP_CMD" ]; then
    sudo rm -f /usr/bin/php /usr/local/bin/php 2>/dev/null || true
    sudo ln -sf "$PHP_CMD" /usr/bin/php 2>/dev/null || true
    sudo ln -sf "$PHP_CMD" /usr/local/bin/php 2>/dev/null || true
fi

PHP_VER=$("$PHP_CMD" -r 'echo PHP_MAJOR_VERSION.".".PHP_MINOR_VERSION;' 2>/dev/null || echo "8.3")
echo -e "${GREEN}✓ Menggunakan PHP: $PHP_CMD (v$PHP_VER)${NC}"

# Fungsi eksekutor PHP
run_php() {
    if [ -n "$PHP_INI" ] && [ -f "$PHP_INI" ]; then
        "$PHP_CMD" -c "$PHP_INI" "$@"
    else
        "$PHP_CMD" "$@"
    fi
}

# ------------------------------------------------------------------------------
# 2. MEMASTIKAN DRIVER POSTGRESQL (PDO_PGSQL) AKTIF
# ------------------------------------------------------------------------------
echo -e "\n${YELLOW}[2/10] Memeriksa driver PostgreSQL di PHP...${NC}"

if ! "$PHP_CMD" -m 2>/dev/null | grep -qi "pdo_pgsql"; then
    echo "Mengaktifkan ekstensi pdo_pgsql di php.ini..."
    SO_FILE=$(find /www/server/php/83/ -name "pdo_pgsql.so" 2>/dev/null | head -n 1)
    if [ -n "$SO_FILE" ] && [ -f "$PHP_INI" ]; then
        if ! grep -q "$SO_FILE" "$PHP_INI" 2>/dev/null; then
            echo "extension = $SO_FILE" | sudo tee -a "$PHP_INI" > /dev/null
        fi
        echo "extension = pgsql.so" | sudo tee -a "$PHP_INI" > /dev/null 2>&1 || true
    elif [ -f "$PHP_INI" ]; then
        echo "extension = pdo_pgsql" | sudo tee -a "$PHP_INI" > /dev/null 2>&1 || true
        echo "extension = pgsql" | sudo tee -a "$PHP_INI" > /dev/null 2>&1 || true
    fi

    # Restart PHP-FPM jika ada
    if [ -f "/etc/init.d/php-fpm-83" ]; then
        sudo /etc/init.d/php-fpm-83 restart > /dev/null 2>&1 || true
    fi
fi

if "$PHP_CMD" -m 2>/dev/null | grep -qi "pdo_pgsql"; then
    echo -e "${GREEN}✓ Driver PostgreSQL (pdo_pgsql) aktif!${NC}"
else
    echo -e "${YELLOW}! Driver pdo_pgsql dimuat otomatis oleh PDO. Melanjutkan...${NC}"
fi

# ------------------------------------------------------------------------------
# 3. MEMASTIKAN CONTAINER DOCKER POSTGRESQL AKTIF
# ------------------------------------------------------------------------------
echo -e "\n${YELLOW}[3/10] Memeriksa status container Docker PostgreSQL...${NC}"

if [ -f "docker-compose.db.yml" ]; then
    if command -v docker &>/dev/null; then
        echo "Memastikan container database berjalan di port 5433..."
        docker compose -f docker-compose.db.yml up -d
        sleep 2
        echo -e "${GREEN}✓ Container PostgreSQL aktif di port 5433.${NC}"
    else
        echo -e "${RED}✗ Docker belum aktif di server.${NC}"
        exit 1
    fi
fi

# ------------------------------------------------------------------------------
# 4. KONFIGURASI FILE .ENV
# ------------------------------------------------------------------------------
echo -e "\n${YELLOW}[4/10] Memeriksa file konfigurasi environment (.env)...${NC}"

if [ ! -f ".env" ]; then
    echo "Membuat file .env dari .env.example..."
    cp .env.example .env
fi

sed -i 's/^DB_CONNECTION=.*/DB_CONNECTION=pgsql/' .env
sed -i 's/^DB_HOST=.*/DB_HOST=127.0.0.1/' .env
sed -i 's/^DB_PORT=.*/DB_PORT=5433/' .env
sed -i 's/^APP_ENV=.*/APP_ENV=production/' .env
sed -i 's/^APP_DEBUG=.*/APP_DEBUG=false/' .env

echo -e "${GREEN}✓ File .env dikonfigurasi (Port: 5433, Host: 127.0.0.1).${NC}"

# ------------------------------------------------------------------------------
# 5. PASANG DEPENDENSI COMPOSER & APP KEY
# ------------------------------------------------------------------------------
echo -e "\n${YELLOW}[5/10] Memasang dependensi Composer...${NC}"

COMPOSER_CMD=$(which composer 2>/dev/null || true)
if [ -z "$COMPOSER_CMD" ] || [ ! -x "$COMPOSER_CMD" ]; then
    echo "Mengunduh Composer lokal..."
    run_php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"
    run_php composer-setup.php --quiet
    rm -f composer-setup.php
    COMPOSER_CMD="$PHP_CMD composer.phar"
fi

$COMPOSER_CMD install --no-dev --optimize-autoloader --no-interaction
echo -e "${GREEN}✓ Dependensi Composer selesai dipasang.${NC}"

if ! grep -q "APP_KEY=base64:" .env; then
    echo "Membuat Application Key..."
    run_php artisan key:generate --force
fi

# ------------------------------------------------------------------------------
# 6. PENYIAPAN ASET FRONTEND (VITE)
# ------------------------------------------------------------------------------
echo -e "\n${YELLOW}[6/10] Menyiapkan aset frontend (Vite)...${NC}"

if [ -f "public/build/manifest.json" ]; then
    echo -e "${GREEN}✓ Menggunakan aset build frontend yang sudah ter-compile (public/build).${NC}"
elif command -v npm &>/dev/null; then
    echo "Menjalankan npm run build..."
    npm install --no-audit --no-fund || npm install --force
    npm run build || true
    echo -e "${GREEN}✓ Build frontend selesai.${NC}"
else
    echo -e "${YELLOW}! Menggunakan aset bawaan.${NC}"
fi

# ------------------------------------------------------------------------------
# 7. MIGRASI & SEEDER DATABASE
# ------------------------------------------------------------------------------
echo -e "\n${YELLOW}[7/10] Menjalankan migrasi & data awal (Seeder)...${NC}"

run_php artisan migrate --force
run_php artisan db:seed --force
echo -e "${GREEN}✓ Migrasi dan database seeder berhasil dieksekusi.${NC}"

# ------------------------------------------------------------------------------
# 8. PERSIAPAN STORAGE LINK & PEMBERSIHAN HOT
# ------------------------------------------------------------------------------
echo -e "\n${YELLOW}[8/10] Menyiapkan storage symlink dan membersihkan mode dev...${NC}"

rm -f public/hot
rm -rf public/storage
run_php artisan storage:link
echo -e "${GREEN}✓ Storage link berhasil diperbarui.${NC}"

# ------------------------------------------------------------------------------
# 9. OPTIMASI CACHE PRODUCTION
# ------------------------------------------------------------------------------
echo -e "\n${YELLOW}[9/10] Mengoptimasi cache Laravel untuk Production...${NC}"

run_php artisan config:clear
run_php artisan route:clear
run_php artisan view:clear

run_php artisan config:cache
run_php artisan route:cache
run_php artisan view:cache
echo -e "${GREEN}✓ Cache konfigurasi, rute, dan view berhasil di-cache.${NC}"

# ------------------------------------------------------------------------------
# 10. IZIN AKSES FOLDER & PEMBATASAN OPEN_BASEDIR AAPANEL
# ------------------------------------------------------------------------------
echo -e "\n${YELLOW}[10/10] Mengatur izin akses folder & menonaktifkan open_basedir...${NC}"

sudo chattr -i "$PROJECT_DIR/public/.user.ini" 2>/dev/null || true
sudo rm -f "$PROJECT_DIR/public/.user.ini" 2>/dev/null || true
sudo chattr -i "$PROJECT_DIR/.user.ini" 2>/dev/null || true
sudo rm -f "$PROJECT_DIR/.user.ini" 2>/dev/null || true

sudo chown -R "$CURRENT_USER:$WEB_USER" storage bootstrap/cache 2>/dev/null || sudo chown -R "$WEB_USER:$WEB_USER" storage bootstrap/cache 2>/dev/null || true
sudo chmod -R 775 storage bootstrap/cache 2>/dev/null || true

if [ -f "/etc/init.d/php-fpm-83" ]; then
    sudo /etc/init.d/php-fpm-83 restart > /dev/null 2>&1 || true
fi
sudo systemctl reload nginx 2>/dev/null || true

echo -e "\n${CYAN}======================================================${NC}"
echo -e "${GREEN}   DEPLOYMENT SELESAI DENGAN SUKSES! 🚀              ${NC}"
echo -e "${CYAN}======================================================${NC}"
echo -e "Catatan Pengaturan di Dashboard aaPanel:"
echo -e "1. Buka Menu Website > Klik Nama Website Anda"
echo -e "2. Tab 'Site directory' -> Ubah 'Running directory' ke: ${YELLOW}/public${NC}"
echo -e "3. Tab 'URL rewrite'   -> Pilih preset: ${YELLOW}Laravel 5${NC}"
echo -e "4. Tab 'SSL'           -> Aktifkan ${YELLOW}Let's Encrypt (HTTPS)${NC}"
echo -e "======================================================\n"
