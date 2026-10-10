#!/usr/bin/env bash
set -e

GREEN='\033[0;32m'
YELLOW='\033[1;33m'
RED='\033[0;31m'
NC='\033[0m'

echo -e "${YELLOW}=== MEMPERBAIKI KONEKSI PHP-FPM DAN NGINX ===${NC}"

# 1. Tautkan binary php-fpm ke direktori aaPanel
echo "1. Memeriksa binary PHP-FPM..."
sudo mkdir -p /www/server/php/83/sbin 2>/dev/null || true
if [ -x "/usr/sbin/php-fpm" ]; then
    sudo ln -sf /usr/sbin/php-fpm /www/server/php/83/sbin/php-fpm
fi

# 2. Nonaktifkan PrivateTmp systemd agar socket /tmp terlihat global
echo "2. Mengatur konfigurasi systemd (menonaktifkan PrivateTmp)..."
OVERRIDE_DIR="/etc/systemd/system/php-fpm.service.d"
OVERRIDE_FILE="$OVERRIDE_DIR/override.conf"
sudo mkdir -p "$OVERRIDE_DIR"
cat << 'EOF' | sudo tee "$OVERRIDE_FILE" > /dev/null
[Service]
PrivateTmp=false
EOF

# 3. Konfigurasi /etc/php-fpm.d/www.conf agar listen ke /tmp/php-cgi-83.sock
echo "3. Menyesuaikan socket listen di /etc/php-fpm.d/www.conf..."
if [ -f "/etc/php-fpm.d/www.conf" ]; then
    sudo sed -i 's|^listen = .*|listen = /tmp/php-cgi-83.sock|' /etc/php-fpm.d/www.conf
    sudo sed -i 's|^;*listen.owner = .*|listen.owner = www|' /etc/php-fpm.d/www.conf
    sudo sed -i 's|^;*listen.group = .*|listen.group = www|' /etc/php-fpm.d/www.conf
    sudo sed -i 's|^;*listen.mode = .*|listen.mode = 0666|' /etc/php-fpm.d/www.conf
    sudo sed -i 's|^user = .*|user = www|' /etc/php-fpm.d/www.conf
    sudo sed -i 's|^group = .*|group = www|' /etc/php-fpm.d/www.conf
fi

# 4. Restart service PHP-FPM
echo "4. Memuat ulang daemon & me-restart PHP-FPM..."
sudo systemctl daemon-reload
sudo systemctl enable php-fpm 2>/dev/null || true
sudo systemctl restart php-fpm

# Jika ada socket default di /run, buat symlink cadangan
if [ -e "/run/php-fpm/www.sock" ] && [ ! -e "/tmp/php-cgi-83.sock" ]; then
    sudo ln -sf /run/php-fpm/www.sock /tmp/php-cgi-83.sock
    sudo chmod 777 /run/php-fpm/www.sock 2>/dev/null || true
fi

# Coba juga service aaPanel jika ada script init
if [ -f "/etc/init.d/php-fpm-83" ]; then
    sudo /etc/init.d/php-fpm-83 restart 2>/dev/null || true
fi

# Berikan izin ke socket jika sudah terbentuk
if [ -e "/tmp/php-cgi-83.sock" ]; then
    sudo chmod 777 /tmp/php-cgi-83.sock
    echo -e "${GREEN}✓ Socket /tmp/php-cgi-83.sock BERHASIL aktif!${NC}"
    ls -la /tmp/php-cgi-83.sock
else
    echo -e "${RED}✗ Socket /tmp/php-cgi-83.sock belum ditemukan. Memeriksa socket yang ada:${NC}"
    sudo ss -xlpn | grep php || true
fi

# 5. Reload Nginx
echo "5. Me-reload Nginx..."
sudo /etc/init.d/nginx reload 2>/dev/null || sudo systemctl reload nginx 2>/dev/null || true

# 6. Test koneksi lokal
echo "6. Menguji respon website lokal:"
HTTP_STATUS=$(curl -s -o /dev/null -w "%{http_code}" -H "Host: sport-therapist.corpshow.id" http://127.0.0.1/ || echo "000")
echo -e "HTTP Status Respon: ${GREEN}$HTTP_STATUS${NC}"

if [ "$HTTP_STATUS" = "200" ] || [ "$HTTP_STATUS" = "302" ]; then
    echo -e "\n${GREEN}======================================================${NC}"
    echo -e "${GREEN}   BERHASIL! Website siap diakses di browser! 🚀     ${NC}"
    echo -e "${GREEN}======================================================${NC}"
else
    echo -e "\n${YELLOW}Catatan: Jika masih $HTTP_STATUS, silakan periksa status log di atas.${NC}"
fi
