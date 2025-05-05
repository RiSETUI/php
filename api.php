<?php
require_once 'includes/config.php';
require_once 'includes/functions.php';

header('Content-Type: application/json');

$action = $_POST['action'] ?? '';

try {
    switch ($action) {
        case 'network_stats':
            $stmt = $pdo->prepare("INSERT INTO network_stats 
                (timestamp, download_speed, upload_speed, download_total, upload_total, network_type, signal_strength, tcp_connections, latency) 
                VALUES (NOW(), ?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([
                $_POST['download_speed'],
                $_POST['upload_speed'],
                $_POST['download_total'],
                $_POST['upload_total'],
                $_POST['network_type'],
                $_POST['signal_strength'],
                $_POST['tcp_connections'],
                $_POST['latency']
            ]);
            break;
            
        case 'isp_info':
            $stmt = $pdo->prepare("INSERT INTO isp_info 
                (timestamp, ip_address, isp_name, city, country) 
                VALUES (NOW(), ?, ?, ?, ?)");
            $stmt->execute([
                $_POST['ip_address'],
                $_POST['isp_name'],
                $_POST['city'],
                $_POST['country']
            ]);
            break;
            
        case 'device_info':
            $stmt = $pdo->prepare("INSERT INTO device_info 
                (timestamp, model, android_version, kernel_version, root_status, battery_level, cpu_usage, memory_usage, storage_free) 
                VALUES (NOW(), ?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([
                $_POST['model'],
                $_POST['android_version'],
                $_POST['kernel_version'],
                $_POST['root_status'],
                $_POST['battery_level'],
                $_POST['cpu_usage'],
                $_POST['memory_usage'],
                $_POST['storage_free']
            ]);
            break;
            
        default:
            throw new Exception("Invalid action");
    }
    
    echo json_encode(['status' => 'success']);
} catch (Exception $e) {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}
?>