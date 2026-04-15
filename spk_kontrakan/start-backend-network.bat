@echo off
REM ============================================================================
REM Script untuk memulai Laravel Backend dengan Network Access
REM Tujuan: Agar Flutter Mobile Device bisa terhubung dari network
REM ============================================================================

cls
echo.
echo ╔════════════════════════════════════════════════════════╗
echo ║  🚀 SPK Backend - Network Accessible Mode              ║
echo ╚════════════════════════════════════════════════════════╝
echo.

REM Dapatkan IP address komputer
echo Mendeteksi IP Address komputer...
for /f %%I in ('powershell -NoProfile -Command "(Get-NetIPAddress -AddressFamily IPv4 ^| Where-Object IPAddress -NotLike '127.*' ^| Where-Object IPAddress -NotLike '169.254.*' ^| Where-Object IPAddress -NotLike '192.168.137.*' ^| Select-Object -First 1 -ExpandProperty IPAddress)"') do set IP=%%I

if "%IP%"=="" (
    echo ❌ ERROR: Tidak bisa menemukan IP Address!
    echo Pastikan komputer terhubung ke network.
    pause
    exit /b 1
)

echo ✅ IP Address terdeteksi: %IP%
echo.
echo 📱 Backend akan accessible dari:
echo    • Local: http://localhost:8000
echo    • Network: http://%IP%:8000
echo    • API: http://%IP%:8000/api
echo.
echo ⚙️  Starting Laravel Backend...
echo.

REM Pindah ke directory project
cd /d "%~dp0"

REM Jalankan Laravel serve dengan host 0.0.0.0
php artisan serve --host=0.0.0.0 --port=8000

pause
