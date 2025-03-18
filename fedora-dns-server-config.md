I understand there's still an issue with the setup. Let me provide a completely revised, comprehensive configuration that should work without errors. I'll check everything carefully and provide the full working configuration.

## DNS Server Configuration

### 1. /etc/named.conf
```
options {
    listen-on port 53 { 127.0.0.1; 192.168.1.10; };
    listen-on-v6 port 53 { ::1; };
    directory     "/var/named";
    dump-file     "/var/named/data/cache_dump.db";
    statistics-file "/var/named/data/named_stats.txt";
    memstatistics-file "/var/named/data/named_mem_stats.txt";
    recursing-file "/var/named/data/named.recursing";
    allow-query     { any; };
    allow-transfer  { none; };
    recursion yes;
    dnssec-validation no;
    dnssec-enable no;
    pid-file "/run/named/named.pid";
    session-keyfile "/run/named/session.key";
};

key "ddns-key" {
    algorithm hmac-md5;
    secret "abcdefghijklmnopqrstuvwxyz123456";
};

zone "est.intra" {
    type master;
    file "est.intra.zone";
    allow-update { key ddns-key; };
    notify no;
};

zone "1.168.192.in-addr.arpa" {
    type master;
    file "192.168.1.rev";
    allow-update { key ddns-key; };
    notify no;
};
```

### 2. /var/named/est.intra.zone
```
$TTL 86400
@       IN SOA  dns.est.intra. root.est.intra. (
                2023011010      ; Serial
                3600            ; Refresh
                1800            ; Retry
                604800          ; Expire
                86400 )         ; Minimum TTL

@       IN NS   dns.est.intra.
dns     IN A    192.168.1.10
```

### 3. /var/named/192.168.1.rev
```
$TTL 86400
@       IN SOA  dns.est.intra. root.est.intra. (
                2023011010      ; Serial
                3600            ; Refresh
                1800            ; Retry
                604800          ; Expire
                86400 )         ; Minimum TTL

@       IN NS   dns.est.intra.
10      IN PTR  dns.est.intra.
```

## DHCP Server Configuration

### 4. /etc/dhcp/dhcpd.conf
```
# Global options
default-lease-time 3600;
max-lease-time 86400;
authoritative;
log-facility local7;

# DDNS update settings
ddns-updates on;
ddns-update-style interim;
ignore client-updates;

# DDNS key - MUST match the one in named.conf
key "ddns-key" {
    algorithm hmac-md5;
    secret "abcdefghijklmnopqrstuvwxyz123456";
};

# DNS zones for updates
zone est.intra. {
    primary 127.0.0.1;
    key ddns-key;
}

zone 1.168.192.in-addr.arpa. {
    primary 127.0.0.1;
    key ddns-key;
}

# Network configuration
option domain-name "est.intra";
option domain-name-servers 192.168.1.10;
option routers 192.168.1.1;

# Subnet declaration
subnet 192.168.1.0 netmask 255.255.255.0 {
    range 192.168.1.50 192.168.1.150;
    option broadcast-address 192.168.1.255;
    
    # Important for client hostname
    option host-name "client";
    ddns-hostname "client";
    ddns-domain-name = "est.intra";
}
```

### 5. /etc/sysconfig/dhcpd
```
# Use your actual network interface here
DHCPDARGS=enp0s8
```

## Setup Script

Here's a complete setup script that will configure everything properly:

```bash
#!/bin/bash

# Stop services first
systemctl stop named
systemctl stop dhcpd

# Install necessary packages if not already installed
dnf install -y bind bind-utils dhcp-server

# Create configuration files
cat > /etc/named.conf << 'EOF'
options {
    listen-on port 53 { 127.0.0.1; 192.168.1.10; };
    listen-on-v6 port 53 { ::1; };
    directory     "/var/named";
    dump-file     "/var/named/data/cache_dump.db";
    statistics-file "/var/named/data/named_stats.txt";
    memstatistics-file "/var/named/data/named_mem_stats.txt";
    recursing-file "/var/named/data/named.recursing";
    allow-query     { any; };
    allow-transfer  { none; };
    recursion yes;
    dnssec-validation no;
    dnssec-enable no;
    pid-file "/run/named/named.pid";
    session-keyfile "/run/named/session.key";
};

key "ddns-key" {
    algorithm hmac-md5;
    secret "abcdefghijklmnopqrstuvwxyz123456";
};

zone "est.intra" {
    type master;
    file "est.intra.zone";
    allow-update { key ddns-key; };
    notify no;
};

zone "1.168.192.in-addr.arpa" {
    type master;
    file "192.168.1.rev";
    allow-update { key ddns-key; };
    notify no;
};
EOF

cat > /var/named/est.intra.zone << 'EOF'
$TTL 86400
@       IN SOA  dns.est.intra. root.est.intra. (
                2023011010      ; Serial
                3600            ; Refresh
                1800            ; Retry
                604800          ; Expire
                86400 )         ; Minimum TTL

@       IN NS   dns.est.intra.
dns     IN A    192.168.1.10
EOF

cat > /var/named/192.168.1.rev << 'EOF'
$TTL 86400
@       IN SOA  dns.est.intra. root.est.intra. (
                2023011010      ; Serial
                3600            ; Refresh
                1800            ; Retry
                604800          ; Expire
                86400 )         ; Minimum TTL

@       IN NS   dns.est.intra.
10      IN PTR  dns.est.intra.
EOF

cat > /etc/dhcp/dhcpd.conf << 'EOF'
# Global options
default-lease-time 3600;
max-lease-time 86400;
authoritative;
log-facility local7;

# DDNS update settings
ddns-updates on;
ddns-update-style interim;
ignore client-updates;

# DDNS key - MUST match the one in named.conf
key "ddns-key" {
    algorithm hmac-md5;
    secret "abcdefghijklmnopqrstuvwxyz123456";
};

# DNS zones for updates
zone est.intra. {
    primary 127.0.0.1;
    key ddns-key;
}

zone 1.168.192.in-addr.arpa. {
    primary 127.0.0.1;
    key ddns-key;
}

# Network configuration
option domain-name "est.intra";
option domain-name-servers 192.168.1.10;
option routers 192.168.1.1;

# Subnet declaration
subnet 192.168.1.0 netmask 255.255.255.0 {
    range 192.168.1.50 192.168.1.150;
    option broadcast-address 192.168.1.255;
    
    # Important for client hostname
    option host-name "client";
    ddns-hostname "client";
    ddns-domain-name = "est.intra";
}
EOF

# Create interface config (replace enp0s8 with your actual interface)
echo 'DHCPDARGS=enp0s8' > /etc/sysconfig/dhcpd

# Set proper permissions
chown named:named /var/named/est.intra.zone
chown named:named /var/named/192.168.1.rev
chmod 664 /var/named/est.intra.zone
chmod 664 /var/named/192.168.1.rev

# Disable SELinux temporarily
setenforce 0

# Set SELinux contexts
setsebool -P named_write_master_zones 1
restorecon -rv /var/named
restorecon -v /etc/named.conf
restorecon -v /etc/dhcp/dhcpd.conf

# Open firewall ports
firewall-cmd --permanent --add-service=dns
firewall-cmd --permanent --add-service=dhcp
firewall-cmd --reload

# Start and enable services
systemctl enable named
systemctl enable dhcpd
systemctl start named
systemctl start dhcpd

# Check service status
echo "Named status:"
systemctl status named
echo "DHCPD status:"
systemctl status dhcpd

# Test DNS configuration
echo "Testing DNS configuration:"
dig @127.0.0.1 dns.est.intra
```

## Key Changes

1. **Simplified key name**: Using "ddns-key" instead of "ddns-key.est.intra" to avoid any confusion
2. **Fixed reverse zone file naming**: Using "192.168.1.rev" for clarity
3. **Using 127.0.0.1 for DDNS updates**: This ensures the DHCP server updates the DNS server locally
4. **Improved permissions and contexts**
5. **Disabled SELinux** initially to eliminate that as a source of problems
6. **Added more allowing query settings**: Setting "allow-query { any; }" to ensure DNS queries are accepted

After running this setup, all clients connecting via DHCP should be assigned the domain name "client.est.intra" automatically.
