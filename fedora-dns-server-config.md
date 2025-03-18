I understand you need a solution that's easier to copy and paste without additional explanations. Here's a streamlined version of the DNS and DHCP configuration files that you can directly copy and paste:

### 1. For named.conf

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
    dnssec-validation yes;
    managed-keys-directory "/var/named/dynamic";
    pid-file "/run/named/named.pid";
    session-keyfile "/run/named/session.key";
    include "/etc/crypto-policies/back-ends/bind.config";
};

key "ddns-key.est.intra" {
    algorithm hmac-md5;
    secret "abcdefghijklmnopqrstuvwxyz123456";
};

zone "est.intra" IN {
    type master;
    file "est.intra.zone";
    allow-update { key "ddns-key.est.intra"; };
    journal "est.intra.zone.jnl";
};

zone "1.168.192.in-addr.arpa" IN {
    type master;
    file "est.intra.rev";
    allow-update { key "ddns-key.est.intra"; };
    journal "est.intra.rev.jnl";
};
```

### 2. For est.intra.zone

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

### 3. For est.intra.rev

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

### 4. For dhcpd.conf

```
ddns-updates on;
ddns-update-style interim;
update-static-leases on;
use-host-decl-names on;

key "ddns-key.est.intra" {
    algorithm hmac-md5;
    secret "abcdefghijklmnopqrstuvwxyz123456";
};

zone est.intra. {
    key ddns-key.est.intra;
    primary 192.168.1.10;
}

zone 1.168.192.in-addr.arpa. {
    key ddns-key.est.intra;
    primary 192.168.1.10;
}

option domain-name "est.intra";
option domain-name-servers 192.168.1.10;
default-lease-time 600;
max-lease-time 7200;
authoritative;

subnet 192.168.1.0 netmask 255.255.255.0 {
    range 192.168.1.50 192.168.1.150;
    option routers 192.168.1.1;
    option broadcast-address 192.168.1.255;
    
    ddns-hostname = concat("client-", binary-to-ascii(10, 8, "-", leased-address));
    ddns-domain-name = "est.intra";
}
```

### 5. Command sequence (after creating the files above)

```
sudo chmod 644 /var/named/est.intra.zone
sudo chmod 644 /var/named/est.intra.rev
sudo chown named:named /var/named/est.intra.zone
sudo chown named:named /var/named/est.intra.rev
sudo restorecon -r /var/named
sudo restorecon -r /etc/named.conf
sudo restorecon -r /etc/dhcp
sudo systemctl restart named
sudo systemctl restart dhcpd
sudo systemctl enable named
sudo systemctl enable dhcpd
sudo firewall-cmd --permanent --add-service=dns
sudo firewall-cmd --permanent --add-service=dhcp
sudo firewall-cmd --reload
```

Note: Use the same DDNS key in both files. I've set it to "abcdefghijklmnopqrstuvwxyz123456" but you can replace it with your own key if desired.
