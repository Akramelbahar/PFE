I'll help you set up a relay agent in Fedora to connect two networks with the specifications you've provided. Let me outline the complete setup and configuration steps for your network architecture.

# Network Architecture Setup for Fedora

## Network Overview
- **LAN Network**: 192.168.1.0/24
- **DMZ Network**: 192.168.2.0/24
- **Relay Agent**: Connects both networks
  - LAN Interface: 192.168.1.10
  - DMZ Interface: 192.168.2.10
- **DMZ Server**: 192.168.2.1
  - Services: DHCP, DNS (dns.est.intra), Web Server (www.est.intra)
- **Client(s)**: In 192.168.1.0/24 network, obtaining IP via DHCP

## 1. Setting Up the Relay Agent

First, we need to configure the relay agent machine with two network interfaces:

```bash
# Install required packages
sudo dnf install dhcp-relay

# Configure the first network interface (LAN)
sudo nmcli connection add type ethernet con-name lan-network ifname eth0 ip4 192.168.1.10/24

# Configure the second network interface (DMZ)
sudo nmcli connection add type ethernet con-name dmz-network ifname eth1 ip4 192.168.2.10/24

# Enable IP forwarding (so the machine can route packets between networks)
# Specify both interfaces and the DHCP server IP
DHCRELAY_OPTS="-d --no-pid -i enp0s8 -i enp0s9 192.168.2.1"


sudo sysctl -p

# Configure DHCP relay to forward requests from LAN to DMZ server
sudo dnf install dhcp-relay
```

Now configure the DHCP relay by editing `/etc/dhcp/dhcrelay.conf`:

```
# Point to the DHCP server in DMZ
DHCRELAY_OPTS="-i eth0 -i eth1 192.168.2.1"
```

Start the DHCP relay service:

```bash
sudo systemctl enable --now dhcrelay
```

## 2. DMZ Server Configuration (192.168.2.1)

### 2.1 Basic Network Configuration

```bash
# Configure static IP for DMZ server
sudo nmcli connection add type ethernet con-name dmz-server ifname eth0 ip4 192.168.2.1/24
```

### 2.2 DHCP Server Configuration

```bash
# Install DHCP server
sudo dnf install dhcp-server

# Edit DHCP configuration
sudo nano /etc/dhcp/dhcpd.conf
```

Add the following configuration to `/etc/dhcp/dhcpd.conf`:

```
# Global DHCP configuration
ddns-update-style interim;
ddns-domainname "est.intra.";
ddns-rev-domainname "in-addr.arpa.";
ddns-updates on;
ignore client-updates;
update-static-leases on;

# DHCP for LAN
subnet 192.168.1.0 netmask 255.255.255.0 {
  range 192.168.1.100 192.168.1.200;
  option routers 192.168.1.10;
  option domain-name "est.intra";
  option domain-name-servers 192.168.2.1;
  default-lease-time 3600;
  max-lease-time 7200;
}

# DHCP for DMZ 
subnet 192.168.2.0 netmask 255.255.255.0 {
  range 192.168.2.100 192.168.2.200;
  option routers 192.168.2.10;
  option domain-name "est.intra";
  option domain-name-servers 192.168.2.1;
  default-lease-time 3600;
  max-lease-time 7200;
}
```

Enable and start the DHCP service:

```bash
sudo systemctl enable --now dhcpd
```

### 2.3 DNS Server Configuration

```bash
# Install DNS server packages
sudo dnf install bind bind-utils

# Configure the DNS service
sudo nano /etc/named.conf
```

Replace the default content with the following configuration:

```
options {
    listen-on port 53 { 127.0.0.1; 192.168.2.1; };
    listen-on-v6 port 53 { ::1; };
    directory "/var/named";
    dump-file "/var/named/data/cache_dump.db";
    statistics-file "/var/named/data/named_stats.txt";
    memstatistics-file "/var/named/data/named_mem_stats.txt";
    
    allow-query { localhost; 192.168.1.0/24; 192.168.2.0/24; };
    allow-transfer { localhost; };
    
    recursion yes;
    dnssec-validation yes;
    
    /* For DDNS */
    allow-update { 192.168.2.1; };
};

logging {
    channel default_debug {
        file "data/named.run";
        severity dynamic;
    };
};

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

Now, create the forward zone file:

```bash
sudo nano /var/named/est.intra.zone
```

Add the following content:

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

Create the reverse zone files:

```bash
sudo nano /var/named/1.168.192.in-addr.arpa.zone
```

Add the following content:

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

Next, create the second reverse zone file:

```bash
sudo nano /var/named/2.168.192.in-addr.arpa.zone
```

Add the following content:

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

Set proper permissions on the zone files:

```bash
sudo chown named:named /var/named/est.intra.zone
sudo chown named:named /var/named/1.168.192.in-addr.arpa.zone
sudo chown named:named /var/named/2.168.192.in-addr.arpa.zone
```

Enable and start the DNS service:

```bash
sudo systemctl enable --now named
```

Configure the firewall to allow DNS queries:

```bash
sudo firewall-cmd --permanent --add-service=dns
sudo firewall-cmd --reload
```

### 2.4 Web Server Configuration

```bash
# Install Apache, PHP and MySQL
sudo dnf install httpd php php-mysqlnd mariadb-server

# Start and enable MariaDB (MySQL)
sudo systemctl enable --now mariadb

# Secure the MariaDB installation
sudo mysql_secure_installation

# Start and enable Apache
sudo systemctl enable --now httpd
```

Configure the virtual hosts for www.est.intra:

```bash
sudo nano /etc/httpd/conf.d/est.intra.conf
```

Add the following content:

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

Create the document root directory and a sample PHP file:

```bash
sudo mkdir -p /var/www/html/est.intra
sudo nano /var/www/html/est.intra/index.php
```

Add a simple PHP script to test the setup:

```php
<?php
echo "<h1>Welcome to www.est.intra!</h1>";
echo "<p>Server is running on " . $_SERVER['SERVER_ADDR'] . "</p>";
echo "<h2>PHP Information</h2>";
phpinfo();
?>
```

Set the correct ownership:

```bash
sudo chown -R apache:apache /var/www/html/est.intra
```

Configure the firewall to allow HTTP:

```bash
sudo firewall-cmd --permanent --add-service=http
sudo firewall-cmd --reload
```

Restart Apache to apply the changes:

```bash
sudo systemctl restart httpd
```

## 3. Testing the Setup

Once all configurations are complete, you can test the setup:

1. From a client machine in the 192.168.1.0/24 network:
   - It should receive an IP address via DHCP
   - DNS resolution for dns.est.intra and www.est.intra should work
   - Web access to www.est.intra should display the PHP page

Test commands from a client:

```bash
# Check if DHCP assigned an IP address
ip addr

# Test DNS resolution
nslookup dns.est.intra
nslookup www.est.intra

# Test web access (via browser or curl)
curl www.est.intra
```

This setup creates a fully functioning network with a relay agent connecting the LAN and DMZ networks, with DNS, DHCP, and web services all running on the DMZ server.
