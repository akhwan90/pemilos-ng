#!/bin/sh

# Pastikan hak akses folder storage dan turunannya benar (khususnya untuk folder yang dimounting)
# Ini mencegah masalah Permission Denied saat Docker me-mount volume dari Host
chown -R www-data:www-data /var/www/html/storage
chmod -R 775 /var/www/html/storage

# Hapus file hot agar Vite Development Mode tidak berjalan secara paksa di produksi
rm -f /var/www/html/public/hot

# Jalankan command bawaan Docker (biasanya supervisord)
exec "$@"
