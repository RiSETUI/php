<?php
require_once 'includes/header.php';
require_once 'includes/functions.php';

$networkStats = getNetworkStats();
$ispInfo = getIspInfo();
$deviceInfo = getDeviceInfo();
?>

<main class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <!-- Main Network Stats -->
    <div class="lg:col-span-2 bg-white rounded-xl shadow-md p-6">
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-xl font-semibold text-gray-800 flex items-center">
                <i data-lucide="network" class="mr-2 text-blue-500"></i> Network Performance
            </h2>
            <div class="flex items-center space-x-2">
                <span class="px-2 py-1 bg-green-100 text-green-800 text-xs rounded-full">Real-time</span>
                <span class="px-2 py-1 bg-indigo-100 text-indigo-800 text-xs rounded-full">1s Interval</span>
            </div>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
            <div class="bg-gray-50 p-4 rounded-lg">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-500">Download Speed</p>
                        <h3 id="download-speed" class="text-2xl font-bold text-blue-600">
                            <?php echo formatSpeed($networkStats['download_speed'] ?? 0); ?>
                        </h3>
                    </div>
                    <i data-lucide="download" class="text-blue-500 w-8 h-8"></i>
                </div>
                <div class="mt-2 text-xs text-gray-500">
                    <span id="download-total"><?php echo formatBytes($networkStats['download_total'] ?? 0); ?></span> total
                </div>
            </div>
            
            <div class="bg-gray-50 p-4 rounded-lg">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-500">Upload Speed</p>
                        <h3 id="upload-speed" class="text-2xl font-bold text-green-600">
                            <?php echo formatSpeed($networkStats['upload_speed'] ?? 0); ?>
                        </h3>
                    </div>
                    <i data-lucide="upload" class="text-green-500 w-8 h-8"></i>
                </div>
                <div class="mt-2 text-xs text-gray-500">
                    <span id="upload-total"><?php echo formatBytes($networkStats['upload_total'] ?? 0); ?></span> total
                </div>
            </div>
        </div>
        
        <div class="bg-gray-50 p-4 rounded-lg">
            <canvas id="networkChart" height="200"></canvas>
        </div>
    </div>
    
    <!-- Device and Connection Info -->
    <div class="space-y-6">
        <!-- Connection Info -->
        <div class="bg-white rounded-xl shadow-md p-6">
            <h2 class="text-xl font-semibold text-gray-800 flex items-center mb-4">
                <i data-lucide="wifi" class="mr-2 text-purple-500"></i> Connection Info
            </h2>
            
            <div class="space-y-3">
                <div class="flex justify-between">
                    <span class="text-sm text-gray-500">ISP</span>
                    <span id="isp-name" class="text-sm font-medium"><?php echo htmlspecialchars($ispInfo['isp_name'] ?? 'Unknown'); ?></span>
                </div>
                <div class="flex justify-between">
                    <span class="text-sm text-gray-500">IP Address</span>
                    <span id="ip-address" class="text-sm font-medium"><?php echo htmlspecialchars($ispInfo['ip_address'] ?? 'Unknown'); ?></span>
                </div>
                <div class="flex justify-between">
                    <span class="text-sm text-gray-500">Location</span>
                    <span id="location" class="text-sm font-medium"><?php echo htmlspecialchars($ispInfo['city'] ?? 'Unknown'); ?>, <?php echo htmlspecialchars($ispInfo['country'] ?? 'Unknown'); ?></span>
                </div>
                <div class="flex justify-between">
                    <span class="text-sm text-gray-500">Network Type</span>
                    <span id="network-type" class="text-sm font-medium"><?php echo htmlspecialchars($networkStats['network_type'] ?? 'Unknown'); ?></span>
                </div>
                <div class="flex justify-between">
                    <span class="text-sm text-gray-500">Signal Strength</span>
                    <span id="signal-strength" class="text-sm font-medium"><?php echo htmlspecialchars($networkStats['signal_strength'] ?? 'Unknown'); ?></span>
                </div>
                <div class="flex justify-between">
                    <span class="text-sm text-gray-500">Latency</span>
                    <span id="latency" class="text-sm font-medium"><?php echo htmlspecialchars($networkStats['latency'] ?? 'Unknown'); ?> ms</span>
                </div>
            </div>
        </div>
        
        <!-- Device Info -->
        <div class="bg-white rounded-xl shadow-md p-6">
            <h2 class="text-xl font-semibold text-gray-800 flex items-center mb-4">
                <i data-lucide="smartphone" class="mr-2 text-orange-500"></i> Device Info
            </h2>
            
            <div class="space-y-3">
                <div class="flex justify-between">
                    <span class="text-sm text-gray-500">Device Model</span>
                    <span id="device-model" class="text-sm font-medium"><?php echo htmlspecialchars($deviceInfo['model'] ?? 'Unknown'); ?></span>
                </div>
                <div class="flex justify-between">
                    <span class="text-sm text-gray-500">Android Version</span>
                    <span id="android-version" class="text-sm font-medium"><?php echo htmlspecialchars($deviceInfo['android_version'] ?? 'Unknown'); ?></span>
                </div>
                <div class="flex justify-between">
                    <span class="text-sm text-gray-500">Kernel Version</span>
                    <span id="kernel-version" class="text-sm font-medium"><?php echo htmlspecialchars($deviceInfo['kernel_version'] ?? 'Unknown'); ?></span>
                </div>
                <div class="flex justify-between">
                    <span class="text-sm text-gray-500">Root Status</span>
                    <span id="root-status" class="text-sm font-medium"><?php echo htmlspecialchars($deviceInfo['root_status'] ?? 'Unknown'); ?></span>
                </div>
                <div class="flex justify-between">
                    <span class="text-sm text-gray-500">Battery Level</span>
                    <span id="battery-level" class="text-sm font-medium"><?php echo htmlspecialchars($deviceInfo['battery_level'] ?? 'Unknown'); ?>%</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-sm text-gray-500">CPU Usage</span>
                    <span id="cpu-usage" class="text-sm font-medium"><?php echo htmlspecialchars($deviceInfo['cpu_usage'] ?? 'Unknown'); ?>%</span>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Additional Stats -->
    <div class="lg:col-span-3 bg-white rounded-xl shadow-md p-6">
        <h2 class="text-xl font-semibold text-gray-800 flex items-center mb-4">
            <i data-lucide="bar-chart-2" class="mr-2 text-red-500"></i> Advanced Statistics
        </h2>
        
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div class="bg-gray-50 p-4 rounded-lg">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-500">TCP Connections</p>
                        <h3 id="tcp-connections" class="text-2xl font-bold text-indigo-600">
                            <?php echo htmlspecialchars($networkStats['tcp_connections'] ?? '0'); ?>
                        </h3>
                    </div>
                    <i data-lucide="link" class="text-indigo-500 w-8 h-8"></i>
                </div>
            </div>
            
            <div class="bg-gray-50 p-4 rounded-lg">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-500">Memory Usage</p>
                        <h3 id="memory-usage" class="text-2xl font-bold text-purple-600">
                            <?php echo htmlspecialchars($deviceInfo['memory_usage'] ?? '0'); ?>%
                        </h3>
                    </div>
                    <i data-lucide="memory-stick" class="text-purple-500 w-8 h-8"></i>
                </div>
            </div>
            
            <div class="bg-gray-50 p-4 rounded-lg">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-500">Storage Free</p>
                        <h3 id="storage-free" class="text-2xl font-bold text-amber-600">
                            <?php echo formatBytes($deviceInfo['storage_free'] ?? 0); ?>
                        </h3>
                    </div>
                    <i data-lucide="hard-drive" class="text-amber-500 w-8 h-8"></i>
                </div>
            </div>
        </div>
    </div>
</main>

<script src="/assets/js/app.js"></script>
<script>
    lucide.createIcons();
    
    // Initialize chart
    const ctx = document.getElementById('networkChart').getContext('2d');
    const chart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: [],
            datasets: [
                {
                    label: 'Download (Kbps)',
                    data: [],
                    borderColor: 'rgba(59, 130, 246, 1)',
                    backgroundColor: 'rgba(59, 130, 246, 0.1)',
                    tension: 0.4,
                    fill: true
                },
                {
                    label: 'Upload (Kbps)',
                    data: [],
                    borderColor: 'rgba(16, 185, 129, 1)',
                    backgroundColor: 'rgba(16, 185, 129, 0.1)',
                    tension: 0.4,
                    fill: true
                }
            ]
        },
        options: {
            responsive: true,
            interaction: {
                mode: 'index',
                intersect: false,
            },
            scales: {
                y: {
                    beginAtZero: true,
                    title: {
                        display: true,
                        text: 'Speed (Kbps)'
                    }
                },
                x: {
                    title: {
                        display: true,
                        text: 'Time'
                    }
                }
            }
        }
    });

    // Update current time
    function updateCurrentTime() {
        const now = new Date();
        document.getElementById('current-time').textContent = now.toLocaleString();
    }
    setInterval(updateCurrentTime, 1000);
    updateCurrentTime();
</script>

</body>
</html>