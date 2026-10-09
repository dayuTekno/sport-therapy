#!/usr/bin/env bash
# ==============================================================================
# Script Otomatis Deploy Website Klinik Sport Therapist
# Kompatibel: AlmaLinux / Rocky / CentOS / Ubuntu (aaPanel Environment)
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

# ------------------------------------------------------------------------------
# 1. DETEKSI BINARY PHP & PHP.INI
# ------------------------------------------------------------------------------
echo -e "\n${YELLOW}[1/9] Mendeteksi PHP dan Konfigurasi...${NC}"

if [ -f "/www/server/php/83/bin/php" ]; then
    PHP_CMD="/www/server/php/83/bin/php"
    PHP_INI="/www/server/php/83/etc/php.ini"
    PHP_VER="8.3"
elif [ -f "/www/server/php/82/bin/php" ]; then
    PHP_CMD="/www/server/php/82/bin"
    PHP_INI="/www/server/php/82/etc/php.ini"
    PHP_VER="8.2"
else
    PHP_CMD=$(which php)
    PHP_INI=""
    PHP_VER=$($PHP_CMD -r 'echo PHP_MAJOR_VERSION.".".PHP_MINOR_VERSION;')
fi

echo -e "${GREEN}✓ Menggunakan PHP: $PHP_CMD (v$PHP_VER)${NC}"

# Fungsi pembantu untuk menjalankan PHP dengan php.ini aaPanel
run_php() {
    if [ -n "$PHP_INI" ] && [ -f "$PHP_INI" ]; then
        "$PHP_CMD" -c "$PHP_INI" "$@"
    else
        "$PHP_CMD" "$@"
    fi
}

# ------------------------------------------------------------------------------
# 2. MEMASTIKAN DEPENDENSI SISTEM & DRIVER POSTGRESQL (PDO_PGSQL)
# ------------------------------------------------------------------------------
echo -e "\n${YELLOW}[2/9] Memeriksa driver PostgreSQL di sistem & PHP...${NC}"

# Install library client libpq di AlmaLinux jika belum ada
if command -v dnf &>/dev/null; then
    if ! rpm -q libpq-devel &>/dev/null; then
        echo "Menginstall libpq & libpq-devel untuk AlmaLinux..."
        sudo dnf install -y libpq libpq-devel postgresql-libs || true
        sudo ldconfig
    fi
fi

# Cek apakah pdo_pgsql terbaca di CLI
if ! run_php -r 'exit(extension_loaded("pdo_pgsql") ? 0 : 1);'; then
    echo -e "${YELLOW}! Driver pdo_pgsql belum aktif di PHP CLI. Mengaktifkan otomatis...${NC}"
    
    # Cari letak file pdo_pgsql.so di folder ekstensi PHP
    SO_FILE=$(find /www/server/php/83/lib/php/extensions/ -name "pdo_pgsql.so" 2>/dev/null | head -n 1)
    if [ -n "$SO_FILE" ] && [ -n "$PHP_INI" ]; then
        if ! grep -q "$SO_FILE" "$PHP_INI" 2>/dev/null; then
            echo "extension = $SO_FILE" | sudo tee -a "$PHP_INI" > /dev/null
        fi
        echo "extension = pgsql.so" | sudo tee -a "$PHP_INI" > /dev/null
    fi
    
    # Restart PHP-FPM
    if [ -f "/etc/init.d/php-fpm-83" ]; then
        sudo /etc/init.d/php-fpm-83 restart > /dev/null 2>&1 || true
    fi
    
    # Jika masih belum terbaca, install melalui script aaPanel
    if ! run_php -r 'exit(extension_loaded("pdo_pgsql") ? 0 : 1);'; then
        if [ -f "/www/server/panel/install/install_soft.sh" ]; then
            echo "Menjalankan instalasi ekstensi pgsql bawaan aaPanel..."
            sudo bash /www/server/panel/install/install_soft.sh 0 install pgsql 83
            sudo /etc/init.d/php-fpm-83 restart > /dev/null 2>&1 || true
        fi
    fi
fi

if run_php -r 'exit(extension_loaded("pdo_pgsql") ? 0 : 1);'; then
    echo -e "${GREEN}✓ Driver PostgreSQL (pdo_pgsql) aktif!${NC}"
else
    echo -e "${RED}✗ Driver pdo_pgsql belum berhasil dimuat. Silakan periksa instalasi pgsql di aaPanel.${NC}"
    exit 1
fi

# ------------------------------------------------------------------------------
# 3. MEMASTIKAN CONTAINER DOCKER POSTGRESQL KLINIK BERJALAN
# ------------------------------------------------------------------------------
echo -e "\n${YELLOW}[3/9] Memeriksa status container Docker PostgreSQL...${NC}"

if [ -f "docker-compose.db.yml" ]; then
    if command -v docker &>/dev/null; then
        echo "Memastikan container database berjalan di port 5433..."
        docker compose -f docker-compose.db.yml up -d
        
        # Tunggu beberapa detik agar Postgres siap menerima koneksi
        echo "Menunggu database siap..."
        sleep 4
        echo -e "${GREEN}✓ Container PostgreSQL aktif dan terisolasi di port 5433.${NC}"
    else
        echo -e "${RED}✗ Docker tidak terpasang di server.${NC}"
        exit 1
    fi
else
    echo -e "${YELLOW}! File docker-compose.db.yml tidak ditemukan, melewati tahap ini.${NC}"
fi

# ------------------------------------------------------------------------------
# 4. KONFIGURASI FILE .ENV
# ------------------------------------------------------------------------------
echo -e "\n${YELLOW}[4/9] Memeriksa file konfigurasi environment (.env)...${NC}"

if [ ! -f ".env" ]; then
    echo "Membuat file .env dari .env.example..."
    cp .env.example .env
fi

# Pastikan port DB adalah 5433 dan host 127.0.0.1
sed -i 's/^DB_CONNECTION=.*/DB_CONNECTION=pgsql/' .env
sed -i 's/^DB_HOST=.*/DB_HOST=127.0.0.1/' .env
sed -i 's/^DB_PORT=.*/DB_PORT=5433/' .env
sed -i 's/^APP_ENV=.*/APP_ENV=production/' .env
sed -i 's/^APP_DEBUG=.*/APP_DEBUG=false/' .env

echo -e "${GREEN}✓ File .env dikonfigurasi (Port: 5433, Host: 127.0.0.1).${NC}"

# ------------------------------------------------------------------------------
# 5. INSTALL DEPENDENSI COMPOSER & APP KEY
# ------------------------------------------------------------------------------
echo -e "\n${YELLOW}[5/9] Memasang dependensi Composer...${NC}"

COMPOSER_CMD=$(which composer || true)
if [ -z "$COMPOSER_CMD" ]; then
    echo "Mengunduh Composer lokal..."
    run_php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"
    run_php composer-setup.php --quiet
    rm -f composer-setup.php
    COMPOSER_CMD="$PHP_CMD composer.phar"
fi

$COMPOSER_CMD install --no-dev --optimize-autoloader --no-interaction
echo -e "${GREEN}✓ Dependensi Composer selesai dipasang.${NC}"

# Generate APP_KEY jika belum ada
if ! grep -q "APP_KEY=base64:" .env; then
    echo "Membuat Application Key..."
    run_php artisan key:generate --force
    echo -e "${GREEN}✓ Application Key berhasil dibuat.${NC}"
fi

# ------------------------------------------------------------------------------
# 6. MIGRASI & SEEDER DATABASE
# ------------------------------------------------------------------------------
echo -e "\n${YELLOW}[6/9] Menjalankan migrasi & data awal (Seeder)...${NC}"

run_php artisan migrate --force
run_php artisan db:seed --force
echo -e "${GREEN}✓ Migrasi dan database seeder berhasil dieksekusi.${NC}"

# ------------------------------------------------------------------------------
# 7. PERSIAPAN ASET & STORAGE LINK
# ------------------------------------------------------------------------------
echo -e "\n${YELLOW}[7/9] Menyiapkan storage symlink dan aset frontend...${NC}"

# Bersihkan file hot (mode dev) agar Vite tidak mencoba akses localhost:5173
rm -f public/hot

# Recreate symlink storage yang bersih
rm -rf public/storage
run_php artisan storage:link

echo -e "${GREEN}✓ Storage link berhasil diperbarui.${NC}"

# ------------------------------------------------------------------------------
# 8. OPTIMASI CACHE PRODUCTION
# ------------------------------------------------------------------------------
echo -e "\n${YELLOW}[8/9] Mengoptimasi cache Laravel untuk Production...${NC}"

run_php artisan config:clear
run_php artisan route:clear
run_php artisan view:clear

run_php artisan config:cache
run_php artisan route:cache
run_php artisan view:cache

echo -e "${GREEN}✓ Cache konfigurasi, rute, dan view berhasil di-cache.${NC}"

# ------------------------------------------------------------------------------
# 9. PENGATURAN HAK AKSES FOLDER (PERMISSIONS)
# ------------------------------------------------------------------------------
echo -e "\n${YELLOW}[9/9] Mengatur izin akses folder (storage & cache)...${NC}"

CURRENT_USER=$(whoami)
WEB_USER="www"

# Berikan izin ke storage dan bootstrap/cache
sudo chown -R "$CURRENT_USER:$WEB_USER" storage bootstrap/cache 2>/dev/null || sudo chown -R "$WEB_USER:$WEB_USER" storage bootstrap/cache
sudo chmod -R 775 storage bootstrap/cache

echo -e "${GREEN}✓ Izin folder berhasil diatur.${NC}"

# ------------------------------------------------------------------------------
# SELESAI
# ------------------------------------------------------------------------------
echo -e "\n${CYAN}======================================================${NC}"
echo -e "${GREEN}   DEPLOYMENT SELESAI DENGAN SUKSES! 🚀              ${NC}"
echo -e "${CYAN}======================================================${NC}"
echo -e "Catatan Pengaturan di Dashboard aaPanel:"
echo -e "1. Buka Menu Website > Klik Nama Website Anda"
echo -e "2. Tab 'Site directory' -> Ubah 'Running directory' ke: ${YELLOW}/public${NC}"
echo -e "3. Tab 'URL rewrite'   -> Pilih preset: ${YELLOW}Laravel 5${NC}"
echo -e "4. Tab 'SSL'           -> Aktifkan ${YELLOW}Let's Encrypt (HTTPS)${NC}"
echo -e "======================================================\n"
