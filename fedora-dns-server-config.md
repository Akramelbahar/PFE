# Complete Network Setup Guide: LAN, DMZ, and Relay Agent

I'll provide a comprehensive step-by-step guide to create your network architecture with Fedora servers. This guide covers all necessary configurations without skipping any steps.

## Overview of Network Architecture
- **LAN Network**: 192.168.1.0/24
- **DMZ Network**: 192.168.2.0/24
- **Relay Agent**: Connects both networks (interfaces: enp0s8 and enp0s9)
- **DMZ Server (192.168.2.1)**: DHCP, DNS (dns.est.intra), Web Server (www.est.intra)
- **LAN Client**: Obtains IP via DHCP, resolves via dns.est.intra, accesses www.est.intra

## Step 1: Set Up DMZ Server (192.168.2.1)

### 1.1. Basic Server Setup
```bash
# Update the system
sudo dnf update -y

# Set hostname
sudo hostnamectl set-hostname dmz-server.est.intra

# Configure networking for static IP
sudo nmcli connection add type ethernet con-name dmz-network ifname enp0s8 ip4 192.168.2.1/24
sudo nmcli connection modify dmz-network ipv4.method manual
sudo nmcli connection up dmz-network
```

### 1.2. DHCP Server Setup
```bash
# Install DHCP server
sudo dnf install -y dhcp-server

# Configure DHCP server
sudo cp /etc/dhcp/dhcpd.conf /etc/dhcp/dhcpd.conf.bak
sudo nano /etc/dhcp/dhcpd.conf
```

Add this content to dhcpd.conf:
```
# Global DHCP configuration
ddns-update-style interim;
ddns-domainname "est.intra.";
ddns-rev-domainname "in-addr.arpa.";
ddns-updates on;
ignore client-updates;
update-static-leases on;

# Authorization
allow booting;
allow bootp;

# DHCP for LAN subnet
subnet 192.168.1.0 netmask 255.255.255.0 {
  range 192.168.1.100 192.168.1.200;
  option routers 192.168.1.10;
  option domain-name "est.intra";
  option domain-name-servers 192.168.2.1;
  default-lease-time 3600;
  max-lease-time 7200;
}

# DHCP for DMZ subnet
subnet 192.168.2.0 netmask 255.255.255.0 {
  range 192.168.2.100 192.168.2.200;
  option routers 192.168.2.10;
  option domain-name "est.intra";
  option domain-name-servers 192.168.2.1;
  default-lease-time 3600;
  max-lease-time 7200;
}
```

Start and enable the DHCP service:
```bash
sudo systemctl start dhcpd
sudo systemctl enable dhcpd
sudo systemctl status dhcpd
```

### 1.3. DNS Server Setup
```bash
# Install DNS server
sudo dnf install -y bind bind-utils

# Configure main DNS configuration
sudo cp /etc/named.conf /etc/named.conf.bak
sudo nano /etc/named.conf
```

Replace the content with:
```
options {
    listen-on port 53 { 127.0.0.1; 192.168.2.1; };
    listen-on-v6 port 53 { ::1; };
    directory "/var/named";
    dump-file "/var/named/data/cache_dump.db";
    statistics-file "/var/named/data/named_stats.txt";
    memstatistics-file "/var/named/data/named_mem_stats.txt";
    secroots-file "/var/named/data/named.secroots";
    recursing-file "/var/named/data/named.recursing";
    
    allow-query { localhost; 192.168.1.0/24; 192.168.2.0/24; };
    allow-transfer { localhost; };
    
    recursion yes;
    dnssec-enable yes;
    dnssec-validation yes;
    
    /* For DDNS */
    allow-update { 192.168.2.1; };
    
    managed-keys-directory "/var/named/dynamic";
    pid-file "/run/named/named.pid";
    session-keyfile "/run/named/session.key";
    
    /* https://fedoraproject.org/wiki/Changes/CryptoPolicy */
    include "/etc/crypto-policies/back-ends/bind.config";
};

logging {
    channel default_debug {
        file "data/named.run";
        severity dynamic;
    };
};

zone "." IN {
    type hint;
    file "named.ca";
};

include "/etc/named.rfc1912.zones";
include "/etc/named.root.key";

/* Forward zone for est.intra */
zone "est.intra" IN {
    type master;
    file "est.intra.zone";
    allow-update { 192.168.2.1; };
};

/* Reverse zone for 192.168.1.0/24 */
zone "1.168.192.in-addr.arpa" IN {
    type master;
    file "1.168.192.in-addr.arpa.zone";
    allow-update { 192.168.2.1; };
};

/* Reverse zone for 192.168.2.0/24 */
zone "2.168.192.in-addr.arpa" IN {
    type master;
    file "2.168.192.in-addr.arpa.zone";
    allow-update { 192.168.2.1; };
};
```

Create forward zone file:
```bash
sudo nano /var/named/est.intra.zone
```

Add this content:
```
$TTL 86400
@       IN      SOA     dns.est.intra. admin.est.intra. (
                        2024031901      ; Serial
                        3600            ; Refresh
                        1800            ; Retry
                        604800          ; Expire
                        86400 )         ; Minimum TTL

@       IN      NS      dns.est.intra.
@       IN      A       192.168.2.1

dns     IN      A       192.168.2.1
www     IN      A       192.168.2.1
relay   IN      A       192.168.2.10
lan-relay IN    A       192.168.1.10
```

Create reverse zone for 192.168.1.0/24:
```bash
sudo nano /var/named/1.168.192.in-addr.arpa.zone
```

Add this content:
```
$TTL 86400
@       IN      SOA     dns.est.intra. admin.est.intra. (
                        2024031901      ; Serial
                        3600            ; Refresh
                        1800            ; Retry
                        604800          ; Expire
                        86400 )         ; Minimum TTL

@       IN      NS      dns.est.intra.
10      IN      PTR     lan-relay.est.intra.
```

Create reverse zone for 192.168.2.0/24:
```bash
sudo nano /var/named/2.168.192.in-addr.arpa.zone
```

Add this content:
```
$TTL 86400
@       IN      SOA     dns.est.intra. admin.est.intra. (
                        2024031901      ; Serial
                        3600            ; Refresh
                        1800            ; Retry
                        604800          ; Expire
                        86400 )         ; Minimum TTL

@       IN      NS      dns.est.intra.
1       IN      PTR     dns.est.intra.
1       IN      PTR     www.est.intra.
10      IN      PTR     relay.est.intra.
```

Set proper permissions:
```bash
sudo chown named:named /var/named/est.intra.zone
sudo chown named:named /var/named/1.168.192.in-addr.arpa.zone
sudo chown named:named /var/named/2.168.192.in-addr.arpa.zone
sudo chmod 644 /var/named/est.intra.zone
sudo chmod 644 /var/named/1.168.192.in-addr.arpa.zone
sudo chmod 644 /var/named/2.168.192.in-addr.arpa.zone
```

Start and enable DNS service:
```bash
sudo systemctl start named
sudo systemctl enable named
sudo systemctl status named
```

Verify DNS configuration:
```bash
sudo named-checkconf
sudo named-checkzone est.intra /var/named/est.intra.zone
sudo named-checkzone 1.168.192.in-addr.arpa /var/named/1.168.192.in-addr.arpa.zone
sudo named-checkzone 2.168.192.in-addr.arpa /var/named/2.168.192.in-addr.arpa.zone
```

### 1.4. Web Server Setup
```bash
# Install Apache, PHP, and MariaDB
sudo dnf install -y httpd php php-mysqlnd mariadb-server

# Start and enable MariaDB
sudo systemctl start mariadb
sudo systemctl enable mariadb

# Secure MariaDB installation (follow the prompts)
sudo mysql_secure_installation

# Configure Apache
sudo nano /etc/httpd/conf.d/est.intra.conf
```

Add this content:
```
<VirtualHost *:80>
    ServerName www.est.intra
    ServerAlias www.est.intra
    DocumentRoot /var/www/html/est.intra
    ErrorLog /var/log/httpd/est.intra-error.log
    CustomLog /var/log/httpd/est.intra-access.log combined
    
    <Directory /var/www/html/est.intra>
        Options Indexes FollowSymLinks
        AllowOverride All
        Require all granted
    </Directory>
</VirtualHost>
```

Create web content:
```bash
sudo mkdir -p /var/www/html/est.intra
sudo nano /var/www/html/est.intra/index.php
```

Add this PHP code:
```php
<?php
echo "<h1>Welcome to www.est.intra!</h1>";
echo "<p>Server is running on " . $_SERVER['SERVER_ADDR'] . "</p>";
echo "<p>Your IP address is: " . $_SERVER['REMOTE_ADDR'] . "</p>";
echo "<h2>PHP Information</h2>";
phpinfo();
?>
```

Set proper permissions:
```bash
sudo chown -R apache:apache /var/www/html/est.intra
sudo restorecon -R /var/www/html/est.intra
```

Start and enable Apache:
```bash
sudo systemctl start httpd
sudo systemctl enable httpd
sudo systemctl status httpd
```

### 1.5. Firewall Configuration for DMZ Server
```bash
# Allow necessary services through the firewall
sudo firewall-cmd --permanent --add-service=dns
sudo firewall-cmd --permanent --add-service=dhcp
sudo firewall-cmd --permanent --add-service=http
sudo firewall-cmd --reload
```

## Step 2: Set Up Relay Agent

### 2.1. Basic Server Setup
```bash
# Update the system
sudo dnf update -y

# Set hostname
sudo hostnamectl set-hostname relay.est.intra

# Configure network interfaces
# First interface (LAN - 192.168.1.10)
sudo nmcli connection add type ethernet con-name lan-network ifname enp0s8 ip4 192.168.1.10/24
sudo nmcli connection modify lan-network ipv4.method manual

# Second interface (DMZ - 192.168.2.10)
sudo nmcli connection add type ethernet con-name dmz-network ifname enp0s9 ip4 192.168.2.10/24
sudo nmcli connection modify dmz-network ipv4.method manual

# Activate connections
sudo nmcli connection up lan-network
sudo nmcli connection up dmz-network
```

### 2.2. Configure IP Forwarding
```bash
# Enable IP forwarding
sudo sysctl -w net.ipv4.ip_forward=1
sudo echo "net.ipv4.ip_forward=1" >> /etc/sysctl.conf
sudo sysctl -p
```

### 2.3. Install and Configure DHCP Relay
```bash
# Install DHCP relay
sudo dnf install -y dhcp-relay

# Configure DHCP relay
sudo nano /etc/sysconfig/dhcrelay
```

Add this content:
```
# Point to the DHCP server and specify interfaces
DHCRELAY_OPTS="-d --no-pid -i enp0s8 -i enp0s9 192.168.2.1"
```

Start and enable DHCP relay:
```bash
sudo systemctl daemon-reload
sudo systemctl start dhcrelay
sudo systemctl enable dhcrelay
sudo systemctl status dhcrelay
```

### 2.4. Firewall Configuration for Relay Agent
```bash
# Configure firewall to allow traffic between networks
sudo firewall-cmd --permanent --direct --add-rule ipv4 filter FORWARD 0 -i enp0s8 -o enp0s9 -j ACCEPT
sudo firewall-cmd --permanent --direct --add-rule ipv4 filter FORWARD 0 -i enp0s9 -o enp0s8 -j ACCEPT

# Allow DHCP traffic
sudo firewall-cmd --permanent --add-service=dhcp
sudo firewall-cmd --reload
```

## Step 3: Set Up LAN Client

### 3.1. Basic Client Setup
```bash
# Update the system
sudo dnf update -y

# Set hostname
sudo hostnamectl set-hostname client.est.intra

# Configure network to use DHCP
sudo nmcli connection add type ethernet con-name lan-client ifname enp0s8
sudo nmcli connection modify lan-client ipv4.method auto
sudo nmcli connection up lan-client
```

## Step 4: Testing the Setup

### 4.1. Test DNS Resolution on DMZ Server
```bash
# Verify DNS is working
dig dns.est.intra @192.168.2.1
dig www.est.intra @192.168.2.1
dig -x 192.168.2.1 @192.168.2.1
```

### 4.2. Test DHCP on LAN Client
```bash
# Verify client received IP from DHCP
ip addr show
cat /etc/resolv.conf
```

### 4.3. Test DNS Resolution on LAN Client
```bash
# Test DNS resolution from client
nslookup dns.est.intra
nslookup www.est.intra
```

### 4.4. Test Web Access on LAN Client
```bash
# Test access to web server
curl www.est.intra
```

Or open a browser and navigate to http://www.est.intra

## Troubleshooting Tips

### If DHCP Relay Fails
```bash
# Check status and logs
sudo systemctl status dhcrelay
sudo journalctl -xe -u dhcrelay

# Verify interface names
ip addr show
```

### If DNS Resolution Fails
```bash
# Check DNS server status
sudo systemctl status named
sudo journalctl -xe -u named

# Test DNS resolution directly
dig dns.est.intra @192.168.2.1
```

### If Web Server Access Fails
```bash
# Check web server status
sudo systemctl status httpd
sudo journalctl -xe -u httpd

# Check SELinux status
sudo sestatus

# If SELinux is causing issues
sudo setenforce 0  # Temporarily disable
```

This comprehensive guide covers all the steps needed to set up your network architecture with LAN, DMZ, relay agent, DHCP, DNS, and web services. Follow these steps sequentially to ensure proper configuration.
