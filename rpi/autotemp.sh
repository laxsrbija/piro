#!/bin/bash
# Ova skripta postavlja temperaturu grejnog tela na dnevne vrednosti
# ukoliko je pec postavljena na automatski rezim i izvrsava se u 8 sati ujutru.
# 0 8 * * * /bin/bash /var/www/html/piro/rpi/autotemp.sh
echo "Izvrsavanje autotemp skripte..."
wget -q -O - "http://localhost/piro/rpi/piro-query.php?f=autoTemp&arg=-1"
