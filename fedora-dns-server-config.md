# Complete Fedora DNS Server Configuration Guide

This guide provides comprehensive instructions for setting up a DNS server on Fedora with domain `intra.ests.com` and the IP address `192.168.1.10`.

## Initial System Setup

### Install Fedora

1. Create a new VM in VirtualBox:
   - 2 GB RAM minimum
   - 20 GB disk space
   - Network adapter set to "Internal Network" (name it "intranet")

2. Install Fedora from ISO
   - During installation, set hostname to `dns.intra.ests.com`
   - Create a user account as needed

### Configure Static IP Address

After installation and login:

```bash
# Create/edit network configuration file
sudo nano /etc/NetworkManager/system-connections/enp0s3.nmconnection
```

Add these contents (replace enp0s3 with your actual interface name if different):

```
[connection]
id=enp0s3
type=ethernet
interface-name=enp0s3
autoconnect=true

[ethernet]
mac-address=<Your MAC address>

[ipv4]
method=manual
addresses=192.168.1.10/24
gateway=192.168.1.1
dns=127.0.0.1

[ipv6]
method=disabled
```

Save and apply the changes:

```bash
sudo chmod 600 /etc/NetworkManager/system-connections/enp0s3.nmconnection
sudo nmcli connection reload
sudo nmcli connection down enp0s3 && sudo nmcli connection up enp0s3
```

Verify your IP address:

```bash
ip addr show
```

## DNS Server Installation and Configuration

### Install BIND DNS Server

```bash
# Update the system
sudo dnf update -y

# Install BIND and utilities
sudo dnf install bind bind-utils -y
```

### Configure BIND DNS Server

1. Backup the original configuration:

```bash
sudo cp /etc/named.conf /etc/named.conf.backup
```

2. Edit the main configuration file:

```bash
sudo nano /etc/named.conf
```

Replace the contents with:

```
// named.conf
options {
        listen-on port 53 { 127.0.0.1; 192.168.1.10; };
        listen-on-v6 port 53 { ::1; };
        directory       "/var/named";
        dump-file       "/var/named/data/cache_dump.db";
        statistics-file "/var/named/data/named_stats.txt";
        memstatistics-file "/var/named/data/named_mem_stats.txt";
        secroots-file   "/var/named/data/named.secroots";
        recursing-file  "/var/named/data/named.recursing";

        // Allow queries from local network
        allow-query     { localhost; 192.168.1.0/24; };
        
        // Enable recursion for clients
        recursion yes;
        
        // Forward DNS queries to these public DNS servers
        forwarders {
                1.1.1.1;
                8.8.8.8;
        };
        forward first;

        dnssec-validation yes;
        managed-keys-directory "/var/named/dynamic";
        pid-file "/run/named/named.pid";
        session-keyfile "/run/named/session.key";
};

logging {
        channel default_debug {
                file "data/named.run";
                severity dynamic;
        };
};

// Root hint zone
zone "." IN {
        type hint;
        file "named.ca";
};

// Forward zone for intra.ests.com
zone "intra.ests.com" IN {
        type master;
        file "intra.ests.com.zone";
        allow-update { none; };
};

// Reverse zone for 192.168.1.0/24
zone "1.168.192.in-addr.arpa" IN {
        type master;
        file "1.168.192.zone";
        allow-update { none; };
};

// Include standard zones
include "/etc/named.rfc1912.zones";
include "/etc/named.root.key";
```

### Create Zone Files

1. Create the forward zone file:

```bash
sudo nano /var/named/intra.ests.com.zone
```

Add:

```
$TTL 86400
@       IN SOA  dns.intra.ests.com. admin.intra.ests.com. (
                2023031201      ; Serial (YYYYMMDDNN format)
                3600            ; Refresh (1 hour)
                1800            ; Retry (30 minutes)
                604800          ; Expire (1 week)
                86400 )         ; Minimum TTL (1 day)

; Name Servers
@       IN NS   dns.intra.ests.com.

; A Records
@       IN A    192.168.1.10
dns     IN A    192.168.1.10
client  IN A    192.168.1.100

; CNAME Records
www     IN CNAME        @
mail    IN CNAME        @
```

2. Create the reverse zone file:

```bash
sudo nano /var/named/1.168.192.zone
```

Add:

```
$TTL 86400
@       IN SOA  dns.intra.ests.com. admin.intra.ests.com. (
                2023031201      ; Serial (YYYYMMDDNN format)
                3600            ; Refresh (1 hour)
                1800            ; Retry (30 minutes)
                604800          ; Expire (1 week)
                86400 )         ; Minimum TTL (1 day)

; Name Servers
@       IN NS   dns.intra.ests.com.

; PTR Records
10      IN PTR  dns.intra.ests.com.
100     IN PTR  client.intra.ests.com.
```

3. Set correct permissions:

```bash
sudo chown named:named /var/named/intra.ests.com.zone
sudo chown named:named /var/named/1.168.192.zone
sudo chmod 640 /var/named/intra.ests.com.zone
sudo chmod 640 /var/named/1.168.192.zone
```

## Start and Enable the DNS Service

1. Check configuration syntax:

```bash
sudo named-checkconf /etc/named.conf
```

2. Check zone file syntax:

```bash
sudo named-checkzone intra.ests.com /var/named/intra.ests.com.zone
sudo named-checkzone 1.168.192.in-addr.arpa /var/named/1.168.192.zone
```

3. Start and enable the BIND service:

```bash
sudo systemctl start named
sudo systemctl enable named
sudo systemctl status named
```

## Configure Firewall

Allow DNS traffic through the firewall:

```bash
sudo firewall-cmd --permanent --add-service=dns
sudo firewall-cmd --reload
sudo firewall-cmd --list-all
```

## Test DNS Server Configuration

1. Test forward resolution from the DNS server:

```bash
# Using dig
dig dns.intra.ests.com @localhost
dig client.intra.ests.com @localhost

# Using nslookup
nslookup dns.intra.ests.com
nslookup client.intra.ests.com
```

2. Test reverse resolution:

```bash
dig -x 192.168.1.10 @localhost
dig -x 192.168.1.100 @localhost

nslookup 192.168.1.10
nslookup 192.168.1.100
```

3. Test external DNS resolution:

```bash
dig google.com @localhost
```

## Troubleshooting

If you encounter issues:

1. Check BIND service status:
```bash
sudo systemctl status named
```

2. Check logs for errors:
```bash
sudo journalctl -u named
```

3. Verify zone file permissions:
```bash
ls -la /var/named/
```

4. Confirm SELinux is not blocking the service:
```bash
sudo ausearch -m avc -ts recent
```

5. If SELinux is causing issues, you can set the correct context:
```bash
sudo restorecon -rv /var/named
```

## Additional Configuration (Optional)

### Configure DNS Forwarding for Internet Access

If needed, ensure forwarding is properly configured in `/etc/named.conf`:

```
options {
    ...
    forwarders {
        1.1.1.1;  // Cloudflare
        8.8.8.8;  // Google
    };
    forward first;
    ...
};
```

### Add DHCP Support

If you want the DNS server to also provide DHCP:

```bash
sudo dnf install dhcp-server -y
sudo nano /etc/dhcp/dhcpd.conf
```

Add:

```
option domain-name "intra.ests.com";
option domain-name-servers 192.168.1.10;

default-lease-time 600;
max-lease-time 7200;

subnet 192.168.1.0 netmask 255.255.255.0 {
  range 192.168.1.50 192.168.1.200;
  option routers 192.168.1.1;
}
```

Start and enable DHCP:

```bash
sudo systemctl start dhcpd
sudo systemctl enable dhcpd
sudo firewall-cmd --add-service=dhcp --permanent
sudo firewall-cmd --reload
```
