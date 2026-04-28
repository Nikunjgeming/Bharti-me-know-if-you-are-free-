<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST');
header('Access-Control-Allow-Headers: Content-Type');

require_once '../includes/functions.php';

$action = $_GET['action'] ?? 'all';
$search = $_GET['search'] ?? '';
$category = $_GET['category'] ?? '';
$page = intval($_GET['page'] ?? 1);
$limit = intval($_GET['limit'] ?? 50);

if ($search) {
    $result = searchServices($search);
} elseif ($category && $category != 'all') {
    $result = getServicesByCategory($category);
} else {
    $result = getAllServices();
}

$services = $result['services'];
$total = $result['total'];

// Pagination
$offset = ($page - 1) * $limit;
$paginatedServices = array_slice($services, $offset, $limit, true);

// Format response
$response = [
    'success' => true,
    'total' => $total,
    'page' => $page,
    'limit' => $limit,
    'total_pages' => ceil($total / $limit),
    'services' => []
];

foreach ($paginatedServices as $code => $info) {
    $response['services'][] = [
        'code' => $code,
        'name' => $info['name'] ?? $code,
        'price' => $info['price'] ?? 10,
        'price_formatted' => formatPrice($info['price'] ?? 10),
        'available' => $info['available'] ?? true,
        'icon' => getServiceIcon($code),
        'category' => 'general'
    ];
}

echo json_encode($response);
?>