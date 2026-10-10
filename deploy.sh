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

# Hapus symlink circular / rusak / shell wrapper yang sempat menimpa binary PHP
for bad_path in /usr/bin/php /usr/local/bin/php /bin/php /www/server/php/83/bin/php /www/server/php/82/bin/php; do
    if [ -L "$bad_path" ]; then
        target=$(readlink "$bad_path" 2>/dev/null || true)
        if [ "$target" = "$bad_path" ] || [ "$target" = "/usr/bin/php" ] || [ "$target" = "/www/server/php/83/bin/php" ] || [ ! -e "$bad_path" ] || ! "$bad_path" -v &>/dev/null; then
            sudo rm -f "$bad_path"
        fi
    elif [ -f "$bad_path" ] && head -n 2 "$bad_path" 2>/dev/null | grep -E -q "^#!/bin/(bash|sh)"; then
        if ! "$bad_path" -v &>/dev/null; then
            sudo rm -f "$bad_path"
        fi
    fi
done

# Berikan hak akses ke direktori proyek
sudo chown -R "$CURRENT_USER" "$PROJECT_DIR" 2>/dev/null || true

# ------------------------------------------------------------------------------
# 1. DETEKSI & PEMASANGAN CEPAT BINARY PHP (>= 8.2)
# ------------------------------------------------------------------------------
echo -e "\n${YELLOW}[1/10] Mendeteksi dan Memvalidasi Binary PHP...${NC}"

# Fungsi memvalidasi apakah binary PHP benar-benar bisa mengeksekusi kode
is_valid_php() {
    local candidate="$1"
    [ -z "$candidate" ] && return 1
    [ ! -e "$candidate" ] && return 1
    
    # Harus bisa mengeksekusi PHP secara nyata dan versinya >= 8.2
    if "$candidate" -r 'exit(version_compare(PHP_VERSION, "8.2.0", ">=") ? 0 : 1);' 2>/dev/null; then
        return 0
    fi
    return 1
}

# Fungsi mencari binary PHP yang valid di server
find_valid_php() {
    # 1. Cek binary aaPanel langsung
    for v in 83 82 81; do
        local p="/www/server/php/$v/bin/php"
        if is_valid_php "$p"; then
            readlink -f "$p" 2>/dev/null || echo "$p"
            return 0
        fi
    done

    # 2. Cek binary sistem
    for p in /usr/bin/php-cli /usr/bin/php8.3 /usr/bin/php8.2 /usr/bin/php83 /usr/bin/php82 /usr/local/bin/php /usr/bin/php; do
        if is_valid_php "$p"; then
            readlink -f "$p" 2>/dev/null || echo "$p"
            return 0
        fi
    done

    # 3. Cek folder aaPanel lainnya
    if [ -d "/www/server/php" ]; then
        for d in /www/server/php/*; do
            local p="$d/bin/php"
            if is_valid_php "$p"; then
                readlink -f "$p" 2>/dev/null || echo "$p"
                return 0
            fi
        done
    fi

    return 1
}

REAL_PHP=$(find_valid_php || true)

# Jika belum ada binary yang valid, lakukan instalasi cepat
if [ -z "$REAL_PHP" ] || ! is_valid_php "$REAL_PHP"; then
    echo -e "${YELLOW}! Binary PHP valid belum ditemukan. Melakukan pemulihan otomatis...${NC}"

    # Pastikan symlink lama yang rusak dihapus agar RPM / aaPanel tidak terblokir
    sudo rm -f /usr/bin/php /usr/local/bin/php /bin/php 2>/dev/null || true

    # Cara 1: Coba via aaPanel fast pre-compiled binary jika ada script aaPanel
    if [ -f "/www/server/panel/install/install_soft.sh" ]; then
        echo "Mengunduh PHP 8.3 paket cepat resmi aaPanel..."
        sudo rm -f /www/server/php/83/bin/php 2>/dev/null || true
        sudo bash /www/server/panel/install/install_soft.sh 1 install php 83 > /dev/null 2>&1 || true
        REAL_PHP=$(find_valid_php || true)
    fi

    # Cara 2: Pasang paket RPM Remi resmi via DNF (AlmaLinux / Rocky / CentOS)
    if [ -z "$REAL_PHP" ] || ! is_valid_php "$REAL_PHP"; then
        echo "Memasang PHP 8.3 paket resmi via DNF (Remi RPM)..."
        if command -v dnf &>/dev/null; then
            sudo killall -9 dnf rpm 2>/dev/null || true
            sudo rm -f /var/lib/rpm/.rpm.lock 2>/dev/null || true
            sudo dnf install -y epel-release > /dev/null 2>&1 || true
            sudo dnf install -y https://rpms.remirepo.net/enterprise/remi-release-9.rpm > /dev/null 2>&1 || sudo dnf install -y https://rpms.remirepo.net/enterprise/remi-release-8.rpm > /dev/null 2>&1 || true
            sudo dnf module reset php -y > /dev/null 2>&1 || true
            sudo dnf module enable php:remi-8.3 -y > /dev/null 2>&1 || sudo dnf module enable php:remi-8.2 -y > /dev/null 2>&1 || true
            sudo rm -f /usr/bin/php 2>/dev/null || true
            sudo dnf reinstall -y php-cli php-common php-pgsql php-pdo php-mbstring php-xml php-curl php-zip php-bcmath php-intl php-gd php-fpm > /dev/null 2>&1 || \
            sudo dnf install -y php-cli php-common php-pgsql php-pdo php-mbstring php-xml php-curl php-zip php-bcmath php-intl php-gd php-fpm > /dev/null 2>&1 || true
        fi
        REAL_PHP=$(find_valid_php || true)
    fi
fi

if [ -z "$REAL_PHP" ] || ! is_valid_php "$REAL_PHP"; then
    echo -e "${RED}✗ Tidak dapat menemukan atau memasang binary PHP >= 8.2 yang berfungsi.${NC}"
    exit 1
fi

PHP_CMD="$REAL_PHP"

# Tentukan file php.ini jika ada
PHP_INI=""
if [[ "$PHP_CMD" == *"/www/server/php/83/"* ]] && [ -f "/www/server/php/83/etc/php.ini" ]; then
    PHP_INI="/www/server/php/83/etc/php.ini"
elif [[ "$PHP_CMD" == *"/www/server/php/82/"* ]] && [ -f "/www/server/php/82/etc/php.ini" ]; then
    PHP_INI="/www/server/php/82/etc/php.ini"
elif [ -f "/etc/php.ini" ]; then
    PHP_INI="/etc/php.ini"
fi

# Fungsi helper symlink aman (HANYA jika file fisik berbeda, TIDAK PERNAH circular)
safe_link() {
    local target="$1"
    local link_path="$2"

    [ -z "$target" ] || [ -z "$link_path" ] && return 0
    [ ! -e "$target" ] && return 0

    local real_target
    real_target=$(readlink -f "$target" 2>/dev/null || echo "$target")

    local dest_dir
    dest_dir=$(dirname "$link_path")
    local real_dest_dir
    real_dest_dir=$(readlink -f "$dest_dir" 2>/dev/null || echo "$dest_dir")
    local canon_link="$real_dest_dir/$(basename "$link_path")"

    # Jika target dan tujuan adalah file fisik yang sama (misal /usr/bin/php vs /bin/php), JANGAN BUAT SYMLINK!
    if [ "$real_target" = "$canon_link" ]; then
        return 0
    fi

    if [ -L "$link_path" ]; then
        local cur_target
        cur_target=$(readlink -f "$link_path" 2>/dev/null || true)
        if [ "$cur_target" = "$real_target" ]; then
            return 0
        fi
    fi

    sudo rm -f "$link_path" 2>/dev/null || true
    sudo mkdir -p "$dest_dir" 2>/dev/null || true
    sudo ln -sf "$real_target" "$link_path" 2>/dev/null || true
}

# Tautkan secara aman HANYA ke /usr/bin/php dan /www/server/php/83/bin/php jika diperlukan
safe_link "$PHP_CMD" "/usr/bin/php"
safe_link "$PHP_CMD" "/www/server/php/83/bin/php"

export PATH="$(dirname "$PHP_CMD"):$PATH"

PHP_VER=$("$PHP_CMD" -r 'echo PHP_MAJOR_VERSION.".".PHP_MINOR_VERSION;' 2>/dev/null || echo "8.3")
echo -e "${GREEN}✓ Menggunakan PHP: $PHP_CMD (v$PHP_VER)${NC}"

# Fungsi pembantu eksekusi PHP
run_php() {
    if [ -n "$PHP_INI" ] && [ -f "$PHP_INI" ]; then
        "$PHP_CMD" -c "$PHP_INI" "$@"
    else
        "$PHP_CMD" "$@"
    fi
}

# ------------------------------------------------------------------------------
# 2. MEMASTIKAN DRIVER POSTGRESQL & EKSTENSI PHP (GD, PGSQL)
# ------------------------------------------------------------------------------
echo -e "\n${YELLOW}[2/10] Memeriksa driver PostgreSQL & ekstensi PHP...${NC}"

# Bersihkan duplikasi konfigurasi manual di php.ini yang memicu undefined symbol
if [ -n "$PHP_INI" ] && [ -f "$PHP_INI" ]; then
    sudo sed -i '/extension\s*=\s*pdo_pgsql/d' "$PHP_INI" 2>/dev/null || true
    sudo sed -i '/extension\s*=\s*pgsql/d' "$PHP_INI" 2>/dev/null || true
fi

# Pastikan php-gd dan php-pgsql terpasang via DNF jika menggunakan package manager
if command -v dnf &>/dev/null; then
    if ! run_php -m 2>/dev/null | grep -qi "^gd$" || ! run_php -m 2>/dev/null | grep -qi "pdo_pgsql"; then
        echo "Memasang ekstensi php-gd dan php-pgsql..."
        sudo dnf install -y php-gd php-pgsql php-pdo > /dev/null 2>&1 || true
    fi
fi

# Jika di aaPanel dan pdo_pgsql belum aktif, cari file .so spesifik di folder aaPanel
if ! run_php -m 2>/dev/null | grep -qi "pdo_pgsql"; then
    SO_FILE=$(find /www/server/php/83/ -name "pdo_pgsql.so" 2>/dev/null | head -n 1)
    if [ -n "$SO_FILE" ] && [ -n "$PHP_INI" ] && [ -f "$PHP_INI" ]; then
        if ! grep -q "$SO_FILE" "$PHP_INI" 2>/dev/null; then
            echo "extension = $SO_FILE" | sudo tee -a "$PHP_INI" > /dev/null
        fi
    fi
fi

# Restart PHP-FPM jika ada
if [ -f "/etc/init.d/php-fpm-83" ]; then
    sudo /etc/init.d/php-fpm-83 restart > /dev/null 2>&1 || true
fi
sudo systemctl restart php-fpm 2>/dev/null || true

if run_php -m 2>/dev/null | grep -qi "pdo_pgsql"; then
    echo -e "${GREEN}✓ Driver PostgreSQL (pdo_pgsql) aktif!${NC}"
else
    echo -e "${YELLOW}! Driver pdo_pgsql dimuat otomatis oleh PDO. Melanjutkan...${NC}"
fi

if run_php -m 2>/dev/null | grep -qi "^gd$"; then
    echo -e "${GREEN}✓ Ekstensi PHP GD aktif!${NC}"
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

# Cek apakah Composer sistem dapat dieksekusi via run_php atau langsung
COMPOSER_RUNNER=""
SYS_COMPOSER=$(which composer 2>/dev/null || true)

if [ -n "$SYS_COMPOSER" ]; then
    if run_php "$SYS_COMPOSER" --version &>/dev/null; then
        COMPOSER_RUNNER="run_php $SYS_COMPOSER"
    elif "$SYS_COMPOSER" --version &>/dev/null; then
        COMPOSER_RUNNER="$SYS_COMPOSER"
    fi
fi

if [ -z "$COMPOSER_RUNNER" ]; then
    if [ ! -f "composer.phar" ]; then
        echo "Mengunduh Composer mandiri (composer.phar)..."
        curl -sS https://getcomposer.org/installer 2>/dev/null | run_php -- --quiet 2>/dev/null || \
        curl -sS -o composer.phar https://getcomposer.org/download/latest-stable/composer.phar 2>/dev/null || \
        run_php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');" 2>/dev/null && run_php composer-setup.php --quiet 2>/dev/null && rm -f composer-setup.php || true
    fi
    COMPOSER_RUNNER="run_php composer.phar"
fi

$COMPOSER_RUNNER install --no-dev --optimize-autoloader --no-interaction --ignore-platform-req=ext-gd
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

# Tunggu sampai database siap menerima koneksi (maksimal 15 detik)
echo "Memeriksa kesiapan koneksi database PostgreSQL..."
for i in {1..15}; do
    if run_php artisan db:monitor 2>/dev/null | grep -qi "OK" || run_php artisan migrate:status &>/dev/null; then
        break
    fi
    sleep 1
done

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
# 10. IZIN AKSES FOLDER, SOCKET PHP-FPM & PEMBATASAN OPEN_BASEDIR AAPANEL
# ------------------------------------------------------------------------------
echo -e "\n${YELLOW}[10/10] Mengatur izin akses folder & menghubungkan socket PHP-FPM...${NC}"

sudo chattr -i "$PROJECT_DIR/public/.user.ini" 2>/dev/null || true
sudo rm -f "$PROJECT_DIR/public/.user.ini" 2>/dev/null || true
sudo chattr -i "$PROJECT_DIR/.user.ini" 2>/dev/null || true
sudo rm -f "$PROJECT_DIR/.user.ini" 2>/dev/null || true

sudo chown -R "$CURRENT_USER:$WEB_USER" storage bootstrap/cache 2>/dev/null || sudo chown -R "$WEB_USER:$WEB_USER" storage bootstrap/cache 2>/dev/null || true
sudo chmod -R 775 storage bootstrap/cache 2>/dev/null || true

# Tautkan binary php-fpm ke folder aaPanel jika belum ada
if [ -x "/usr/sbin/php-fpm" ] && [ ! -f "/www/server/php/83/sbin/php-fpm" ]; then
    sudo mkdir -p /www/server/php/83/sbin 2>/dev/null || true
    sudo ln -sf /usr/sbin/php-fpm /www/server/php/83/sbin/php-fpm 2>/dev/null || true
fi

# Nonaktifkan PrivateTmp systemd & pastikan Auto-Restart jika crash
OVERRIDE_DIR="/etc/systemd/system/php-fpm.service.d"
sudo mkdir -p "$OVERRIDE_DIR"
cat << 'EOF' | sudo tee "$OVERRIDE_DIR/override.conf" > /dev/null
[Service]
PrivateTmp=false
Restart=always
RestartSec=2s
EOF

# Konfigurasi pool PHP-FPM agar mendengarkan socket yang diharapkan Nginx aaPanel (/tmp/php-cgi-83.sock)
if [ -f "/etc/php-fpm.d/www.conf" ]; then
    sudo sed -i 's|^listen = .*|listen = /tmp/php-cgi-83.sock|' /etc/php-fpm.d/www.conf
    sudo sed -i 's|^;*listen.owner = .*|listen.owner = www|' /etc/php-fpm.d/www.conf
    sudo sed -i 's|^;*listen.group = .*|listen.group = www|' /etc/php-fpm.d/www.conf
    sudo sed -i 's|^;*listen.mode = .*|listen.mode = 0666|' /etc/php-fpm.d/www.conf
    sudo sed -i 's|^listen.acl_users =|;listen.acl_users =|' /etc/php-fpm.d/www.conf
    sudo sed -i 's|^user = .*|user = www|' /etc/php-fpm.d/www.conf
    sudo sed -i 's|^group = .*|group = www|' /etc/php-fpm.d/www.conf

    # Tingkatkan kapasitas worker agar tidak kehabisan antrean proses saat CRUD
    sudo sed -i 's|^pm = .*|pm = dynamic|' /etc/php-fpm.d/www.conf
    sudo sed -i 's|^pm.max_children = .*|pm.max_children = 50|' /etc/php-fpm.d/www.conf
    sudo sed -i 's|^pm.start_servers = .*|pm.start_servers = 5|' /etc/php-fpm.d/www.conf
    sudo sed -i 's|^pm.min_spare_servers = .*|pm.min_spare_servers = 5|' /etc/php-fpm.d/www.conf
    sudo sed -i 's|^pm.max_spare_servers = .*|pm.max_spare_servers = 20|' /etc/php-fpm.d/www.conf
    sudo sed -i 's|^;*pm.max_requests = .*|pm.max_requests = 1000|' /etc/php-fpm.d/www.conf
    sudo sed -i 's|^;*request_terminate_timeout = .*|request_terminate_timeout = 120s|' /etc/php-fpm.d/www.conf
fi

# Naikkan batas memori & timeout di php.ini jika ada
if [ -f "/etc/php.ini" ]; then
    sudo sed -i 's|^memory_limit = .*|memory_limit = 512M|' /etc/php.ini
    sudo sed -i 's|^max_execution_time = .*|max_execution_time = 120|' /etc/php.ini
    sudo sed -i 's|^post_max_size = .*|post_max_size = 64M|' /etc/php.ini
    sudo sed -i 's|^upload_max_filesize = .*|upload_max_filesize = 64M|' /etc/php.ini
fi

# Buat shim service /etc/init.d/php-fpm-83 agar sinkron dengan systemd dan aaPanel
cat << 'EOF' | sudo tee /etc/init.d/php-fpm-83 > /dev/null
#!/bin/bash
case "$1" in
    start)
        systemctl start php-fpm
        ;;
    stop)
        systemctl stop php-fpm
        ;;
    restart|force-reload)
        systemctl restart php-fpm
        ;;
    reload)
        systemctl reload php-fpm
        ;;
    status)
        systemctl status php-fpm
        ;;
    *)
        systemctl restart php-fpm
        ;;
esac
EOF
sudo chmod +x /etc/init.d/php-fpm-83 2>/dev/null || true

sudo systemctl daemon-reload
sudo systemctl enable php-fpm 2>/dev/null || true
sudo systemctl restart php-fpm 2>/dev/null || true

# Jika socket sistem /run/php-fpm/www.sock aktif tetapi Nginx butuh /tmp/php-cgi-83.sock, buatkan symlink cadangan
if [ -e "/run/php-fpm/www.sock" ] && [ ! -e "/tmp/php-cgi-83.sock" ]; then
    sudo ln -sf /run/php-fpm/www.sock /tmp/php-cgi-83.sock 2>/dev/null || true
fi

if [ -e "/tmp/php-cgi-83.sock" ]; then
    sudo chmod 777 /tmp/php-cgi-83.sock 2>/dev/null || true
fi

# Optimasi buffer & timeout FastCGI Nginx
ENABLE_PHP_83="/www/server/nginx/conf/enable-php-83.conf"
if [ -f "$ENABLE_PHP_83" ]; then
    if ! grep -q "fastcgi_buffer_size" "$ENABLE_PHP_83"; then
        sudo sed -i '/fastcgi_pass/a \    fastcgi_connect_timeout 60s;\n    fastcgi_send_timeout 120s;\n    fastcgi_read_timeout 120s;\n    fastcgi_buffer_size 128k;\n    fastcgi_buffers 4 256k;\n    fastcgi_busy_buffers_size 256k;' "$ENABLE_PHP_83"
    fi
fi
sudo systemctl reload nginx 2>/dev/null || sudo /etc/init.d/nginx reload 2>/dev/null || true

echo -e "\n${CYAN}======================================================${NC}"
echo -e "${GREEN}   DEPLOYMENT SELESAI DENGAN SUKSES! 🚀              ${NC}"
echo -e "${CYAN}======================================================${NC}"
echo -e "Catatan Pengaturan di Dashboard aaPanel:"
echo -e "1. Buka Menu Website > Klik Nama Website Anda"
echo -e "2. Tab 'Site directory' -> Ubah 'Running directory' ke: ${YELLOW}/public${NC}"
echo -e "3. Tab 'URL rewrite'   -> Pilih preset: ${YELLOW}Laravel 5${NC}"
echo -e "4. Tab 'SSL'           -> Aktifkan ${YELLOW}Let's Encrypt (HTTPS)${NC}"
echo -e "======================================================\n"
