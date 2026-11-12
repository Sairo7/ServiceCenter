<?php
/**
 * Single Entry Point (Front Controller)
 * TKJ Service Business Web App
 */

// 1. Require Application Configuration
require_once __DIR__ . '/config/config.php';

// 2. Require Core Classes & Middleware
require_once __DIR__ . '/core/Database.php';
require_once __DIR__ . '/core/Controller.php';
require_once __DIR__ . '/core/Flasher.php';
require_once __DIR__ . '/core/Auth.php';
require_once __DIR__ . '/core/App.php';

// 3. Autoload Models
spl_autoload_register(function ($className) {
    $file = __DIR__ . '/app/models/' . $className . '.php';
    if (file_exists($file)) {
        require_once $file;
    }
});

// 4. Initialize Core Router App
$app = new App();
