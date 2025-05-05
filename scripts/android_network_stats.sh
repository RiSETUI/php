#!/system/bin/sh

# Configuration
INTERFACE="rmnet_data1"
LOG_FILE="/data/local/tmp/network_stats.log"
DB_HOST="your_server_ip"
DB_USER="root"
DB_PASS=""
DB_NAME="android_monitor"

# Function to get current timestamp
timestamp() {
    date +"%Y-%m-%d %H:%M:%S"
}

# Function to get network stats
get_network_stats() {
    # Get RX/TX bytes
    rx_bytes=$(cat /sys/class/net/$INTERFACE/statistics/rx_bytes)
    tx_bytes=$(cat /sys/class/net/$INTERFACE/statistics/tx_bytes)
    
    # Calculate speeds (bytes to kilobits)
    if [ -n "$last_rx_bytes" ] && [ -n "$last_tx_bytes" ]; then
        time_elapsed=$((current_time - last_time))
        
        dl_speed=$(( (rx_bytes - last_rx_bytes) * 8 / time_elapsed / 1024 ))
        ul_speed=$(( (tx_bytes - last_tx_bytes) * 8 / time_elapsed / 1024 ))
    else
        dl_speed=0
        ul_speed=0
    fi
    
    # Get network type
    network_type=$(dumpsys telephony.registry | grep mServiceState | grep -oE "data=.[0-9]+" | cut -d= -f2)
    case $network_type in
        0) network_type="Unknown";;
        1) network_type="GPRS";;
        2) network_type="EDGE";;
        3) network_type="UMTS";;
        4) network_type="CDMA";;
        5) network_type="EVDO";;
        6) network_type="EVDO";;
        7) network_type="1xRTT";;
        8) network_type="HSDPA";;
        9) network_type="HSUPA";;
        10) network_type="HSPA";;
        11) network_type="iDen";;
        12) network_type="EVDO_B";;
        13) network_type="LTE";;
        14) network_type="eHRPD";;
        15) network_type="HSPA+";;
        16) network_type="GSM";;
        17) network_type="TD_SCDMA";;
        18) network_type="IWLAN";;
        19) network_type="LTE_CA";;
        *) network_type="Unknown";;
    esac
    
    # Get signal strength
    signal_strength=$(dumpsys telephony.registry | grep mSignalStrength | grep -oE "[0-9]+" | head -1)
    
    # Get TCP connections
    tcp_connections=$(busybox netstat -tn | busybox grep -c ESTABLISHED)
    
    # Get latency (ping google DNS)
    latency=$(ping -c 1 8.8.8.8 | grep "time=" | cut -d= -f2 | cut -d" " -f1 || echo "0")
    
    # Store current values for next run
    last_rx_bytes=$rx_bytes
    last_tx_bytes=$tx_bytes
    last_time=$current_time
    
    echo "$(timestamp) DL: ${dl_speed}Kbps UL: ${ul_speed}Kbps Type: $network_type Signal: $signal_strength TCP: $tcp_connections Latency: ${latency}ms" >> $LOG_FILE
    
    # Insert into database
    curl -s -X POST "http://$DB_HOST/api.php" \
    -H "Content-Type: application/x-www-form-urlencoded" \
    -d "action=network_stats&download_speed=$dl_speed&upload_speed=$ul_speed&download_total=$rx_bytes&upload_total=$tx_bytes&network_type=$network_type&signal_strength=$signal_strength&tcp_connections=$tcp_connections&latency=$latency"
}

# Function to get ISP info
get_isp_info() {
    ip_address=$(curl -s ifconfig.me)
    isp_info=$(curl -s ipinfo.io/$ip_address)
    
    isp_name=$(echo "$isp_info" | grep '"org":' | cut -d'"' -f4)
    city=$(echo "$isp_info" | grep '"city":' | cut -d'"' -f4)
    country=$(echo "$isp_info" | grep '"country":' | cut -d'"' -f4)
    
    # Insert into database
    curl -s -X POST "http://$DB_HOST/api.php" \
    -H "Content-Type: application/x-www-form-urlencoded" \
    -d "action=isp_info&ip_address=$ip_address&isp_name=$isp_name&city=$city&country=$country"
}

# Function to get device info
get_device_info() {
    model=$(getprop ro.product.model)
    android_version=$(getprop ro.build.version.release)
    kernel_version=$(uname -r)
    root_status=$(which su >/dev/null && echo "Rooted" || echo "Not Rooted")
    battery_level=$(dumpsys battery | grep level | awk '{print $2}')
    cpu_usage=$(top -n 1 | grep -i cpu | awk '{print $2}' | cut -d'%' -f1)
    memory_usage=$(free | grep Mem | awk '{print $3/$2 * 100.0}')
    storage_free=$(df /data | tail -1 | awk '{print $4 * 1024}')
    
    # Insert into database
    curl -s -X POST "http://$DB_HOST/api.php" \
    -H "Content-Type: application/x-www-form-urlencoded" \
    -d "action=device_info&model=$model&android_version=$android_version&kernel_version=$kernel_version&root_status=$root_status&battery_level=$battery_level&cpu_usage=$cpu_usage&memory_usage=$memory_usage&storage_free=$storage_free"
}

# Main loop
while true; do
    current_time=$(date +%s)
    
    get_network_stats
    [ $((current_time % 60)) -eq 0 ] && get_isp_info
    [ $((current_time % 300)) -eq 0 ] && get_device_info
    
    sleep 1
done