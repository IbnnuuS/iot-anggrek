@echo off
cd /d "%~dp0"
echo Menjalankan Web Monitoring + Riwayat Otomatis...
start "Anggrek - Web Monitoring" php artisan serve
start "Anggrek - Riwayat Otomatis" php artisan schedule:work
echo.
echo Selesai. Dua jendela baru terbuka (Web ^& Riwayat) - JANGAN ditutup selama monitoring.
echo Tutup kedua jendela itu untuk mematikan.
pause
