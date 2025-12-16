@echo off
setlocal enabledelayedexpansion

echo Creating folder structure and files...

:: -------------------------------
:: ORIGINAL STRUCTURE (TIDAK DIKURANGI)
:: -------------------------------

:: Create directories
mkdir admin
mkdir assets\css
mkdir assets\js
mkdir assets\images
mkdir assets\uploads
mkdir config
mkdir lib
mkdir models
mkdir controllers
mkdir views
mkdir views\booking
mkdir views\catalog
mkdir views\payment

:: Create files in admin directory
type nul > admin\index.php

:: Create files in config directory
type nul > config\database.php
type nul > config\menu.json
type nul > config\app.php

:: Create files in lib directory
type nul > lib\auth.php
type nul > lib\functions.php
type nul > lib\middleware.php

:: Create model files
type nul > models\Item.php
type nul > models\Booking.php
type nul > models\User.php

:: Controllers
type nul > controllers\CatalogController.php
type nul > controllers\BookingController.php
type nul > controllers\PaymentController.php

:: Views
type nul > views\footer.php
type nul > views\header.php
type nul > views\sidebar.php
type nul > views\topnav.php

type nul > views\catalog\list.php
type nul > views\catalog\detail.php

type nul > views\booking\form.php
type nul > views\booking\status.php

type nul > views\payment\checkout.php
type nul > views\payment\success.php

:: Root files
type nul > .htaccess
type nul > .env
type nul > composer.json
type nul > index.php
type nul > login.php
type nul > logout.php
type nul > register.php


:: -----------------------------------------------------
:: TAMBAHAN UNTUK CSS, JS, BOOTSTRAP (TIDAK MENGURANGI)
:: -----------------------------------------------------

echo Adding missing Bootstrap and custom asset files...

:: Bootstrap CSS
echo /* Bootstrap placeholder */ > assets\css\bootstrap.min.css

:: Custom CSS
echo /* Custom style */ > assets\css\style.css

:: Bootstrap JS
echo // Bootstrap bundle placeholder > assets\js\bootstrap.bundle.min.js

:: Custom main.js
echo console.log("Camping Rental App Loaded"); > assets\js\main.js


:: -----------------------------------------------------
:: TAMBAHAN TEMPLATE HEADER & FOOTER (tidak menghapus file lama)
:: -----------------------------------------------------

echo Adding Bootstrap HTML template to header/footer...

(
echo <!DOCTYPE html>
echo <html lang="en">
echo <head>
echo     ^<meta charset="UTF-8"^>
echo     ^<meta name="viewport" content="width=device-width, initial-scale=1.0"^>
echo     ^<title>Aplikasi Camping Rental</title^>
echo     ^<link rel="stylesheet" href="/assets/css/bootstrap.min.css"^>
echo     ^<link rel="stylesheet" href="/assets/css/style.css"^>
echo </head>
echo <body>
echo <?php include "topnav.php"; ?>
echo <div class="container mt-4">
) > views\header.php

(
echo </div>
echo ^<script src="/assets/js/bootstrap.bundle.min.js"^>^</script^>
echo ^<script src="/assets/js/main.js"^>^</script^>
echo </body>
echo </html>
) > views\footer.php


echo Folder structure and files created successfully!
pause
