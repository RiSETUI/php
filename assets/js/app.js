document.addEventListener('DOMContentLoaded', function() {
    const eventSource = new EventSource('realtime.php');
    const chart = Chart.getChart('networkChart');
    const maxDataPoints = 60; // Show 60 data points (1 minute at 1s intervals)
    
    // Format speed with unit conversion
    function formatSpeed(speed) {
        if (speed >= 1024) {
            return (speed / 1024).toFixed(2) + ' Mbps';
        }
        return speed.toFixed(2) + ' Kbps';
    }
    
    eventSource.addEventListener('update', function(e) {
        const data = JSON.parse(e.data);
        
        // Update network stats
        document.getElementById('download-speed').textContent = formatSpeed(data.network.download_speed);
        document.getElementById('upload-speed').textContent = formatSpeed(data.network.upload_speed);
        document.getElementById('download-total').textContent = formatBytes(data.network.download_total);
        document.getElementById('upload-total').textContent = formatBytes(data.network.upload_total);
        
        // Update connection info
        document.getElementById('isp-name').textContent = data.isp.isp_name;
        document.getElementById('ip-address').textContent = data.isp.ip_address;
        document.getElementById('location').textContent = `${data.isp.city}, ${data.isp.country}`;
        document.getElementById('network-type').textContent = data.network.network_type;
        document.getElementById('signal-strength').textContent = data.network.signal_strength;
        document.getElementById('latency').textContent = data.network.latency;
        
        // Update device info
        document.getElementById('device-model').textContent = data.device.model;
        document.getElementById('android-version').textContent = data.device.android_version;
        document.getElementById('kernel-version').textContent = data.device.kernel_version;
        document.getElementById('root-status').textContent = data.device.root_status;
        document.getElementById('battery-level').textContent = data.device.battery_level;
        document.getElementById('cpu-usage').textContent = data.device.cpu_usage;
        
        // Update advanced stats
        document.getElementById('tcp-connections').textContent = data.network.tcp_connections;
        document.getElementById('memory-usage').textContent = data.device.memory_usage;
        document.getElementById('storage-free').textContent = formatBytes(data.device.storage_free);
        
        // Update chart
        const now = new Date();
        const timeLabel = now.getHours() + ':' + now.getMinutes() + ':' + now.getSeconds();
        
        // Add new data
        chart.data.labels.push(timeLabel);
        chart.data.datasets[0].data.push(data.network.download_speed);
        chart.data.datasets[1].data.push(data.network.upload_speed);
        
        // Remove old data if we have too many points
        if (chart.data.labels.length > maxDataPoints) {
            chart.data.labels.shift();
            chart.data.datasets[0].data.shift();
            chart.data.datasets[1].data.shift();
        }
        
        chart.update();
    });
    
    // Format bytes helper function
    function formatBytes(bytes, decimals = 2) {
        if (bytes === 0) return '0 Bytes';
        
        const k = 1024;
        const dm = decimals < 0 ? 0 : decimals;
        const sizes = ['Bytes', 'KB', 'MB', 'GB', 'TB'];
        
        const i = Math.floor(Math.log(bytes) / Math.log(k));
        
        return parseFloat((bytes / Math.pow(k, i)).toFixed(dm)) + ' ' + sizes[i];
    }
});