<?php
// Set the content type to JSON
header('Content-Type: application/json');

// Check if the script is running on a rooted device with Magisk and BusyBox
if (!file_exists('/sbin/magisk') || !shell_exec('which busybox')) {
    echo json_encode([
        'error' => 'This script requires a rooted device with Magisk and BusyBox installed.'
    ]);
    exit;
}

// Function to execute shell commands safely
function runCommand($command) {
    return trim(shell_exec($command));
}

// Collect device information
$device = [
    'model' => runCommand('getprop ro.product.model'),
    'android_version' => runCommand('getprop ro.build.version.release'),
    'kernel_version' => runCommand('uname -r'),
    'root_status' => file_exists('/sbin/magisk'),
    'uptime' => runCommand('cat /proc/uptime | awk \'{print $1}\'')
];

// Collect network information
$network = [
    'isp' => runCommand('getprop dhcp.wlan0.vendor'),
    'ip_address' => runCommand('ip -4 addr show wlan0 | grep inet | awk \'{print $2}\' | cut -d"/" -f1'),
    'signal_strength' => runCommand('cat /proc/net/wireless | grep wlan0 | awk \'{print int($3)}\''),
    'network_type' => runCommand('getprop gsm.network.type'),
    'mac_address' => runCommand('cat /sys/class/net/wlan0/address'),
    'rx_bytes' => runCommand('cat /sys/class/net/wlan0/statistics/rx_bytes'),
    'tx_bytes' => runCommand('cat /sys/class/net/wlan0/statistics/tx_bytes')
];

// Collect system information
$system = [
    'cpu_usage' => runCommand('top -bn1 | grep "CPU:" | awk \'{print $2}\' | sed \'s/%//\''),
    'cpu_cores' => runCommand('nproc'),
    'cpu_freq' => runCommand('cat /sys/devices/system/cpu/cpu0/cpufreq/cpuinfo_cur_freq'),
    'ram_usage' => runCommand('free | grep Mem | awk \'{print ($3/$2) * 100}\''),
    'ram_total' => runCommand('free | grep Mem | awk \'{print $2}\''),
    'ram_used' => runCommand('free | grep Mem | awk \'{print $3}\''),
    'battery_level' => runCommand('dumpsys battery | grep level | awk \'{print $2}\''),
    'battery_status' => runCommand('dumpsys battery | grep status | awk \'{print $2}\''),
    'battery_temp' => runCommand('dumpsys battery | grep temperature | awk \'{print $2/10}\'')
];

// Collect storage information
$storage = [
    'internal_total' => runCommand('df /data | tail -1 | awk \'{print $2}\''),
    'internal_used' => runCommand('df /data | tail -1 | awk \'{print $3}\''),
    'external_total' => runCommand('df /mnt/media_rw | tail -1 | awk \'{print $2}\''),
    'external_used' => runCommand('df /mnt/media_rw | tail -1 | awk \'{print $3}\'')
];

// Collect running processes
$processes = [];
foreach (explode("\n", runCommand('ps -eo pid,comm,%cpu,%mem --sort=-%cpu | head -n 15')) as $line) {
    $parts = preg_split('/\s+/', $line);
    if (count($parts) === 4) {
        $processes[] = [
            'pid' => $parts[0],
            'name' => $parts[1],
            'cpu' => $parts[2],
            'memory' => $parts[3]
        ];
    }
}

// Collect sensor data (example for accelerometer, gyroscope, etc.)
$sensors = [
    'accelerometer' => [
        'x' => runCommand('cat /sys/class/input/input1/event0 | awk \'{print $1}\''),
        'y' => runCommand('cat /sys/class/input/input1/event1 | awk \'{print $2}\''),
        'z' => runCommand('cat /sys/class/input/input1/event2 | awk \'{print $3}\'')
    ],
    'gyroscope' => [
        'x' => runCommand('cat /sys/class/input/input2/event0 | awk \'{print $1}\''),
        'y' => runCommand('cat /sys/class/input/input2/event1 | awk \'{print $2}\''),
        'z' => runCommand('cat /sys/class/input/input2/event2 | awk \'{print $3}\'')
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

// Output the JSON response
echo json_encode($response, JSON_PRETTY_PRINT);
