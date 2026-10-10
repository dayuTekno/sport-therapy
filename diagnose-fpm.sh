#!/usr/bin/env bash
# ==============================================================================
# Script Diagnosis Mendalam Penyebab PHP-FPM Mati Setiap 10 Menit
# ==============================================================================

CYAN='\033[0;36m'
YELLOW='\033[1;33m'
GREEN='\033[0;32m'
RED='\033[0;31m'
NC='\033[0m'

echo -e "${CYAN}======================================================${NC}"
echo -e "${CYAN}   DIAGNOSIS SISTEM & PENYEBAB 502 BAD GATEWAY        ${NC}"
echo -e "${CYAN}======================================================${NC}"

# 1. Cek Penggunaan Memori & Swap (OOM Killer Check)
echo -e "\n${YELLOW}[1] PENGGUNAAN MEMORI (RAM & SWAP):${NC}"
free -m
echo ""
sudo dmesg -T 2>/dev/null | grep -i -E "oom|killed process|php-fpm" | tail -n 10 || true

# 2. Cek Log Systemd PHP-FPM (Apa yang terjadi saat menit 11:00, 11:10, 11:20)
echo -e "\n${YELLOW}[2] LOG SYSTEMD PHP-FPM (30 Menit Terakhir):${NC}"
sudo journalctl -u php-fpm --since "30 minutes ago" --no-pager | tail -n 25 || true

# 3. Cek Log Error PHP-FPM
echo -e "\n${YELLOW}[3] LOG ERROR INTERNAL PHP-FPM:${NC}"
for logf in /var/log/php-fpm/error.log /var/log/php-fpm/www-error.log /www/server/php/83/var/log/php-fpm.log; do
    if [ -f "$logf" ]; then
        echo -e "${GREEN}--- $logf ---${NC}"
        sudo tail -n 15 "$logf"
    fi
done

# 4. Cek Jadwal CRON Server & aaPanel (Penyebab pola 10 menit)
echo -e "\n${YELLOW}[4] DAFTAR CRON JOB (ROOT & AAPANEL):${NC}"
sudo crontab -l 2>/dev/null || true
echo "--- /etc/crontab & /etc/cron.d ---"
cat /etc/crontab 2>/dev/null | grep -v "^#" | grep -v "^$" || true
sudo head -n 5 /etc/cron.d/* 2>/dev/null || true

echo -e "\n--- Isi Script di /www/server/cron/ ---"
if [ -d "/www/server/cron" ]; then
    for f in /www/server/cron/*; do
        if [ -f "$f" ]; then
            echo -e "${CYAN}[File: $f]${NC}"
            cat "$f"
            echo ""
        fi
    done
fi

# 5. Cek Systemd Timers
echo -e "\n${YELLOW}[5] SYSTEMD TIMERS AKTIF:${NC}"
sudo systemctl list-timers --no-pager | head -n 15

# 6. Status Socket dan Service Saat Ini
echo -e "\n${YELLOW}[6] STATUS SERVICE & SOCKET SAAT INI:${NC}"
sudo systemctl status php-fpm --no-pager | head -n 15
ls -la /tmp/php-cgi-83.sock 2>/dev/null || echo -e "${RED}Socket /tmp/php-cgi-83.sock TIDAK ADA!${NC}"
ls -la /run/php-fpm/ 2>/dev/null || true

echo -e "\n${CYAN}======================================================${NC}"
