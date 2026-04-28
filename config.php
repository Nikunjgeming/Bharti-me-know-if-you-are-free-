<?php
// Database Configuration
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'money_maker');

// API Configuration
define('SMS_API_KEY', '3u2gVqnhq0lltzBNjDNmxXQsruT0jKg1');
define('SMS_API_URL', 'https://meowsms.shop/stubs/handler_api.php');

// Payment Gateway Configuration
define('PAYMENT_API_KEY', '9f65d2938cb85bd7d58745946b84d4d8');
define('PAYMENT_CREATE_URL', 'https://smartupiqr.co.in/api/create-order');
define('PAYMENT_CHECK_URL', 'https://smartupiqr.co.in/api/check-order-status');

// Bot Configuration
define('BOT_USERNAME', 'MoneyMakerBot');
define('ADMIN_ID', 8465424759);
define('SITE_URL', 'https://yourdomain.com');
define('SITE_NAME', 'Money Maker Bot');

// Cache Settings
define('CACHE_DIR', __DIR__ . '/../cache/');
define('CACHE_TIME', 300); // 5 minutes

// Create cache directory if not exists
if (!file_exists(CACHE_DIR)) {
    mkdir(CACHE_DIR, 0777, true);
}

// Session Start
session_start();

// Time Zone
date_default_timezone_set('Asia/Kolkata');
?>