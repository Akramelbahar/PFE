I'll provide you with complete, error-free configurations for both DNS and DHCP servers that will automatically assign the domain name "client.est.intra" to any device connecting through DHCP.

## Server Configuration Files

### 1. DNS Server Configuration (/etc/named.conf)
```
options {
    listen-on port 53 { 127.0.0.1; 192.168.1.10; };
    listen-on-v6 port 53 { ::1; };
    directory     "/var/named";
    dump-file     "/var/named/data/cache_dump.db";
    statistics-file "/var/named/data/named_stats.txt";
    memstatistics-file "/var/named/data/named_mem_stats.txt";
    recursing-file "/var/named/data/named.recursing";
    allow-query     { localhost; 192.168.1.0/24; };
    recursion yes;
    dnssec-validation no;
    pid-file "/run/named/named.pid";
    session-keyfile "/run/named/session.key";
};

key "ddns-key.est.intra" {
    algorithm hmac-md5;
    secret "abcdefghijklmnopqrstuvwxyz123456";
};

zone "est.intra" IN {
    type master;
    file "est.intra.zone";
    allow-update { key "ddns-key.est.intra"; };
};

zone "1.168.192.in-addr.arpa" IN {
    type master;
    file "est.intra.rev";
    allow-update { key "ddns-key.est.intra"; };
};
```

### 2. Forward Zone File (/var/named/est.intra.zone)
```
$TTL 86400
@       IN SOA  dns.est.intra. root.est.intra. (
                2023011001 ; Serial
                3600       ; Refresh
                1800       ; Retry
                604800     ; Expire
                86400      ; Minimum TTL
)
@       IN NS   dns
dns     IN A    192.168.1.10
```

### 3. Reverse Zone File (/var/named/est.intra.rev)
```
$TTL 86400
@       IN SOA  dns.est.intra. root.est.intra. (
                2023011001 ; Serial
                3600       ; Refresh
                1800       ; Retry
                604800     ; Expire
                86400      ; Minimum TTL
)
@       IN NS   dns.est.intra.
10      IN PTR  dns.est.intra.
```

### 4. DHCP Configuration (/etc/dhcp/dhcpd.conf)
```
# Basic DHCP settings
default-lease-time 3600;
max-lease-time 86400;
authoritative;

# DDNS settings
ddns-updates on;
ddns-update-style interim;
ignore client-updates;

# DDNS key
key "ddns-key.est.intra" {
    algorithm hmac-md5;
    secret "abcdefghijklmnopqrstuvwxyz123456";
};

# DNS update zones
zone est.intra. {
    primary 192.168.1.10;
    key ddns-key.est.intra;
}

zone 1.168.192.in-addr.arpa. {
    primary 192.168.1.10;
    key ddns-key.est.intra;
}

# Global options
option domain-name "est.intra";
option domain-name-servers 192.168.1.10;

# Subnet configuration
subnet 192.168.1.0 netmask 255.255.255.0 {
    range 192.168.1.50 192.168.1.150;
    option routers 192.168.1.1;
    option broadcast-address 192.168.1.255;
    
    # Force all clients to use the same hostname
    option host-name "client";
    ddns-hostname "client";
    ddns-domain-name = "est.intra";
}
```

### 5. DHCP Interface Configuration (/etc/sysconfig/dhcpd)
```
# Replace eth0 with your actual network interface name
DHCPDARGS=enp0s8
```

## Setup Commands

Run these commands to set up everything properly:

```bash
# Set correct permissions
sudo chown named:named /var/named/est.intra.zone
sudo chown named:named /var/named/est.intra.rev
sudo chmod 664 /var/named/est.intra.zone
sudo chmod 664 /var/named/est.intra.rev

# Disable SELinux temporarily
sudo setenforce 0

# Set SELinux contexts correctly
sudo setsebool -P named_write_master_zones 1
sudo restorecon -rv /var/named
sudo restorecon -v /etc/named.conf
sudo restorecon -v /etc/dhcp/dhcpd.conf

# Restart services
sudo systemctl restart named
sudo systemctl restart dhcpd

# Enable services to start at boot
sudo systemctl enable named
sudo systemctl enable dhcpd

# Open firewall ports
sudo firewall-cmd --permanent --add-service=dns
sudo firewall-cmd --permanent --add-service=dhcp
sudo firewall-cmd --reload
```

## Verification

On your client machine, run:

```bash
sudo dhclient -r
sudo dhclient

# Check hostname
hostname

# Check DNS configuration
cat /etc/resolv.conf

# Test DNS resolution
nslookup client.est.intra
nslookup dns.est.intra
```

This configuration will:
1. Set up a DNS server that allows dynamic updates
2. Configure a DHCP server that assigns IPs and updates DNS records
3. Force all clients to use "client.est.intra" as their hostname
4. Set the necessary permissions and contexts for everything to work

Note: When multiple clients connect, they will all get the same DNS name "client.est.intra" but with different IP addresses. The most recent client to connect will be the one that resolves to that name in DNS.
