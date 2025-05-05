<?php
// Set the content type to Server-Sent Events
header('Content-Type: text/event-stream');
header('Cache-Control: no-cache');
header('Connection: keep-alive');

// Function to execute shell commands safely
function runCommand($command) {
    $output = shell_exec($command);
    return $output !== null ? trim($output) : null;
}

// Function to send SSE data
function sendSSE($event, $data) {
    echo "event: $event\n";
    echo "data: " . json_encode($data) . "\n\n";
    ob_flush();
    flush();
}

// Infinite loop to send real-time data
while (true) {
    // Collect device information
    $device = [
        'model' => runCommand('getprop ro.product.model') ?? 'Unknown',
        'android_version' => runCommand('getprop ro.build.version.release') ?? 'Unknown',
        'kernel_version' => runCommand('uname -r') ?? 'Unknown',
        'root_status' => file_exists('/sbin/magisk'),
        'uptime' => intval(runCommand('cat /proc/uptime | awk \'{print $1}\'')) // in seconds
    ];

    // Collect network information
    $network = [
        'isp' => runCommand('getprop dhcp.wlan0.vendor') ?? 'Unknown',
        'ip_address' => runCommand('ip -4 addr show wlan0 | grep inet | awk \'{print $2}\' | cut -d"/" -f1') ?? 'Unknown',
        'signal_strength' => intval(runCommand('cat /proc/net/wireless | grep wlan0 | awk \'{print int($3)}\'')) ?: 0,
        'network_type' => runCommand('getprop gsm.network.type') ?? 'Unknown',
        'mac_address' => runCommand('cat /sys/class/net/wlan0/address') ?? 'Unknown',
        'rx_bytes' => intval(runCommand('cat /sys/class/net/wlan0/statistics/rx_bytes')) ?: 0,
        'tx_bytes' => intval(runCommand('cat /sys/class/net/wlan0/statistics/tx_bytes')) ?: 0
    ];

    // Collect system information
    $system = [
        'cpu_usage' => round(floatval(runCommand('top -bn1 | grep "CPU:" | awk \'{print $2}\' | sed \'s/%//\'')) ?: 0, 2),
        'cpu_cores' => intval(runCommand('nproc')) ?: 0,
        'cpu_freq' => intval(runCommand('cat /sys/devices/system/cpu/cpu0/cpufreq/cpuinfo_cur_freq')) ?: 0,
        'ram_usage' => round(floatval(runCommand('free | grep Mem | awk \'{print ($3/$2) * 100}\'')) ?: 0, 2),
        'ram_total' => intval(runCommand('free | grep Mem | awk \'{print $2}\'')) ?: 0, // in KB
        'ram_used' => intval(runCommand('free | grep Mem | awk \'{print $3}\'')) ?: 0, // in KB
        'battery_level' => intval(runCommand('dumpsys battery | grep level | awk \'{print $2}\'')) ?: 0,
        'battery_status' => runCommand('dumpsys battery | grep status | awk \'{print $2}\'') ?? 'Unknown',
        'battery_temp' => round(floatval(runCommand('dumpsys battery | grep temperature | awk \'{print $2 / 10}\'')) ?: 0, 1) // in °C
    ];

    // Collect storage information
    $storage = [
        'internal_total' => intval(runCommand('df /data | tail -1 | awk \'{print $2}\'')) ?: 0, // in KB
        'internal_used' => intval(runCommand('df /data | tail -1 | awk \'{print $3}\'')) ?: 0, // in KB
        'external_total' => intval(runCommand('df /mnt/media_rw | tail -1 | awk \'{print $2}\'')) ?: 0, // in KB
        'external_used' => intval(runCommand('df /mnt/media_rw | tail -1 | awk \'{print $3}\'')) ?: 0 // in KB
    ];

    // Collect running processes
    $processes = [];
    foreach (explode("\n", runCommand('ps -eo pid,comm,%cpu,%mem --sort=-%cpu | head -n 15')) as $line) {
        $parts = preg_split('/\s+/', $line);
        if (count($parts) === 4) {
            $processes[] = [
                'pid' => $parts[0],
                'name' => $parts[1],
                'cpu' => floatval($parts[2]) ?: 0.0,
                'memory' => floatval($parts[3]) ?: 0.0
            ];
        }
    }

    // Collect sensor data
    $sensors = [
        'accelerometer' => [
            'x' => floatval(runCommand('cat /sys/class/input/input1/event0 | awk \'{print $1}\'')) ?: 0.0,
            'y' => floatval(runCommand('cat /sys/class/input/input1/event1 | awk \'{print $2}\'')) ?: 0.0,
            'z' => floatval(runCommand('cat /sys/class/input/input1/event2 | awk \'{print $3}\'')) ?: 0.0
        ],
        'gyroscope' => [
            'x' => floatval(runCommand('cat /sys/class/input/input2/event0 | awk \'{print $1}\'')) ?: 0.0,
            'y' => floatval(runCommand('cat /sys/class/input/input2/event1 | awk \'{print $2}\'')) ?: 0.0,
            'z' => floatval(runCommand('cat /sys/class/input/input2/event2 | awk \'{print $3}\'')) ?: 0.0
        ]
    ];

    // Collect system logs
    $logs = [];
    foreach (explode("\n", runCommand('dmesg | tail -n 50')) as $line) {
        $logs[] = [
            'timestamp' => time(),
            'message' => $line,
            'type' => 'info'
        ];
    }

    // Combine all data into a single response
    $response = [
        'device' => $device,
        'network' => $network,
        'system' => $system,
        'storage' => $storage,
        'processes' => $processes,
        'sensors' => $sensors,
        'logs' => $logs
    ];

    // Send data via SSE
    sendSSE('update', $response);

    // Delay before sending the next update
    sleep(1);
}
?>
