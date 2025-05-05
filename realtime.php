<?php
header('Content-Type: text/event-stream');
header('Cache-Control: no-cache');
header('Connection: keep-alive');

require_once 'includes/config.php';
require_once 'includes/functions.php';

function sendEvent($event, $data) {
    echo "event: $event\n";
    echo "data: " . json_encode($data) . "\n\n";
    ob_flush();
    flush();
}

$lastId = isset($_SERVER["HTTP_LAST_EVENT_ID"]) ? intval($_SERVER["HTTP_LAST_EVENT_ID"]) : 0;
if ($lastId === 0 && isset($_GET['lastId'])) {
    $lastId = intval($_GET['lastId']);
}

while (true) {
    $networkStats = getNetworkStats();
    $ispInfo = getIspInfo();
    $deviceInfo = getDeviceInfo();
    
    $data = [
        'network' => $networkStats,
        'isp' => $ispInfo,
        'device' => $deviceInfo,
        'timestamp' => time()
    ];
    
    sendEvent('update', $data);
    
    if (connection_aborted()) break;
    
    sleep(POLL_INTERVAL);
}
?>