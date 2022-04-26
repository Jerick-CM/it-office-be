https://docs.laravel-excel.com/3.1/getting-started/installation.html

composer require psr/simple-cache:^1.0 maatwebsite/excel
php artisan vendor:publish --provider="Maatwebsite\Excel\ExcelServiceProvider" --tag=config

composer require phpoffice/phpspreadsheet
