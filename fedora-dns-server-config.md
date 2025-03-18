To have domains automatically assigned by DNS and DHCP when clients connect, we need to ensure the DDNS (Dynamic DNS) updates are working correctly. Let me provide you with a complete configuration that will automatically assign domain names to clients.

### 1. DNS Server Configuration (named.conf)

```bash
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
    dnssec-enable no;
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

### 2. Forward Zone File (est.intra.zone)

```
$TTL 86400
@       IN SOA  dns.est.intra. admin.est.intra. (
                2023011001 ; Serial
                3600       ; Refresh
                1800       ; Retry
                604800     ; Expire
                86400      ; Minimum TTL
)
; Name servers
@       IN NS   dns.est.intra.
; A records
dns     IN A    192.168.1.10
```

### 3. Reverse Zone File (est.intra.rev)

```
$TTL 86400
@       IN SOA  dns.est.intra. admin.est.intra. (
                2023011001 ; Serial
                3600       ; Refresh
                1800       ; Retry
                604800     ; Expire
                86400      ; Minimum TTL
)
; Name servers
@       IN NS   dns.est.intra.
; PTR records
10      IN PTR  dns.est.intra.
```

### 4. DHCP Server Configuration (dhcpd.conf)

```
# DDNS configuration
ddns-updates on;
ddns-update-style interim;
update-static-leases on;
use-host-decl-names on;

# Define the DDNS key (must match the one in named.conf)
key "ddns-key.est.intra" {
    algorithm hmac-md5;
    secret "abcdefghijklmnopqrstuvwxyz123456";
};

# DNS update configuration
zone est.intra. {
    key ddns-key.est.intra;
    primary 192.168.1.10;
}

zone 1.168.192.in-addr.arpa. {
    key ddns-key.est.intra;
    primary 192.168.1.10;
}

# Basic network settings
option domain-name "est.intra";
option domain-name-servers 192.168.1.10;
default-lease-time 3600;
max-lease-time 86400;
authoritative;
log-facility local7;

# Define the subnet
subnet 192.168.1.0 netmask 255.255.255.0 {
    range 192.168.1.50 192.168.1.150;
    option routers 192.168.1.1;
    option broadcast-address 192.168.1.255;
    
    # This will automatically generate a hostname based on the client's IP
    # For example, 192.168.1.108 would get hostname "client-108"
    ddns-hostname = concat("client-", binary-to-ascii(10, 8, "-", substring(leased-address, 3, 1)));
    ddns-domain-name = "est.intra";
    
    # FQDN options
    option fqdn.no-client-update on;
    option fqdn.server-update on;
    option fqdn.encoded on;
}
```

### 5. Set Permissions and Restart Services

```bash
sudo chmod 644 /var/named/est.intra.zone
sudo chmod 644 /var/named/est.intra.rev
sudo chown named:named /var/named/est.intra.zone
sudo chown named:named /var/named/est.intra.rev
sudo restorecon -r /var/named
sudo restorecon -r /etc/named.conf
sudo restorecon -r /etc/dhcp

sudo systemctl restart named
sudo systemctl restart dhcpd
```

### 6. Client Configuration

On the client, request a new IP address:

```bash
sudo dhclient -r
sudo dhclient
```

With this configuration:

1. The DHCP server will assign IP addresses to clients
2. It will automatically generate hostnames based on IP (e.g., client-108 for 192.168.1.108)
3. The DHCP server will automatically update the DNS server with both forward and reverse records
4. Clients will be able to resolve names within the est.intra domain

To verify the setup is working, the client should be able to:

```bash
hostname   # Should show the hostname assigned by DHCP
dig client-108.est.intra  # Should resolve to the IP
nslookup 192.168.1.108  # Should return the hostname
```

If you want a more expressive hostname that includes more parts of the IP address, you can modify the `ddns-hostname` line to:

```
ddns-hostname = concat("client-", binary-to-ascii(10, 8, "-", leased-address));
```

This will create names like "client-192-168-1-108" instead of just "client-108".
