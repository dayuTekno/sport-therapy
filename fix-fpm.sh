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

# 2. Nonaktifkan PrivateTmp systemd
echo "2. Mematikan isolasi PrivateTmp systemd..."
OVERRIDE_DIR="/etc/systemd/system/php-fpm.service.d"
OVERRIDE_FILE="$OVERRIDE_DIR/override.conf"
sudo mkdir -p "$OVERRIDE_DIR"
cat << 'EOF' | sudo tee "$OVERRIDE_FILE" > /dev/null
[Service]
PrivateTmp=false
EOF

# 3. Konfigurasi listen socket
echo "3. Menyesuaikan listen socket /tmp/php-cgi-83.sock..."
if [ -f "/etc/php-fpm.d/www.conf" ]; then
    sudo sed -i 's|^listen = .*|listen = /tmp/php-cgi-83.sock|' /etc/php-fpm.d/www.conf
    sudo sed -i 's|^;*listen.owner = .*|listen.owner = www|' /etc/php-fpm.d/www.conf
    sudo sed -i 's|^;*listen.group = .*|listen.group = www|' /etc/php-fpm.d/www.conf
    sudo sed -i 's|^;*listen.mode = .*|listen.mode = 0666|' /etc/php-fpm.d/www.conf
    sudo sed -i 's|^user = .*|user = www|' /etc/php-fpm.d/www.conf
    sudo sed -i 's|^group = .*|group = www|' /etc/php-fpm.d/www.conf
fi

sudo systemctl daemon-reload
sudo systemctl enable php-fpm 2>/dev/null || true
sudo systemctl restart php-fpm

if [ -e "/run/php-fpm/www.sock" ] && [ ! -e "/tmp/php-cgi-83.sock" ]; then
    sudo ln -sf /run/php-fpm/www.sock /tmp/php-cgi-83.sock
    sudo chmod 777 /run/php-fpm/www.sock 2>/dev/null || true
fi

if [ -f "/etc/init.d/php-fpm-83" ]; then
    sudo /etc/init.d/php-fpm-83 restart 2>/dev/null || true
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

# 5. Reload Nginx
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
