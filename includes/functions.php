<?php
require_once 'config.php';

function getNetworkStats() {
    global $pdo;
    
    $stmt = $pdo->query("SELECT * FROM network_stats ORDER BY timestamp DESC LIMIT 1");
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

function getIspInfo() {
    global $pdo;
    
    $stmt = $pdo->query("SELECT * FROM isp_info ORDER BY timestamp DESC LIMIT 1");
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

function getDeviceInfo() {
    global $pdo;
    
    $stmt = $pdo->query("SELECT * FROM device_info ORDER BY timestamp DESC LIMIT 1");
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

function getHistoryData($hours = 24) {
    global $pdo;
    
    $stmt = $pdo->prepare("SELECT * FROM network_stats WHERE timestamp >= NOW() - INTERVAL ? HOUR ORDER BY timestamp");
    $stmt->execute([$hours]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function formatSpeed($bytes) {
    if ($bytes >= 1024) {
        return number_format($bytes / 1024, 2) . ' Mbps';
    }
    return number_format($bytes, 2) . ' Kbps';
}

function formatBytes($bytes, $precision = 2) {
    $units = ['B', 'KB', 'MB', 'GB', 'TB'];
    $bytes = max($bytes, 0);
    $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
    $pow = min($pow, count($units) - 1);
    $bytes /= pow(1024, $pow);
    return round($bytes, $precision) . ' ' . $units[$pow];
}
?>