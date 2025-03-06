@echo off
cd /d "C:\Users\USER\Desktop\bloom_ads"
echo %date% %time% - Starting currency rate update >> scheduler_log.txt
php artisan schedule:run >> scheduler_log.txt 2>&1
echo %date% %time% - Finished currency rate update >> scheduler_log.txt
