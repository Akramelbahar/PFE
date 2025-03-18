DNS Configuration (BIND):

Main config: /etc/named.conf
Forward zone file: /var/named/est.intra.zone
Reverse zone file: /var/named/est.intra.rev
DDNS key (if separate): /etc/named/ddns-key.est.intra
Log files: /var/log/named.log or /var/named/data/named.run


DHCP Configuration:

Main config: /etc/dhcp/dhcpd.conf
Interface settings: /etc/sysconfig/dhcpd
Leases database: /var/lib/dhcpd/dhcpd.leases
Log files: /var/log/messages or through journalctl


I'll provide you with a complete set of fixed configuration files that should properly add DNS records for DHCP clients.

## 1. DNS Server Configuration (/etc/named.conf)

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
    journal "est.intra.zone.jnl";
};

zone "1.168.192.in-addr.arpa" {
    type master;
    file "192.168.1.rev";
    allow-update { key ddns-key; };
    notify no;
    journal "192.168.1.rev.jnl";
};

logging {
    channel default_debug {
        file "/var/log/named.log";
        severity dynamic;
        print-time yes;
    };
    category update { default_debug; };
    category security { default_debug; };
};
```

## 2. Forward Zone File (/var/named/est.intra.zone)

```
$TTL 86400
@       IN SOA  dns.est.intra. root.est.intra. (
                2023011012      ; Serial
                3600            ; Refresh
                1800            ; Retry
                604800          ; Expire
                86400 )         ; Minimum TTL

@       IN NS   dns.est.intra.
dns     IN A    192.168.1.10
client  IN A    192.168.1.108
```

## 3. Reverse Zone File (/var/named/192.168.1.rev)

```
$TTL 86400
@       IN SOA  dns.est.intra. root.est.intra. (
                2023011012      ; Serial
                3600            ; Refresh
                1800            ; Retry
                604800          ; Expire
                86400 )         ; Minimum TTL

@       IN NS   dns.est.intra.
10      IN PTR  dns.est.intra.
108     IN PTR  client.est.intra.
```

## 4. DHCP Configuration (/etc/dhcp/dhcpd.conf)

```
# Global options
default-lease-time 3600;
max-lease-time 86400;
authoritative;
log-facility local7;

# DDNS update settings
ddns-updates on;
ddns-update-style interim;
update-static-leases on;
do-forward-updates on;
update-optimization off;

# DDNS key
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

# Subnet declaration
subnet 192.168.1.0 netmask 255.255.255.0 {
    range 192.168.1.50 192.168.1.150;
    option routers 192.168.1.1;
    option broadcast-address 192.168.1.255;
    
    # Host configuration
    option host-name "client";
    ddns-hostname "client";
    ddns-domain-name = "est.intra";
}
```

## 5. DHCP Interface Configuration (/etc/sysconfig/dhcpd)

```
# Use your actual network interface name
DHCPDARGS=enp0s8
```

## 6. Setup Commands

Execute these commands to apply all configurations correctly:

```bash
# Create log file
sudo mkdir -p /var/log
sudo touch /var/log/named.log
sudo chown named:named /var/log/named.log
sudo chmod 664 /var/log/named.log

# Set permissions for DNS files
sudo chown named:named /var/named/est.intra.zone
sudo chown named:named /var/named/192.168.1.rev
sudo chmod 664 /var/named/est.intra.zone
sudo chmod 664 /var/named/192.168.1.rev

# Remove any existing journal files
sudo rm -f /var/named/*.jnl

# Disable SELinux temporarily
sudo setenforce 0

# Set SELinux contexts
sudo setsebool -P named_write_master_zones 1
sudo restorecon -rv /var/named
sudo restorecon -v /etc/named.conf
sudo restorecon -v /etc/dhcp/dhcpd.conf
sudo restorecon -v /var/log/named.log

# Restart services
sudo systemctl restart named
sudo systemctl restart dhcpd

# Check if services are running
sudo systemctl status named
sudo systemctl status dhcpd

# Open firewall ports
sudo firewall-cmd --permanent --add-service=dns
sudo firewall-cmd --permanent --add-service=dhcp
sudo firewall-cmd --reload
```

## 7. Testing Commands

After applying all changes, test your configuration:

```bash
# On the client, release and renew IP
sudo dhclient -r
sudo dhclient

# On the server, check logs
sudo tail -f /var/log/named.log
sudo journalctl -u dhcpd -f

# Test forward lookup
dig @192.168.1.10 client.est.intra

# Test reverse lookup
dig @192.168.1.10 -x 192.168.1.108
```

These configurations include several key improvements:
- Added journal files for both zones
- Included explicit logging
- Added both forward and reverse records in the zone files
- Added multiple DDNS options to ensure updates work
- Disabled SELinux temporarily to eliminate permission issues

If you're still having problems after implementing these changes, you might need to restart the entire system and check the hardware configuration of your network.
zone "est.intra" {
    type master;
    file "est.intra.zone";
    allow-update { key ddns-key; };
    journal "est.intra.zone.jnl";
    update-policy local;  # Add this line
};
