@echo off
cd /d "%~dp0"

rem Project ini butuh PHP 8.4+; pilih PHP 8.4 dari Laragon jika tersedia.
set "PHP=php"
for /d %%D in ("C:\laragon\bin\php\php-8.4*") do set "PHP=%%D\php.exe"

echo Menjalankan Web Monitoring + Riwayat Otomatis...
start "Anggrek - Web Monitoring" "%PHP%" artisan serve
start "Anggrek - Riwayat Otomatis" "%PHP%" artisan schedule:work
echo.
echo Selesai. Dua jendela baru terbuka (Web ^& Riwayat) - JANGAN ditutup selama monitoring.
echo Tutup kedua jendela itu untuk mematikan.
pause
