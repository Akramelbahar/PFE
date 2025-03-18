I'll generate all the essential files you need for your DNS/DHCP setup to ensure everything works correctly, including forward and reverse lookups.

## 1. Main DNS Configuration (/etc/named.conf)

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
```

## 2. Forward Zone File (/var/named/est.intra.zone)

```
$TTL 86400
@       IN SOA  dns.est.intra. admin.est.intra. (
                2023011005 ; Serial
                3600       ; Refresh
                1800       ; Retry
                604800     ; Expire
                86400      ; Minimum TTL
)
@       IN NS   dns
dns     IN A    192.168.1.10
```

## 3. Reverse Zone File (/var/named/est.intra.rev)

```
$TTL 86400
@       IN SOA  dns.est.intra. admin.est.intra. (
                2023011005 ; Serial
                3600       ; Refresh
                1800       ; Retry
                604800     ; Expire
                86400      ; Minimum TTL
)
@       IN NS   dns.est.intra.
10      IN PTR  dns.est.intra.
```

## 4. DHCP Configuration (/etc/dhcp/dhcpd.conf)

```
# Basic DHCP settings
default-lease-time 3600;
max-lease-time 86400;
authoritative;

# DDNS configuration
ddns-updates on;
ddns-update-style interim;
update-static-leases on;

# DDNS key
key "ddns-key.est.intra" {
    algorithm hmac-md5;
    secret "abcdefghijklmnopqrstuvwxyz123456";
};

# Zone information
zone est.intra. {
    primary 192.168.1.10;
    key ddns-key.est.intra;
}

zone 1.168.192.in-addr.arpa. {
    primary 192.168.1.10;
    key ddns-key.est.intra;
}

# Network configuration
option domain-name "est.intra";
option domain-name-servers 192.168.1.10;

subnet 192.168.1.0 netmask 255.255.255.0 {
    range 192.168.1.50 192.168.1.150;
    option routers 192.168.1.1;
    option broadcast-address 192.168.1.255;
    
    option host-name "client";
    ddns-hostname "client";
    ddns-domain-name = "est.intra";
}
```

## 5. Commands to Apply Configuration

```bash
# Set permissions for DNS files
sudo chown named:named /var/named/est.intra.zone
sudo chown named:named /var/named/est.intra.rev
sudo chmod 664 /var/named/est.intra.zone
sudo chmod 664 /var/named/est.intra.rev

# Handle SELinux (if enabled)
sudo setenforce 0
sudo setsebool -P named_write_master_zones 1
sudo restorecon -rv /var/named
sudo restorecon -v /etc/named.conf
sudo restorecon -v /etc/dhcp/dhcpd.conf

# Setup DHCP interface (adjust interface name if needed)
echo "DHCPDARGS=eth0" | sudo tee /etc/sysconfig/dhcpd

# Restart services
sudo systemctl restart named
sudo systemctl restart dhcpd

# Enable services at boot
sudo systemctl enable named
sudo systemctl enable dhcpd

# Open firewall ports
sudo firewall-cmd --permanent --add-service=dns
sudo firewall-cmd --permanent --add-service=dhcp
sudo firewall-cmd --reload
```

## 6. Verification Commands

```bash
# Check DNS server
sudo systemctl status named
dig @192.168.1.10 dns.est.intra
dig @192.168.1.10 -x 192.168.1.10

# Check DHCP server
sudo systemctl status dhcpd
cat /var/lib/dhcpd/dhcpd.leases

# Test client connection (run on client)
sudo dhclient -r
sudo dhclient
hostname
ping dns.est.intra
```

These files and commands should give you a complete and working setup for your DNS/DHCP configuration with the "client.est.intra" domain assigned to all DHCP clients.
