I'll help you set up Dynamic DNS (DDNS) with DHCP integration on Fedora Server. DDNS allows clients to automatically update their DNS records when they receive IP addresses from DHCP.

Let's modify our previous configuration to implement DDNS:

## Server Configuration (DDNS + DHCP)

First, let's modify the BIND configuration to allow dynamic updates:

```bash
# On the server VM (dns.est.intra)
# Generate a key for secure updates
sudo dnssec-keygen -a HMAC-SHA256 -b 256 -n HOST ddns-key.est.intra

# This creates two files in the current directory with names like:
# Kddns-key.est.intra.+157+12345.key and Kddns-key.est.intra.+157+12345.private
# Note the key value from the .key file for use below
```

Now, let's create a key configuration file:

```bash
sudo vi /etc/named/ddns.key
```

Add the following content (replace the secret with the actual key from your generated file):

```
key "ddns-key.est.intra" {
    algorithm hmac-sha256;
    secret "paste_your_generated_secret_here";
};
```

Now, modify the named.conf file:

```bash
sudo vi /etc/named.conf
```

Include the key file and modify the zone definitions:

```
include "/etc/named/ddns.key";

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

Now modify the zone files to prepare them for dynamic updates:

```bash
sudo vi /var/named/est.intra.zone
```

Simplify it to the minimal needed content:

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

Simplify the reverse zone file as well:

```bash
sudo vi /var/named/est.intra.rev
```

Content:

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

Now let's modify the DHCP configuration to work with DDNS:

```bash
sudo vi /etc/dhcp/dhcpd.conf
```

Update with the following content:

```
# DDNS configuration
ddns-updates on;
ddns-update-style interim;
update-static-leases on;
use-host-decl-names on;

# Include the DDNS key
include "/etc/named/ddns.key";

# DNS zone update configurations
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
  
  # Fixed IP for client
  host client {
    hardware ethernet AA:BB:CC:DD:EE:FF;  # Replace with actual MAC address
    fixed-address 192.168.1.100;
    option host-name "client.est.intra";
    ddns-hostname "client";
    ddns-domain-name "est.intra";
  }
}
```

Set proper file permissions:

```bash
# Secure the key file
sudo chmod 640 /etc/named/ddns.key
sudo chown root:named /etc/named/ddns.key

# Make sure named can write to the zone files
sudo chown named:named /var/named/est.intra.zone
sudo chown named:named /var/named/est.intra.rev
sudo chmod 644 /var/named/est.intra.zone
sudo chmod 644 /var/named/est.intra.rev

# Make the key accessible to DHCP server
sudo chgrp dhcpd /etc/named/ddns.key
sudo chmod 640 /etc/named/ddns.key
```

Now restart both services:

```bash
sudo systemctl restart named
sudo systemctl restart dhcpd

# Verify the services are running
sudo systemctl status named
sudo systemctl status dhcpd
```

## Client Configuration

For DDNS to work properly, you'll want to configure the client to use DHCP. On the client machine:

```bash
# On the client VM
# Configure to use DHCP
sudo nmcli con mod "System eth0" ipv4.method auto
sudo nmcli con up "System eth0"

# You can explicitly set the hostname if you want
sudo hostnamectl set-hostname client.est.intra
```

## Testing DDNS

To test that dynamic DNS updates are working:

1. On the client, request a new IP address:
   ```bash
   sudo dhclient -v -r eth0
   sudo dhclient -v eth0
   ```

2. Check the client's network configuration:
   ```bash
   ip addr show eth0
   cat /etc/resolv.conf
   ```

3. On the server, check if the DNS records have been updated:
   ```bash
   sudo rndc dumpdb -zones
   sudo cat /var/named/data/named_dump.db
   
   # Or query the DNS server directly
   dig @192.168.1.10 client.est.intra
   dig @192.168.1.10 -x 192.168.1.100
   ```

4. From the client, test name resolution:
   ```bash
   nslookup client.est.intra
   nslookup dns.est.intra
   ping -c 4 dns.est.intra
   ```

## Troubleshooting

If you encounter issues:

1. Check the logs:
   ```bash
   sudo journalctl -u named
   sudo journalctl -u dhcpd
   ```

2. Test manual DDNS updates to verify your configuration:
   ```bash
   # On the server, using nsupdate tool
   nsupdate -k /path/to/Kddns-key.est.intra.+157+12345.private
   > server 192.168.1.10
   > zone est.intra
   > update add test.est.intra 86400 A 192.168.1.200
   > send
   > quit
   ```

3. Verify the update with:
   ```bash
   dig @192.168.1.10 test.est.intra
   ```

This DDNS setup allows clients to automatically register themselves in DNS when they receive IP addresses from DHCP, making it easier to manage your network and keep DNS records up to date.
