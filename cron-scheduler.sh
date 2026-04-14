#!/bin/bash
# cron-scheduler.sh
# Script de cron para Nexova Desk (nicagsm) en Hostinger
# Cron hPanel: * * * * * sh /home/u164741808/domains/nexovadesk.com/public_html/nicagsm/cron-scheduler.sh

BASE="/home/u164741808/domains/nexovadesk.com/public_html/nicagsm"
PHP="/usr/bin/php"

cd "$BASE" || exit 1
$PHP artisan schedule:run >> /dev/null 2>&1
