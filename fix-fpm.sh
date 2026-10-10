#!/usr/bin/env bash
set -e

GREEN='\033[0;32m'
CYAN='\033[0;36m'
YELLOW='\133[1;33m'
YELLOW='\033[1;33m'
RED='\033[0;31m'
NC='\033[0m'

PROJECT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"

echo -e "${CYAN}======================================================${NC}"
echo -e "${CYAN}   DIAGNOSIS & PERBAIKAN WEBSITE AAPANEL              ${NC}"
echo -e "${CYAN}======================================================${NC}"

# 1. Tautkan binary php-fpm ke direktori aaPanel
echo "1. Menyiapkan binary PHP-FPM aaPanel..."
sudo mkdir -p /www/server/php/83/sbin 2>/dev/null || true
if [ -x "/usr/sbin/php-fpm" ]; then
    sudo ln -sf /usr/sbin/php-fpm /www/server/php/83/sbin/php-fpm
fi

# 2. Nonaktifkan PrivateTmp systemd & pastikan Auto-Restart jika crash
echo "2. Mematikan isolasi PrivateTmp & mengaktifkan auto-restart systemd..."
OVERRIDE_DIR="/etc/systemd/system/php-fpm.service.d"
OVERRIDE_FILE="$OVERRIDE_DIR/override.conf"
sudo mkdir -p "$OVERRIDE_DIR"
cat << 'EOF' | sudo tee "$OVERRIDE_FILE" > /dev/null
[Service]
PrivateTmp=false
Restart=always
RestartSec=2s
EOF

# 3. Konfigurasi listen socket & Tuning Pool PHP-FPM
echo "3. Menyesuaikan listen socket /tmp/php-cgi-83.sock & kapasitas pool worker..."
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
sudo systemctl restart php-fpm

if [ -e "/run/php-fpm/www.sock" ] && [ ! -e "/tmp/php-cgi-83.sock" ]; then
    sudo ln -sf /run/php-fpm/www.sock /tmp/php-cgi-83.sock
fi

if [ -e "/tmp/php-cgi-83.sock" ]; then
    sudo chmod 777 /tmp/php-cgi-83.sock
    echo -e "${GREEN}✓ Socket PHP-FPM aktif: /tmp/php-cgi-83.sock${NC}"
else
    echo -e "${RED}✗ Socket /tmp/php-cgi-83.sock belum terbentuk!${NC}"
fi

# 4. Memastikan Root Directory Nginx mengarah ke folder public Laravel
echo "4. Menyesuaikan direktori web dan URL rewrite..."
VHOST_CONF="/www/server/panel/vhost/nginx/sport-therapist.corpshow.id.conf"
REWRITE_CONF="/www/server/panel/vhost/rewrite/sport-therapist.corpshow.id.conf"

# Jika ada folder induk di /www/wwwroot/sport-therapist.corpshow.id, tautkan public-nya
PARENT_DIR="/www/wwwroot/sport-therapist.corpshow.id"
if [ -d "$PARENT_DIR" ] && [ "$PARENT_DIR" != "$PROJECT_DIR" ]; then
    if [ ! -L "$PARENT_DIR/public" ]; then
        if [ -d "$PARENT_DIR/public" ]; then
            sudo mv "$PARENT_DIR/public" "$PARENT_DIR/public.bak_$(date +%s)" 2>/dev/null || true
        fi
        sudo ln -sfn "$PROJECT_DIR/public" "$PARENT_DIR/public" 2>/dev/null || true
    fi
fi

# Pastikan URL rewrite Laravel aktif
if [ -f "$REWRITE_CONF" ]; then
    cat << 'EOF' | sudo tee "$REWRITE_CONF" > /dev/null
location / {
    try_files $uri $uri/ /index.php?$query_string;
}
EOF
fi

# Sinkronkan migrasi database & bersihkan cache Laravel
cd "$PROJECT_DIR"
if command -v php &>/dev/null; then
    php artisan migrate --force 2>/dev/null || true
    php artisan optimize:clear 2>/dev/null || true
elif [ -x "/usr/bin/php" ]; then
    /usr/bin/php artisan migrate --force 2>/dev/null || true
    /usr/bin/php artisan optimize:clear 2>/dev/null || true
fi

# 5. Optimasi FastCGI Nginx & Reload
echo "5. Mengoptimasi buffer & timeout FastCGI serta me-reload Nginx..."
ENABLE_PHP_83="/www/server/nginx/conf/enable-php-83.conf"
if [ -f "$ENABLE_PHP_83" ]; then
    if ! grep -q "fastcgi_buffer_size" "$ENABLE_PHP_83"; then
        sudo sed -i '/fastcgi_pass/a \    fastcgi_connect_timeout 60s;\n    fastcgi_send_timeout 120s;\n    fastcgi_read_timeout 120s;\n    fastcgi_buffer_size 128k;\n    fastcgi_buffers 4 256k;\n    fastcgi_busy_buffers_size 256k;' "$ENABLE_PHP_83"
    fi
fi
sudo /etc/init.d/nginx reload 2>/dev/null || sudo systemctl reload nginx 2>/dev/null || true

# 6. Uji Respon Server
echo -e "\n5. Menguji Respon Website:"
HTTP_CODE=$(curl -s -o /dev/null -w "%{http_code}" -H "Host: sport-therapist.corpshow.id" http://127.0.0.1/ || echo "000")
HTTPS_CODE=$(curl -k -s -o /dev/null -w "%{http_code}" -H "Host: sport-therapist.corpshow.id" https://127.0.0.1/ || echo "000")
UP_CODE=$(curl -k -s -o /dev/null -w "%{http_code}" -H "Host: sport-therapist.corpshow.id" https://127.0.0.1/up || echo "000")

echo -e "HTTP  (Port 80) : Status ${GREEN}$HTTP_CODE${NC}"
echo -e "HTTPS (Port 443): Status ${GREEN}$HTTPS_CODE${NC}"
echo -e "Laravel Health (/up): Status ${GREEN}$UP_CODE${NC}"

echo -e "\n--- Log Error Nginx Terkini ---"
sudo tail -n 8 /www/wwwlogs/sport-therapist.corpshow.id.error.log 2>/dev/null || true
echo "--------------------------------"
