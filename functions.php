<?php
require_once 'config.php';

// ============ SMS API FUNCTIONS ============

function apiRequest($params) {
    $params['api_key'] = SMS_API_KEY;
    $url = SMS_API_URL . '?' . http_build_query($params);
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_TIMEOUT, 30);
    $response = curl_exec($ch);
    curl_close($ch);
    
    logApiCall($params['action'], $params, $response);
    
    return $response;
}

function getAllServices($country = '22') {
    $cacheFile = CACHE_DIR . 'services_all_' . $country . '.json';
    
    // Check cache
    if (file_exists($cacheFile) && (time() - filemtime($cacheFile)) < CACHE_TIME) {
        $cached = file_get_contents($cacheFile);
        $data = json_decode($cached, true);
        if ($data && isset($data['services'])) {
            return $data;
        }
    }
    
    $response = apiRequest(['action' => 'getServices', 'country' => $country]);
    $services = json_decode($response, true);
    
    if (is_array($services) && !isset($services['status'])) {
        $result = [
            'total' => count($services),
            'services' => $services,
            'last_updated' => date('Y-m-d H:i:s')
        ];
        file_put_contents($cacheFile, json_encode($result));
        return $result;
    }
    
    // Fallback to cached if API fails
    if (file_exists($cacheFile)) {
        return json_decode(file_get_contents($cacheFile), true);
    }
    
    return ['total' => 0, 'services' => []];
}

function searchServices($query, $country = '22') {
    $allServices = getAllServices($country);
    $services = $allServices['services'];
    $results = [];
    
    $query = strtolower(trim($query));
    
    foreach ($services as $code => $info) {
        $name = strtolower($info['name'] ?? '');
        if (strpos($name, $query) !== false || strpos($code, $query) !== false) {
            $results[$code] = $info;
        }
    }
    
    return [
        'total' => count($results),
        'query' => $query,
        'services' => $results
    ];
}

function getServicesByCategory($category = null) {
    $allServices = getAllServices();
    $services = $allServices['services'];
    
    $categories = [
        'social' => ['whatsapp', 'telegram', 'instagram', 'facebook', 'twitter', 'snapchat', 'linkedin', 'tiktok', 'discord', 'reddit'],
        'email' => ['gmail', 'outlook', 'yahoo', 'protonmail', 'mailru'],
        'ecommerce' => ['amazon', 'flipkart', 'ebay', 'aliexpress', 'walmart', 'shopify'],
        'payment' => ['paytm', 'googlepay', 'phonepe', 'amazonpay', 'paypal', 'stripe'],
        'gaming' => ['pubg', 'freefire', 'callofduty', 'fortnite', 'steam', 'epicgames', 'roblox'],
        'dating' => ['tinder', 'bumble', 'hinge', 'okcupid'],
        'food' => ['swiggy', 'zomato', 'ubereats', 'doordash'],
        'travel' => ['uber', 'ola', 'rapido', 'airbnb', 'makemytrip'],
        'entertainment' => ['netflix', 'spotify', 'youtube', 'hotstar', 'primevideo', 'disneyplus'],
        'productivity' => ['microsoft', 'google', 'slack', 'zoom', 'teams', 'notion']
    ];
    
    if ($category && isset($categories[$category])) {
        $filtered = [];
        foreach ($categories[$category] as $serviceCode) {
            if (isset($services[$serviceCode])) {
                $filtered[$serviceCode] = $services[$serviceCode];
            }
        }
        return ['total' => count($filtered), 'services' => $filtered];
    }
    
    return $allServices;
}

function getBalance() {
    $response = apiRequest(['action' => 'getBalance']);
    if (preg_match('/ACCESS_BALANCE:([0-9.]+)/', $response, $matches)) {
        return floatval($matches[1]);
    }
    return 0;
}

function getNumber($service, $country = '22') {
    $response = apiRequest(['action' => 'getNumber', 'service' => $service, 'country' => $country]);
    
    if (preg_match('/ACCESS_NUMBER:([^:]+):(.+)/', $response, $matches)) {
        return [
            'success' => true,
            'order_id' => $matches[1],
            'phone_number' => $matches[2]
        ];
    }
    
    $errorMsg = $response;
    if ($response == 'NO_NUMBERS') {
        $errorMsg = 'No numbers available for this service. Please try another service.';
    } elseif ($response == 'NO_BALANCE') {
        $errorMsg = 'Insufficient API balance. Contact admin.';
    } elseif ($response == 'BAD_SERVICE') {
        $errorMsg = 'Invalid service code.';
    }
    
    return ['success' => false, 'error' => $errorMsg];
}

function checkOTP($orderId) {
    $response = apiRequest(['action' => 'getStatus', 'id' => $orderId]);
    
    if (preg_match('/STATUS_OK:(.+)/', $response, $matches)) {
        preg_match('/\b\d{4,8}\b/', $matches[1], $otpMatch);
        $otp = $otpMatch[0] ?? '';
        
        return [
            'success' => true,
            'status' => 'received',
            'message' => $matches[1],
            'otp' => $otp
        ];
    } elseif ($response == 'STATUS_WAIT_CODE') {
        return ['success' => false, 'status' => 'waiting', 'message' => 'Waiting for OTP...'];
    } elseif ($response == 'STATUS_CANCEL') {
        return ['success' => false, 'status' => 'cancelled', 'message' => 'Order cancelled or expired'];
    }
    
    return ['success' => false, 'status' => 'unknown', 'message' => 'Unknown status'];
}

function cancelOrder($orderId) {
    $response = apiRequest(['action' => 'setStatus', 'id' => $orderId, 'status' => 8]);
    return strpos($response, 'ACCESS_CANCEL') !== false;
}

function completeOrder($orderId) {
    $response = apiRequest(['action' => 'setStatus', 'id' => $orderId, 'status' => 6]);
    return strpos($response, 'ACCESS_ACTIVATION') !== false;
}

// ============ USER FUNCTIONS ============

function getUser($userId) {
    global $conn;
    $result = $conn->query("SELECT * FROM users WHERE user_id = $userId");
    return $result->fetch_assoc();
}

function getUserBalance($userId) {
    global $conn;
    $result = $conn->query("SELECT balance FROM users WHERE user_id = $userId");
    if ($row = $result->fetch_assoc()) {
        return floatval($row['balance']);
    }
    return 0;
}

function updateUserBalance($userId, $amount, $add = true) {
    global $conn;
    $current = getUserBalance($userId);
    $new = $add ? $current + $amount : $current - $amount;
    $conn->query("UPDATE users SET balance = $new WHERE user_id = $userId");
    return $new;
}

function isAdmin() {
    return isset($_SESSION['is_admin']) && $_SESSION['is_admin'] == 1;
}

function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

// ============ PAYMENT FUNCTIONS ============

function createPaymentOrder($userId, $amount) {
    global $conn;
    
    $orderId = time() . rand(100, 999);
    
    $payload = [
        'customer_mobile' => substr($userId, -10),
        'user_token' => PAYMENT_API_KEY,
        'amount' => $amount,
        'order_id' => $orderId,
        'redirect_url' => SITE_URL . '/dashboard.php'
    ];
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, PAYMENT_CREATE_URL);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    $response = curl_exec($ch);
    curl_close($ch);
    
    $result = json_decode($response, true);
    
    if ($result && isset($result['status_code']) && $result['status_code'] == 201) {
        $paymentOrderId = $result['result']['orderId'];
        $paymentUrl = $result['result']['payment_url'];
        
        $conn->query("INSERT INTO pending_payments (payment_order_id, user_id, amount, status, created_at) VALUES 
                      ('$paymentOrderId', $userId, $amount, 'pending', NOW())");
        
        $txnId = 'TXN_' . time() . rand(100, 999);
        $conn->query("INSERT INTO transactions (txn_id, user_id, amount, type, status, payment_order_id, created_at) VALUES 
                      ('$txnId', $userId, $amount, 'deposit', 'pending', '$paymentOrderId', NOW())");
        
        return [
            'success' => true,
            'payment_url' => $paymentUrl,
            'payment_order_id' => $paymentOrderId
        ];
    }
    
    return ['success' => false, 'error' => $result['message'] ?? 'Payment creation failed'];
}

function checkPaymentStatus($paymentOrderId) {
    global $conn;
    
    $payload = [
        'user_token' => PAYMENT_API_KEY,
        'order_id' => $paymentOrderId
    ];
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, PAYMENT_CHECK_URL);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    $response = curl_exec($ch);
    curl_close($ch);
    
    $result = json_decode($response, true);
    
    if ($result && $result['status_code'] == 200 && $result['result']['txnStatus'] == 'SUCCESS') {
        $amount = $result['result']['amount'];
        $utr = $result['result']['utr'];
        
        $conn->query("UPDATE transactions SET status = 'completed', utr_number = '$utr', processed_at = NOW() 
                      WHERE payment_order_id = '$paymentOrderId'");
        $conn->query("UPDATE pending_payments SET status = 'completed', utr_number = '$utr' 
                      WHERE payment_order_id = '$paymentOrderId'");
        
        $result2 = $conn->query("SELECT user_id FROM pending_payments WHERE payment_order_id = '$paymentOrderId'");
        if ($row = $result2->fetch_assoc()) {
            $userId = $row['user_id'];
            updateUserBalance($userId, $amount, true);
        }
        
        return ['success' => true, 'amount' => $amount, 'utr' => $utr];
    }
    
    return ['success' => false];
}

// ============ HELPER FUNCTIONS ============

function logApiCall($action, $request, $response) {
    global $conn;
    $ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
    $requestJson = is_array($request) ? json_encode($request) : $request;
    
    $conn->query("INSERT INTO api_logs (action, request, response, ip_address, created_at) VALUES 
                  ('$action', '" . addslashes($requestJson) . "', '" . addslashes($response) . "', '$ip', NOW())");
}

function getServiceIcon($code) {
    $icons = [
        'whatsapp' => 'fab fa-whatsapp',
        'telegram' => 'fab fa-telegram',
        'instagram' => 'fab fa-instagram',
        'facebook' => 'fab fa-facebook',
        'twitter' => 'fab fa-twitter',
        'snapchat' => 'fab fa-snapchat',
        'linkedin' => 'fab fa-linkedin',
        'youtube' => 'fab fa-youtube',
        'tiktok' => 'fab fa-tiktok',
        'discord' => 'fab fa-discord',
        'reddit' => 'fab fa-reddit',
        'gmail' => 'fas fa-envelope',
        'outlook' => 'fas fa-envelope',
        'yahoo' => 'fas fa-envelope',
        'amazon' => 'fab fa-amazon',
        'flipkart' => 'fas fa-shopping-cart',
        'paytm' => 'fas fa-rupee-sign',
        'googlepay' => 'fab fa-google',
        'phonepe' => 'fas fa-mobile-alt',
        'paypal' => 'fab fa-paypal',
        'stripe' => 'fab fa-stripe',
        'pubg' => 'fas fa-gamepad',
        'freefire' => 'fas fa-gamepad',
        'roblox' => 'fas fa-gamepad',
        'steam' => 'fab fa-steam',
        'netflix' => 'fab fa-netflix',
        'spotify' => 'fab fa-spotify',
        'hotstar' => 'fas fa-tv',
        'primevideo' => 'fab fa-amazon',
        'uber' => 'fas fa-taxi',
        'ola' => 'fas fa-taxi',
        'swiggy' => 'fas fa-utensils',
        'zomato' => 'fas fa-utensils',
        'tinder' => 'fab fa-fire',
        'bumble' => 'fab fa-bumble',
        'microsoft' => 'fab fa-microsoft',
        'google' => 'fab fa-google',
        'slack' => 'fab fa-slack',
        'zoom' => 'fas fa-video',
        'teams' => 'fab fa-microsoft',
        'notion' => 'fas fa-book',
        'shopify' => 'fab fa-shopify',
        'ebay' => 'fab fa-ebay',
        'walmart' => 'fas fa-store',
        'aliexpress' => 'fas fa-shopping-bag',
        'makemytrip' => 'fas fa-plane',
        'airbnb' => 'fab fa-airbnb',
        'doordash' => 'fas fa-motorcycle',
        'ubereats' => 'fas fa-motorcycle',
        'epicgames' => 'fab fa-epicgames',
        'callofduty' => 'fas fa-gamepad',
        'fortnite' => 'fas fa-gamepad',
        'okcupid' => 'fas fa-heart',
        'hinge' => 'fas fa-heart',
        'protonmail' => 'fas fa-shield-alt',
        'mailru' => 'fas fa-envelope',
        'disneyplus' => 'fab fa-disney',
    ];
    return $icons[$code] ?? 'fas fa-mobile-alt';
}

function getCategoryIcon($category) {
    $icons = [
        'social' => 'fas fa-users',
        'email' => 'fas fa-envelope',
        'ecommerce' => 'fas fa-shopping-cart',
        'payment' => 'fas fa-credit-card',
        'gaming' => 'fas fa-gamepad',
        'dating' => 'fas fa-heart',
        'food' => 'fas fa-utensils',
        'travel' => 'fas fa-taxi',
        'entertainment' => 'fas fa-film',
        'productivity' => 'fas fa-briefcase',
        'all' => 'fas fa-globe'
    ];
    return $icons[$category] ?? 'fas fa-circle';
}

function formatPrice($price) {
    return '₹' . number_format($price, 2);
}

function timeAgo($datetime) {
    $timestamp = strtotime($datetime);
    $diff = time() - $timestamp;
    
    if ($diff < 60) return $diff . ' seconds ago';
    if ($diff < 3600) return floor($diff / 60) . ' minutes ago';
    if ($diff < 86400) return floor($diff / 3600) . ' hours ago';
    return floor($diff / 86400) . ' days ago';
}
?>