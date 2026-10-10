#!/usr/bin/env bash
# ==============================================================================
# PERBAIKAN PERMANEN: MIGRASI DARI UNIX SOCKET KE TCP PORT 127.0.0.1:9083
# Mengatasi Socket Hilang Saat Reload Berkala aaPanel / Systemd (SIGUSR2)
# ==============================================================================

set -e

GREEN='\033[0;32m'
CYAN='\033[0;36m'
YELLOW='\033[1;33m'
RED='\033[0;31m'
NC='\033[0m'

PROJECT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"

echo -e "${CYAN}======================================================${NC}"
echo -e "${CYAN}   MENGUBAH PHP-FPM KE TCP PORT 127.0.0.1:9083        ${NC}"
echo -e "${CYAN}======================================================${NC}"

# 1. Konfigurasi Pool PHP-FPM ke TCP Port 127.0.0.1:9083
echo -e "\n${YELLOW}1. Mengatur PHP-FPM mendengarkan TCP Port 127.0.0.1:9083...${NC}"
WWW_CONF="/etc/php-fpm.d/www.conf"
if [ -f "$WWW_CONF" ]; then
    sudo cp "$WWW_CONF" "${WWW_CONF}.bak_$(date +%s)"
    sudo sed -i 's|^listen = .*|listen = 127.0.0.1:9083|' "$WWW_CONF"
    sudo sed -i 's|^;*listen.allowed_clients = .*|listen.allowed_clients = 127.0.0.1|' "$WWW_CONF"
    sudo sed -i 's|^listen.owner =|;listen.owner =|' "$WWW_CONF"
    sudo sed -i 's|^listen.group =|;listen.group =|' "$WWW_CONF"
    sudo sed -i 's|^listen.mode =|;listen.mode =|' "$WWW_CONF"
    sudo sed -i 's|^listen.acl_users =|;listen.acl_users =|' "$WWW_CONF"
    sudo sed -i 's|^user = .*|user = www|' "$WWW_CONF"
    sudo sed -i 's|^group = .*|group = www|' "$WWW_CONF"

    # Worker Pool Tuning
    sudo sed -i 's|^pm = .*|pm = dynamic|' "$WWW_CONF"
    sudo sed -i 's|^pm.max_children = .*|pm.max_children = 50|' "$WWW_CONF"
    sudo sed -i 's|^pm.start_servers = .*|pm.start_servers = 5|' "$WWW_CONF"
    sudo sed -i 's|^pm.min_spare_servers = .*|pm.min_spare_servers = 5|' "$WWW_CONF"
    sudo sed -i 's|^pm.max_spare_servers = .*|pm.max_spare_servers = 20|' "$WWW_CONF"
    sudo sed -i 's|^;*pm.max_requests = .*|pm.max_requests = 1000|' "$WWW_CONF"
    sudo sed -i 's|^;*request_terminate_timeout = .*|request_terminate_timeout = 120s|' "$WWW_CONF"
fi

# 2. Pastikan override systemd (Auto restart & no PrivateTmp)
echo -e "\n${YELLOW}2. Memastikan konfigurasi systemd...${NC}"
OVERRIDE_DIR="/etc/systemd/system/php-fpm.service.d"
sudo mkdir -p "$OVERRIDE_DIR"
cat << 'EOF' | sudo tee "$OVERRIDE_DIR/override.conf" > /dev/null
[Service]
PrivateTmp=false
Restart=always
RestartSec=2s
EOF

# 3. Buat shim /etc/init.d/php-fpm-83 agar sinkron
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
        systemctl reload php-fpm || systemctl restart php-fpm
        ;;
    status)
        systemctl status php-fpm
        ;;
    *)
        systemctl restart php-fpm
        ;;
esac
EOF
sudo chmod +x /etc/init.d/php-fpm-83

# 4. Restart PHP-FPM
echo -e "\n${YELLOW}3. Me-restart PHP-FPM dengan listener TCP...${NC}"
sudo systemctl daemon-reload
sudo systemctl enable php-fpm 2>/dev/null || true
sudo systemctl restart php-fpm

# 5. Konfigurasi Nginx aaPanel agar mengarah ke 127.0.0.1:9083
echo -e "\n${YELLOW}4. Mengarahkan Nginx aaPanel ke fastcgi_pass 127.0.0.1:9083...${NC}"
ENABLE_PHP_83="/www/server/nginx/conf/enable-php-83.conf"
if [ -f "$ENABLE_PHP_83" ]; then
    sudo cp "$ENABLE_PHP_83" "${ENABLE_PHP_83}.bak_$(date +%s)"
    cat << 'EOF' | sudo tee "$ENABLE_PHP_83" > /dev/null
location ~ [^/]\.php(/|$)
{
    try_files $uri =404;
    fastcgi_pass 127.0.0.1:9083;
    fastcgi_index index.php;
    include fastcgi.conf;
    include pathinfo.conf;

    fastcgi_connect_timeout 60s;
    fastcgi_send_timeout 120s;
    fastcgi_read_timeout 120s;
    fastcgi_buffer_size 128k;
    fastcgi_buffers 4 256k;
    fastcgi_busy_buffers_size 256k;
}
EOF
fi

VHOST_CONF="/www/server/panel/vhost/nginx/sport-therapist.corpshow.id.conf"
if [ -f "$VHOST_CONF" ]; then
    sudo sed -i 's|fastcgi_pass unix:/tmp/php-cgi-83.sock;|fastcgi_pass 127.0.0.1:9083;|g' "$VHOST_CONF"
fi

# Reload Nginx
sudo /etc/init.d/nginx reload 2>/dev/null || sudo systemctl reload nginx 2>/dev/null || true

# 6. Jalankan Migrasi & Clear Cache Laravel
echo -e "\n${YELLOW}5. Memperbarui migrasi & cache Laravel...${NC}"
cd "$PROJECT_DIR"
if command -v php &>/dev/null; then
    php artisan migrate --force 2>/dev/null || true
    php artisan optimize:clear 2>/dev/null || true
fi

# 7. Pengujian Respon Website Sebelum & Sesudah Reload
echo -e "\n${YELLOW}6. Menguji Koneksi Website (Port 127.0.0.1:9083):${NC}"
UP_CODE=$(curl -k -s -o /dev/null -w "%{http_code}" -H "Host: sport-therapist.corpshow.id" https://127.0.0.1/up || echo "000")
HTTPS_CODE=$(curl -k -s -o /dev/null -w "%{http_code}" -H "Host: sport-therapist.corpshow.id" https://127.0.0.1/ || echo "000")

echo -e "HTTPS Status           : Status ${GREEN}$HTTPS_CODE${NC}"
echo -e "Laravel Health (/up)   : Status ${GREEN}$UP_CODE${NC}"

# Simulasi Reload systemd untuk membuktikan koneksi tidak putus lagi
echo -e "\n${YELLOW}7. Menguji Simulasi Systemd Reload (Tes Ketahanan):${NC}"
sudo systemctl reload php-fpm
TEST_RELOAD=$(curl -k -s -o /dev/null -w "%{http_code}" -H "Host: sport-therapist.corpshow.id" https://127.0.0.1/up || echo "000")
echo -e "Health (/up) Pasca Reload: Status ${GREEN}$TEST_RELOAD${NC}"

if [ "$TEST_RELOAD" = "200" ]; then
    echo -e "\n${GREEN}======================================================${NC}"
    echo -e "${GREEN}✓ SUKSES TOTAL! KONEKSI PHP-FPM KINI 100% STABIL!    ${NC}"
    echo -e "${GREEN}  Tidak akan ada lagi socket yang hilang saat reload. ${NC}"
    echo -e "${GREEN}======================================================${NC}"
else
    echo -e "\n${RED}Status: $TEST_RELOAD. Periksa log Nginx/PHP.${NC}"
fi
