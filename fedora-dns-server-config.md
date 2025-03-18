I'll provide you with clean, corrected configurations for both the DHCP and DNS servers to ensure automatic domain assignment works correctly.

### 1. DNS Server Configuration (/etc/named.conf)

```
options {
    listen-on port 53 { 127.0.0.1; 192.168.1.10; };
    listen-on-v6 port 53 { ::1; };
    directory     "/var/named";
    dump-file     "/var/named/data/cache_dump.db";
    statistics-file "/var/named/data/named_stats.txt";
    memstatistics-file "/var/named/data/named_mem_stats.txt";
    secroots-file "/var/named/data/named.secroots";
    recursing-file "/var/named/data/named.recursing";
    allow-query     { localhost; 192.168.1.0/24; };
    forwarders      { 8.8.8.8; 8.8.4.4; };
    forward         first;
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

logging {
    channel default_debug {
        file "data/named.run";
        severity dynamic;
    };
    
    channel ddns_log {
        file "/var/log/named.ddns.log";
        severity debug;
        print-severity yes;
        print-time yes;
    };
    
    category update { ddns_log; };
    category security { ddns_log; };
};
```

### 2. Forward Zone File (/var/named/est.intra.zone)

```
$TTL 86400
@       IN SOA  dns.est.intra. admin.est.intra. (
                2023011002 ; Serial
                3600       ; Refresh
                1800       ; Retry
                604800     ; Expire
                86400      ; Minimum TTL
)
@       IN NS   dns.est.intra.
dns     IN A    192.168.1.10
```

### 3. Reverse Zone File (/var/named/est.intra.rev)

```
$TTL 86400
@       IN SOA  dns.est.intra. admin.est.intra. (
                2023011002 ; Serial
                3600       ; Refresh
                1800       ; Retry
                604800     ; Expire
                86400      ; Minimum TTL
)
@       IN NS   dns.est.intra.
10      IN PTR  dns.est.intra.
```

### 4. DHCP Server Configuration (/etc/dhcp/dhcpd.conf)

```
# Basic DHCP settings
default-lease-time 3600;
max-lease-time 86400;
authoritative;
log-facility local7;

# DDNS configuration
ddns-updates on;
ddns-update-style interim;
update-static-leases on;
ignore client-updates;

# DDNS key
key "ddns-key.est.intra" {
    algorithm hmac-md5;
    secret "abcdefghijklmnopqrstuvwxyz123456";
};

# Zone information
zone est.intra. {
    primary 192.168.1.10;
    key ddns-key.est.intra;
};

zone 1.168.192.in-addr.arpa. {
    primary 192.168.1.10;
    key ddns-key.est.intra;
};

# Network configuration
option domain-name "est.intra";
option domain-name-servers 192.168.1.10;

subnet 192.168.1.0 netmask 255.255.255.0 {
    range 192.168.1.50 192.168.1.150;
    option routers 192.168.1.1;
    option broadcast-address 192.168.1.255;
    
    # Generate hostname from IP address
    ddns-hostname = concat("client-", binary-to-ascii(10, 8, "-", substring(leased-address, 3, 1)));
    ddns-domain-name = "est.intra";
}
```

### 5. Set proper permissions and restart services

```bash
# Set permissions for DNS zone files
sudo chown named:named /var/named/est.intra.zone
sudo chown named:named /var/named/est.intra.rev
sudo chmod 664 /var/named/est.intra.zone
sudo chmod 664 /var/named/est.intra.rev

# Create log directory if it doesn't exist
sudo mkdir -p /var/log
sudo touch /var/log/named.ddns.log
sudo chown named:named /var/log/named.ddns.log

# Set SELinux context properly
sudo setenforce 0
sudo setsebool -P named_write_master_zones 1
sudo restorecon -rv /var/named
sudo restorecon -v /etc/named.conf
sudo restorecon -v /etc/dhcp/dhcpd.conf

# Restart services
sudo systemctl restart named
sudo systemctl restart dhcpd
```

After implementing these changes, have your client request a new IP address:

```bash
sudo dhclient -r
sudo dhclient
```

You can verify the domain assignment by checking:

```bash
# On the client
cat /etc/resolv.conf
hostname

# On the server
sudo grep update /var/log/named.ddns.log
dig @192.168.1.10 client-108.est.intra   # If the client got IP 192.168.1.108
```

These configurations should work together to automatically assign domain names to any client that receives an IP from your DHCP server.
