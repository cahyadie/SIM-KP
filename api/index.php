<?php

// Memaksa Laravel menggunakan folder /tmp untuk semua file cache dan kompilasi di Vercel
putenv('VIEW_COMPILED_PATH=/tmp');
putenv('APP_SERVICES_CACHE=/tmp/services.php');
putenv('APP_PACKAGES_CACHE=/tmp/packages.php');
putenv('APP_CONFIG_CACHE=/tmp/config.php');
putenv('APP_ROUTES_CACHE=/tmp/routes.php');
putenv('APP_EVENTS_CACHE=/tmp/events.php');

// Meneruskan request ke index.php bawaan Laravel
require __DIR__ . '/../public/index.php';