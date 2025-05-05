<?php
header('Content-Type: text/html; charset=UTF-8');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Advanced Android Monitoring</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/lucide@latest"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
    <style>
        :root {
            --primary: #6366f1;
            --secondary: #8b5cf6;
            --success: #10b981;
            --danger: #ef4444;
            --warning: #f59e0b;
            --info: #3b82f6;
        }
        .grid-container {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 1.5rem;
        }
        .network-chart-container {
            height: 280px;
        }
        .card {
            transition: all 0.3s ease;
            border-radius: 12px;
            overflow: hidden;
        }
        .card:hover {
            transform: translateY(-4px);
            box-shadow: 0 10px 25px rgba(0,0,0,0.1);
        }
        .tab-content {
            display: none;
            animation: fadeIn 0.3s ease;
        }
        .tab-content.active {
            display: block;
        }
        .status-indicator {
            width: 10px;
            height: 10px;
            border-radius: 50%;
            display: inline-block;
            margin-right: 6px;
        }
        .status-online {
            background-color: var(--success);
        }
        .status-offline {
            background-color: var(--danger);
        }
        .status-warning {
            background-color: var(--warning);
        }
        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }
        .icon-primary {
            color: var(--primary);
        }
        .icon-secondary {
            color: var(--secondary);
        }
        .icon-success {
            color: var(--success);
        }
        .icon-danger {
            color: var(--danger);
        }
        .icon-warning {
            color: var(--warning);
        }
        .icon-info {
            color: var(--info);
        }
        .glow {
            box-shadow: 0 0 10px rgba(99, 102, 241, 0.5);
        }
        .badge {
            display: inline-flex;
            align-items: center;
            padding: 0.25rem 0.75rem;
            border-radius: 9999px;
            font-size: 0.75rem;
            font-weight: 600;
        }
        .badge-primary {
            background-color: rgba(99, 102, 241, 0.1);
            color: var(--primary);
        }
        .badge-success {
            background-color: rgba(16, 185, 129, 0.1);
            color: var(--success);
        }
        .badge-warning {
            background-color: rgba(245, 158, 11, 0.1);
            color: var(--warning);
        }
        .badge-danger {
            background-color: rgba(239, 68, 68, 0.1);
            color: var(--danger);
        }
    </style>
</head>
<body class="bg-gray-50 text-gray-800">
    <div class="container mx-auto px-4 py-6 max-w-7xl">
        <!-- Header Section -->
        <header class="flex flex-col md:flex-row justify-between items-start md:items-center mb-8 gap-4">
            <div>
                <h1 class="text-3xl font-bold text-gray-800 flex items-center gap-2">
                    <i data-lucide="activity" class="w-8 h-8 icon-primary"></i>
                    <span>Android Root Monitor</span>
                </h1>
                <p class="text-gray-600 mt-1">Comprehensive real-time device monitoring dashboard</p>
            </div>
            <div class="flex flex-col sm:flex-row items-start sm:items-center gap-4">
                <div id="connection-status" class="flex items-center bg-white px-4 py-2 rounded-lg shadow-sm">
                    <span class="status-indicator status-online"></span>
                    <span class="font-medium">Connected</span>
                </div>
                <div class="bg-white px-4 py-2 rounded-lg shadow-sm flex items-center gap-2">
                    <i data-lucide="clock" class="w-4 h-4 text-gray-500"></i>
                    <span class="text-sm font-medium text-gray-700" id="last-update">Updating...</span>
                </div>
            </div>
        </header>

        <!-- Quick Stats Row -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
            <div class="bg-white rounded-xl shadow-md p-5 card">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-500">Network Speed</p>
                        <h3 class="text-2xl font-bold mt-1" id="current-network-speed">0 Kbps</h3>
                    </div>
                    <div class="p-3 rounded-full bg-indigo-50">
                        <i data-lucide="wifi" class="w-6 h-6 icon-primary"></i>
                    </div>
                </div>
                <div class="mt-4 flex items-center justify-between text-sm">
                    <span class="text-gray-500">Download: <span id="current-download" class="font-medium">0 Kbps</span></span>
                    <span class="text-gray-500">Upload: <span id="current-upload" class="font-medium">0 Kbps</span></span>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-md p-5 card">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-500">CPU Usage</p>
                        <h3 class="text-2xl font-bold mt-1" id="cpu-usage">0%</h3>
                    </div>
                    <div class="p-3 rounded-full bg-blue-50">
                        <i data-lucide="cpu" class="w-6 h-6 icon-info"></i>
                    </div>
                </div>
                <div class="mt-4 flex items-center justify-between text-sm">
                    <span class="text-gray-500">Cores: <span id="cpu-cores" class="font-medium">0</span></span>
                    <span class="text-gray-500">Freq: <span id="cpu-freq" class="font-medium">0 MHz</span></span>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-md p-5 card">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-500">Memory Usage</p>
                        <h3 class="text-2xl font-bold mt-1" id="ram-usage">0%</h3>
                    </div>
                    <div class="p-3 rounded-full bg-green-50">
                        <i data-lucide="memory-stick" class="w-6 h-6 icon-success"></i>
                    </div>
                </div>
                <div class="mt-4 flex items-center justify-between text-sm">
                    <span class="text-gray-500">Used: <span id="ram-used" class="font-medium">0 MB</span></span>
                    <span class="text-gray-500">Free: <span id="ram-available" class="font-medium">0 MB</span></span>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-md p-5 card">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-500">Battery</p>
                        <h3 class="text-2xl font-bold mt-1" id="battery-level">0%</h3>
                    </div>
                    <div class="p-3 rounded-full bg-yellow-50">
                        <i data-lucide="battery-charging" class="w-6 h-6 icon-warning"></i>
                    </div>
                </div>
                <div class="mt-4 flex items-center justify-between text-sm">
                    <span class="text-gray-500">Status: <span id="battery-status" class="font-medium">Unknown</span></span>
                    <span class="text-gray-500">Temp: <span id="battery-temp" class="font-medium">0°C</span></span>
                </div>
            </div>
        </div>

        <!-- Main Content -->
        <div class="flex flex-col lg:flex-row gap-6">
            <!-- Left Column -->
            <div class="w-full lg:w-2/3 space-y-6">
                <!-- Network Chart -->
                <div class="bg-white rounded-xl shadow-md p-6 card">
                    <div class="flex items-center justify-between mb-6">
                        <h3 class="text-lg font-semibold flex items-center">
                            <i data-lucide="bar-chart-2" class="w-5 h-5 mr-2 icon-primary"></i> 
                            Network Traffic
                        </h3>
                        <div class="flex items-center gap-2">
                            <span class="badge badge-primary flex items-center">
                                <i data-lucide="download" class="w-3 h-3 mr-1"></i>
                                <span id="today-download">0 MB</span>
                            </span>
                            <span class="badge badge-primary flex items-center">
                                <i data-lucide="upload" class="w-3 h-3 mr-1"></i>
                                <span id="today-upload">0 MB</span>
                            </span>
                        </div>
                    </div>
                    <div class="network-chart-container">
                        <canvas id="networkChart"></canvas>
                    </div>
                </div>

                <!-- System Resources -->
                <div class="bg-white rounded-xl shadow-md p-6 card">
                    <h3 class="text-lg font-semibold mb-6 flex items-center">
                        <i data-lucide="gauge" class="w-5 h-5 mr-2 icon-info"></i> 
                        System Resources
                    </h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <h4 class="font-medium text-gray-700 mb-3 flex items-center">
                                <i data-lucide="cpu" class="w-4 h-4 mr-2 icon-info"></i>
                                CPU Usage
                            </h4>
                            <div class="h-40">
                                <canvas id="cpuChart"></canvas>
                            </div>
                        </div>
                        <div>
                            <h4 class="font-medium text-gray-700 mb-3 flex items-center">
                                <i data-lucide="memory-stick" class="w-4 h-4 mr-2 icon-success"></i>
                                Memory Usage
                            </h4>
                            <div class="h-40">
                                <canvas id="ramChart"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Column -->
            <div class="w-full lg:w-1/3 space-y-6">
                <!-- Device Info -->
                <div class="bg-white rounded-xl shadow-md p-6 card">
                    <h3 class="text-lg font-semibold mb-4 flex items-center">
                        <i data-lucide="smartphone" class="w-5 h-5 mr-2 icon-secondary"></i> 
                        Device Information
                    </h3>
                    <div class="space-y-3">
                        <div class="flex items-center justify-between">
                            <span class="text-gray-500">Model:</span>
                            <span id="device-model" class="font-medium">Loading...</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-gray-500">Android Version:</span>
                            <span id="android-version" class="font-medium">Loading...</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-gray-500">Kernel:</span>
                            <span id="kernel-version" class="font-medium">Loading...</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-gray-500">Root Status:</span>
                            <span id="root-status" class="font-medium badge badge-success">Loading...</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-gray-500">Uptime:</span>
                            <span id="device-uptime" class="font-medium">Loading...</span>
                        </div>
                    </div>
                </div>

                <!-- Network Info -->
                <div class="bg-white rounded-xl shadow-md p-6 card">
                    <h3 class="text-lg font-semibold mb-4 flex items-center">
                        <i data-lucide="wifi" class="w-5 h-5 mr-2 icon-primary"></i> 
                        Network Information
                    </h3>
                    <div class="space-y-3">
                        <div class="flex items-center justify-between">
                            <span class="text-gray-500">ISP:</span>
                            <span id="isp-info" class="font-medium">Loading...</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-gray-500">IP Address:</span>
                            <span id="ip-address" class="font-medium">Loading...</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-gray-500">Signal Strength:</span>
                            <span id="signal-strength" class="font-medium">Loading...</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-gray-500">Network Type:</span>
                            <span id="network-type" class="font-medium">Loading...</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-gray-500">MAC Address:</span>
                            <span id="mac-address" class="font-medium">Loading...</span>
                        </div>
                    </div>
                </div>

                <!-- Storage Info -->
                <div class="bg-white rounded-xl shadow-md p-6 card">
                    <h3 class="text-lg font-semibold mb-4 flex items-center">
                        <i data-lucide="hard-drive" class="w-5 h-5 mr-2 icon-warning"></i> 
                        Storage Information
                    </h3>
                    <div class="space-y-4">
                        <div>
                            <div class="flex items-center justify-between mb-1">
                                <span class="text-gray-500">Internal Storage</span>
                                <span class="text-sm font-medium" id="internal-percent">0% used</span>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-2">
                                <div id="internal-bar" class="bg-indigo-600 h-2 rounded-full" style="width: 0%"></div>
                            </div>
                            <div class="flex justify-between text-xs text-gray-500 mt-1">
                                <span id="internal-available">0 GB free</span>
                                <span id="internal-total">0 GB total</span>
                            </div>
                        </div>
                        <div>
                            <div class="flex items-center justify-between mb-1">
                                <span class="text-gray-500">External Storage</span>
                                <span class="text-sm font-medium" id="external-percent">0% used</span>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-2">
                                <div id="external-bar" class="bg-purple-600 h-2 rounded-full" style="width: 0%"></div>
                            </div>
                            <div class="flex justify-between text-xs text-gray-500 mt-1">
                                <span id="external-available">0 GB free</span>
                                <span id="external-total">0 GB total</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Additional Tabs -->
        <div class="mt-8">
            <div class="flex overflow-x-auto pb-2 mb-4">
                <button class="tab-button flex-shrink-0 py-2 px-4 font-medium text-indigo-600 border-b-2 border-indigo-600" data-tab="processes">Processes</button>
                <button class="tab-button flex-shrink-0 py-2 px-4 font-medium text-gray-500 hover:text-gray-700" data-tab="sensors">Sensors</button>
                <button class="tab-button flex-shrink-0 py-2 px-4 font-medium text-gray-500 hover:text-gray-700" data-tab="logs">System Logs</button>
                <button class="tab-button flex-shrink-0 py-2 px-4 font-medium text-gray-500 hover:text-gray-700" data-tab="terminal">Terminal</button>
            </div>

            <div id="processes-tab" class="tab-content active">
                <div class="bg-white rounded-xl shadow-md overflow-hidden">
                    <div class="flex flex-col md:flex-row justify-between items-start md:items-center p-6 pb-0">
                        <h3 class="text-lg font-semibold flex items-center mb-4 md:mb-0">
                            <i data-lucide="list" class="w-5 h-5 mr-2 icon-secondary"></i> 
                            Running Processes
                        </h3>
                        <div class="w-full md:w-auto flex flex-col sm:flex-row gap-3">
                            <div class="relative w-full">
                                <input type="text" placeholder="Search processes..." class="w-full pl-9 pr-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                                <i data-lucide="search" class="absolute left-3 top-2.5 text-gray-400 w-4 h-4"></i>
                            </div>
                            <select class="border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                                <option>Sort by CPU</option>
                                <option>Sort by Memory</option>
                                <option>Sort by Name</option>
                            </select>
                        </div>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">PID</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">CPU %</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">RAM</th>
                                </tr>
                            </thead>
                            <tbody id="processes-list" class="bg-white divide-y divide-gray-200">
                                <tr>
                                    <td colspan="4" class="px-6 py-4 text-center text-gray-500">Loading processes...</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="px-6 py-4 border-t border-gray-200 flex items-center justify-between">
                        <div class="text-sm text-gray-500" id="processes-count">0 processes loaded</div>
                        <div class="flex space-x-2">
                            <button class="px-3 py-1 border border-gray-300 rounded-lg bg-white text-gray-700 hover:bg-gray-50 flex items-center">
                                <i data-lucide="chevron-left" class="w-4 h-4 mr-1"></i> Previous
                            </button>
                            <button class="px-3 py-1 border border-gray-300 rounded-lg bg-white text-gray-700 hover:bg-gray-50 flex items-center">
                                Next <i data-lucide="chevron-right" class="w-4 h-4 ml-1"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <div id="sensors-tab" class="tab-content">
                <div class="bg-white rounded-xl shadow-md p-6">
                    <h3 class="text-lg font-semibold mb-6 flex items-center">
                        <i data-lucide="radio" class="w-5 h-5 mr-2 icon-warning"></i> 
                        Device Sensors
                    </h3>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4" id="sensors-container">
                        <div class="text-center py-4 px-4 bg-gray-50 rounded-lg">
                            <div class="text-gray-500 mb-1">Accelerometer</div>
                            <div class="text-xl font-semibold" id="sensor-accel">Loading...</div>
                        </div>
                        <div class="text-center py-4 px-4 bg-gray-50 rounded-lg">
                            <div class="text-gray-500 mb-1">Gyroscope</div>
                            <div class="text-xl font-semibold" id="sensor-gyro">Loading...</div>
                        </div>
                        <div class="text-center py-4 px-4 bg-gray-50 rounded-lg">
                            <div class="text-gray-500 mb-1">Proximity</div>
                            <div class="text-xl font-semibold" id="sensor-prox">Loading...</div>
                        </div>
                        <div class="text-center py-4 px-4 bg-gray-50 rounded-lg">
                            <div class="text-gray-500 mb-1">Light</div>
                            <div class="text-xl font-semibold" id="sensor-light">Loading...</div>
                        </div>
                    </div>
                </div>
            </div>

            <div id="logs-tab" class="tab-content">
                <div class="bg-white rounded-xl shadow-md overflow-hidden">
                    <div class="flex flex-col md:flex-row justify-between items-start md:items-center p-6 pb-0">
                        <h3 class="text-lg font-semibold flex items-center mb-4 md:mb-0">
                            <i data-lucide="file-text" class="w-5 h-5 mr-2 icon-danger"></i> 
                            System Logs
                        </h3>
                        <div class="flex gap-3">
                            <button class="px-3 py-1.5 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 flex items-center">
                                <i data-lucide="download" class="w-4 h-4 mr-1"></i> Export
                            </button>
                            <button class="px-3 py-1.5 border border-gray-300 rounded-lg bg-white text-gray-700 hover:bg-gray-50 flex items-center">
                                <i data-lucide="trash-2" class="w-4 h-4 mr-1"></i> Clear
                            </button>
                        </div>
                    </div>
                    <div class="p-6">
                        <div class="bg-gray-900 rounded-lg p-4 font-mono text-sm text-gray-300 h-64 overflow-y-auto" id="system-logs">
                            <div class="text-green-400">Connecting to device...</div>
                        </div>
                    </div>
                </div>
            </div>

            <div id="terminal-tab" class="tab-content">
                <div class="bg-white rounded-xl shadow-md overflow-hidden">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold mb-4 flex items-center">
                            <i data-lucide="terminal" class="w-5 h-5 mr-2 icon-success"></i> 
                            Remote Terminal
                        </h3>
                        <div class="bg-gray-900 rounded-lg p-4 font-mono text-sm text-gray-300">
                            <div class="flex items-center mb-2">
                                <span class="text-green-400">root@android:</span>
                                <span class="text-blue-400 ml-1">~$</span>
                                <input type="text" class="bg-transparent border-none outline-none text-white flex-1 ml-2" id="terminal-input" placeholder="Enter command...">
                            </div>
                            <div class="h-48 overflow-y-auto" id="terminal-output">
                                <div class="text-gray-500">Type commands to interact with your device</div>
                            </div>
                        </div>
                        <div class="mt-3 flex gap-2">
                            <button class="px-3 py-1.5 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 flex items-center">
                                <i data-lucide="play" class="w-4 h-4 mr-1"></i> Execute
                            </button>
                            <button class="px-3 py-1.5 border border-gray-300 rounded-lg bg-white text-gray-700 hover:bg-gray-50 flex items-center">
                                <i data-lucide="trash-2" class="w-4 h-4 mr-1"></i> Clear
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Initialize Lucide icons
        lucide.createIcons();
        
        // Tab switching functionality
        document.querySelectorAll('.tab-button').forEach(button => {
            button.addEventListener('click', () => {
                // Remove active class from all buttons and tabs
                document.querySelectorAll('.tab-button').forEach(btn => {
                    btn.classList.remove('border-indigo-600', 'text-indigo-600');
                    btn.classList.add('text-gray-500', 'border-transparent');
                });
                document.querySelectorAll('.tab-content').forEach(tab => {
                    tab.classList.remove('active');
                });
                
                // Add active class to clicked button and corresponding tab
                button.classList.add('border-indigo-600', 'text-indigo-600');
                button.classList.remove('text-gray-500', 'border-transparent');
                const tabId = button.getAttribute('data-tab') + '-tab';
                document.getElementById(tabId).classList.add('active');
            });
        });

        // Initialize combined network chart
        const networkCtx = document.getElementById('networkChart').getContext('2d');
        const networkChart = new Chart(networkCtx, {
            type: 'line',
            data: {
                labels: Array(30).fill(''),
                datasets: [
                    {
                        label: 'Download',
                        data: Array(30).fill(0),
                        borderColor: 'rgba(16, 185, 129, 1)',
                        backgroundColor: 'rgba(16, 185, 129, 0.1)',
                        borderWidth: 2,
                        tension: 0.3,
                        fill: true,
                        pointRadius: 0
                    },
                    {
                        label: 'Upload',
                        data: Array(30).fill(0),
                        borderColor: 'rgba(59, 130, 246, 1)',
                        backgroundColor: 'rgba(59, 130, 246, 0.1)',
                        borderWidth: 2,
                        tension: 0.3,
                        fill: true,
                        pointRadius: 0
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'top',
                        labels: {
                            usePointStyle: true,
                            padding: 20
                        }
                    },
                    tooltip: {
                        mode: 'index',
                        intersect: false,
                        callbacks: {
                            label: function(context) {
                                let label = context.dataset.label || '';
                                if (label) {
                                    label += ': ';
                                }
                                if (context.parsed.y !== null) {
                                    label += formatSpeed(context.parsed.y);
                                }
                                return label;
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                return formatSpeed(value);
                            }
                        },
                        title: {
                            display: true,
                            text: 'Speed'
                        }
                    },
                    x: {
                        display: false,
                        grid: {
                            display: false
                        }
                    }
                },
                interaction: {
                    mode: 'nearest',
                    axis: 'x',
                    intersect: false
                },
                animation: {
                    duration: 0
                }
            }
        });

        // CPU Usage chart
        const cpuCtx = document.getElementById('cpuChart').getContext('2d');
        const cpuChart = new Chart(cpuCtx, {
            type: 'doughnut',
            data: {
                labels: ['Used', 'Free'],
                datasets: [{
                    data: [0, 100],
                    backgroundColor: [
                        'rgba(59, 130, 246, 0.8)',
                        'rgba(229, 231, 235, 0.8)'
                    ],
                    borderWidth: 0
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '75%',
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            padding: 20,
                            usePointStyle: true
                        }
                    }
                }
            }
        });

        // RAM Usage chart
        const ramCtx = document.getElementById('ramChart').getContext('2d');
        const ramChart = new Chart(ramCtx, {
            type: 'doughnut',
            data: {
                labels: ['Used', 'Free'],
                datasets: [{
                    data: [0, 100],
                    backgroundColor: [
                        'rgba(16, 185, 129, 0.8)',
                        'rgba(229, 231, 235, 0.8)'
                    ],
                    borderWidth: 0
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '75%',
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            padding: 20,
                            usePointStyle: true
                        }
                    }
                }
            }
        });

        // Format speed with automatic unit conversion
        function formatSpeed(speed) {
            if (speed >= 1024) {
                return (speed / 1024).toFixed(2) + ' Mbps';
            } else {
                return speed.toFixed(0) + ' Kbps';
            }
        }

        // Format bytes to human readable
        function formatBytes(bytes, decimals = 2) {
            if (bytes === 0) return '0 Bytes';
            
            const k = 1024;
            const dm = decimals < 0 ? 0 : decimals;
            const sizes = ['Bytes', 'KB', 'MB', 'GB', 'TB'];
            const i = Math.floor(Math.log(bytes) / Math.log(k));
            
            return parseFloat((bytes / Math.pow(k, i)).toFixed(dm)) + ' ' + sizes[i];
        }

        // Format time to HH:MM:SS
        function formatTime(seconds) {
            const hrs = Math.floor(seconds / 3600);
            const mins = Math.floor((seconds % 3600) / 60);
            const secs = seconds % 60;
            
            return `${hrs.toString().padStart(2, '0')}:${mins.toString().padStart(2, '0')}:${secs.toString().padStart(2, '0')}`;
        }

        // SSE connection for real-time updates
        const eventSource = new EventSource('api.php?stream=1');
        
        // Store previous network values for speed calculation
        let prevRxBytes = 0;
        let prevTxBytes = 0;
        let lastUpdateTime = 0;
        
        // Arrays to store network speed history
        const downloadSpeeds = Array(30).fill(0);
        const uploadSpeeds = Array(30).fill(0);
        
        eventSource.onmessage = function(event) {
            const data = JSON.parse(event.data);
            const now = new Date();
            
            // Update last update time
            document.getElementById('last-update').textContent = now.toLocaleTimeString();
            
            // Device Info
            if (data.device) {
                document.getElementById('device-model').textContent = data.device.model || 'Unknown';
                document.getElementById('android-version').textContent = data.device.android_version || 'Unknown';
                document.getElementById('kernel-version').textContent = data.device.kernel_version || 'Unknown';
                document.getElementById('root-status').textContent = data.device.root_status ? 'Rooted' : 'Not Rooted';
                document.getElementById('root-status').className = data.device.root_status ? 'font-medium badge badge-success' : 'font-medium badge badge-danger';
                
                if (data.device.uptime) {
                    document.getElementById('device-uptime').textContent = formatTime(data.device.uptime);
                }
            }
            
            // Network Info
            if (data.network) {
                document.getElementById('isp-info').textContent = data.network.isp || 'Unknown';
                document.getElementById('ip-address').textContent = data.network.ip_address || 'Unknown';
                
                if (data.network.signal_strength !== undefined) {
                    const signal = data.network.signal_strength;
                    let signalText = `${signal} dBm`;
                    if (signal >= -70) signalText += ' (Good)';
                    else if (signal >= -85) signalText += ' (Fair)';
                    else signalText += ' (Poor)';
                    document.getElementById('signal-strength').textContent = signalText;
                } else {
                    document.getElementById('signal-strength').textContent = 'Unknown';
                }
                
                document.getElementById('network-type').textContent = data.network.network_type || 'Unknown';
                document.getElementById('mac-address').textContent = data.network.mac_address || 'Unknown';
                
                // Calculate network speeds
                if (data.network.rx_bytes !== undefined && data.network.tx_bytes !== undefined) {
                    const currentTime = Date.now();
                    const timeDiff = (currentTime - lastUpdateTime) / 1000; // in seconds
                    
                    if (lastUpdateTime > 0 && timeDiff > 0) {
                        const rxDiff = data.network.rx_bytes - prevRxBytes;
                        const txDiff = data.network.tx_bytes - prevTxBytes;
                        
                        // Convert to kilobits per second
                        const downloadSpeed = Math.round((rxDiff * 8) / (timeDiff * 1024));
                        const uploadSpeed = Math.round((txDiff * 8) / (timeDiff * 1024));
                        
                        // Update network speed display
                        const totalSpeed = downloadSpeed + uploadSpeed;
                        document.getElementById('current-network-speed').textContent = formatSpeed(totalSpeed);
                        document.getElementById('current-download').textContent = formatSpeed(downloadSpeed);
                        document.getElementById('current-upload').textContent = formatSpeed(uploadSpeed);
                        
                        // Update download speed chart
                        downloadSpeeds.shift();
                        downloadSpeeds.push(downloadSpeed);
                        
                        // Update upload speed chart
                        uploadSpeeds.shift();
                        uploadSpeeds.push(uploadSpeed);
                        
                        // Update combined network chart
                        networkChart.data.datasets[0].data = downloadSpeeds;
                        networkChart.data.datasets[1].data = uploadSpeeds;
                        networkChart.update();
                    }
                    
                    prevRxBytes = data.network.rx_bytes;
                    prevTxBytes = data.network.tx_bytes;
                    lastUpdateTime = currentTime;
                }
                
                // Update traffic totals
                if (data.network.today_rx) {
                    document.getElementById('today-download').textContent = formatBytes(data.network.today_rx);
                }
                if (data.network.today_tx) {
                    document.getElementById('today-upload').textContent = formatBytes(data.network.today_tx);
                }
            }
            
            // System Status
            if (data.system) {
                // CPU
                if (data.system.cpu_usage !== undefined) {
                    document.getElementById('cpu-usage').textContent = `${data.system.cpu_usage}%`;
                    cpuChart.data.datasets[0].data = [data.system.cpu_usage, 100 - data.system.cpu_usage];
                    cpuChart.update();
                }
                if (data.system.cpu_cores) {
                    document.getElementById('cpu-cores').textContent = data.system.cpu_cores;
                }
                if (data.system.cpu_freq) {
                    document.getElementById('cpu-freq').textContent = `${Math.round(data.system.cpu_freq / 1000)} MHz`;
                }
                
                // RAM
                if (data.system.ram_usage !== undefined) {
                    document.getElementById('ram-usage').textContent = `${data.system.ram_usage}%`;
                    ramChart.data.datasets[0].data = [data.system.ram_usage, 100 - data.system.ram_usage];
                    ramChart.update();
                }
                if (data.system.ram_total && data.system.ram_used) {
                    document.getElementById('ram-used').textContent = formatBytes(data.system.ram_used * 1024);
                    document.getElementById('ram-available').textContent = formatBytes((data.system.ram_total - data.system.ram_used) * 1024);
                }
                
                // Battery
                if (data.system.battery_level !== undefined) {
                    document.getElementById('battery-level').textContent = `${data.system.battery_level}%`;
                    
                    // Change battery icon based on level
                    const batteryIcon = document.querySelector('[data-lucide="battery-charging"]');
                    if (data.system.battery_level > 75) {
                        batteryIcon.setAttribute('fill', '#10b981');
                    } else if (data.system.battery_level > 30) {
                        batteryIcon.setAttribute('fill', '#f59e0b');
                    } else {
                        batteryIcon.setAttribute('fill', '#ef4444');
                    }
                    lucide.createIcons(); // Refresh icons
                }
                if (data.system.battery_status) {
                    document.getElementById('battery-status').textContent = data.system.battery_status;
                }
                if (data.system.battery_temp) {
                    document.getElementById('battery-temp').textContent = `${data.system.battery_temp}°C`;
                }
            }
            
            // Storage
            if (data.storage) {
                // Internal storage
                if (data.storage.internal_total && data.storage.internal_used) {
                    const internalFree = data.storage.internal_total - data.storage.internal_used;
                    const internalPercent = Math.round((data.storage.internal_used / data.storage.internal_total) * 100);
                    
                    document.getElementById('internal-percent').textContent = `${internalPercent}% used`;
                    document.getElementById('internal-bar').style.width = `${internalPercent}%`;
                    document.getElementById('internal-total').textContent = formatBytes(data.storage.internal_total * 1024 * 1024, 1);
                    document.getElementById('internal-available').textContent = formatBytes(internalFree * 1024 * 1024, 1);
                }
                
                // External storage
                if (data.storage.external_total && data.storage.external_used) {
                    const externalFree = data.storage.external_total - data.storage.external_used;
                    const externalPercent = Math.round((data.storage.external_used / data.storage.external_total) * 100);
                    
                    document.getElementById('external-percent').textContent = `${externalPercent}% used`;
                    document.getElementById('external-bar').style.width = `${externalPercent}%`;
                    document.getElementById('external-total').textContent = formatBytes(data.storage.external_total * 1024 * 1024, 1);
                    document.getElementById('external-available').textContent = formatBytes(externalFree * 1024 * 1024, 1);
                }
            }
            
            // Processes
            if (data.processes) {
                const processesList = document.getElementById('processes-list');
                processesList.innerHTML = '';
                
                // Sort by CPU usage (descending)
                data.processes.sort((a, b) => (b.cpu || 0) - (a.cpu || 0));
                
                // Show top 15 processes
                data.processes.slice(0, 15).forEach(process => {
                    const row = document.createElement('tr');
                    row.className = 'hover:bg-gray-50';
                    row.innerHTML = `
                        <td class="px-6 py-3 whitespace-nowrap text-sm font-medium text-gray-900">${process.pid}</td>
                        <td class="px-6 py-3 whitespace-nowrap text-sm text-gray-500 truncate max-w-xs">${process.name}</td>
                        <td class="px-6 py-3 whitespace-nowrap text-sm text-gray-500">
                            <div class="flex items-center">
                                <div class="w-8 h-1.5 bg-gray-200 rounded-full mr-2">
                                    <div class="h-1.5 bg-indigo-600 rounded-full" style="width: ${Math.min(100, process.cpu || 0)}%"></div>
                                </div>
                                ${process.cpu || '0.0'}%
                            </div>
                        </td>
                        <td class="px-6 py-3 whitespace-nowrap text-sm text-gray-500">${formatBytes(process.memory * 1024)}</td>
                    `;
                    processesList.appendChild(row);
                });
                
                document.getElementById('processes-count').textContent = `${data.processes.length} processes running`;
            }
            
            // Sensors
            if (data.sensors) {
                if (data.sensors.accelerometer) {
                    document.getElementById('sensor-accel').textContent = 
                        `${data.sensors.accelerometer.x.toFixed(2)}, ${data.sensors.accelerometer.y.toFixed(2)}, ${data.sensors.accelerometer.z.toFixed(2)}`;
                }
                if (data.sensors.gyroscope) {
                    document.getElementById('sensor-gyro').textContent = 
                        `${data.sensors.gyroscope.x.toFixed(2)}, ${data.sensors.gyroscope.y.toFixed(2)}, ${data.sensors.gyroscope.z.toFixed(2)}`;
                }
                if (data.sensors.proximity !== undefined) {
                    document.getElementById('sensor-prox').textContent = 
                        `${data.sensors.proximity} cm`;
                }
                if (data.sensors.light !== undefined) {
                    document.getElementById('sensor-light').textContent = 
                        `${data.sensors.light} lux`;
                }
            }
            
            // System Logs
            if (data.logs) {
                const logsContainer = document.getElementById('system-logs');
                data.logs.forEach(log => {
                    const logElement = document.createElement('div');
                    if (log.type === 'error') {
                        logElement.className = 'text-red-400';
                    } else if (log.type === 'warning') {
                        logElement.className = 'text-yellow-400';
                    } else {
                        logElement.className = 'text-gray-300';
                    }
                    logElement.textContent = `[${new Date(log.timestamp).toLocaleTimeString()}] ${log.message}`;
                    logsContainer.appendChild(logElement);
                });
                logsContainer.scrollTop = logsContainer.scrollHeight;
            }
        };
        
        eventSource.onerror = function() {
            const statusElement = document.getElementById('connection-status');
            statusElement.innerHTML = `
                <span class="status-indicator status-warning"></span>
                <span class="font-medium">Disconnected - Reconnecting...</span>
            `;
            statusElement.classList.add('bg-yellow-50');
            
            setTimeout(() => {
                statusElement.classList.remove('bg-yellow-50');
            }, 2000);
        };
        
        // Terminal functionality
        document.getElementById('terminal-input').addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                const command = this.value.trim();
                if (command) {
                    const output = document.getElementById('terminal-output');
                    output.innerHTML += `<div class="text-green-400">root@android:~$ ${command}</div>`;
                    
                    // Simulate command execution (in a real app, this would be an AJAX call)
                    setTimeout(() => {
                        output.innerHTML += `<div class="text-gray-300">Command output would appear here</div>`;
                        output.scrollTop = output.scrollHeight;
                    }, 500);
                    
                    this.value = '';
                    output.scrollTop = output.scrollHeight;
                }
            }
        });
    </script>
</body>
</html>
