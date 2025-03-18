I'll provide you with the full DHCP and DNS configurations, using nano instead of vi, and ensure the NS records are correctly assigned to devices connecting via DHCP.

## Step 1: Configure BIND (DNS Server)

Let's start with the DNS configuration:

```bash
# Edit named.conf with nano
sudo nano /etc/named.conf
```

Add this content:

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

// Create a key for secure dynamic updates
key "ddns-key.est.intra" {
    algorithm hmac-md5;
    secret "YourGeneratedSecretKeyHere"; // Replace with your generated key
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

Now, let's create the forward zone file:

```bash
sudo nano /var/named/est.intra.zone
```

Add this content:

```
$TTL 86400
@       IN SOA  dns.est.intra. admin.est.intra. (
                2023011002 ; Serial
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

Create the reverse zone file:

```bash
sudo nano /var/named/est.intra.rev
```

Add this content:

```
$TTL 86400
@       IN SOA  dns.est.intra. admin.est.intra. (
                2023011002 ; Serial
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

Set proper permissions:

```bash
sudo chown named:named /var/named/est.intra.zone
sudo chown named:named /var/named/est.intra.rev
sudo chmod 644 /var/named/est.intra.zone
sudo chmod 644 /var/named/est.intra.rev
```

## Step 2: Generate DDNS Key

If you haven't successfully generated a key yet, let's create one manually:

```bash
# Generate a random string to use as your key
openssl rand -base64 16 > /tmp/ddns-key.txt

# View the generated key
cat /tmp/ddns-key.txt
```

Take note of the generated key, and replace "YourGeneratedSecretKeyHere" in the named.conf file with this key.

## Step 3: Configure DHCP with DDNS Integration

Now, let's set up the DHCP configuration:

```bash
sudo nano /etc/dhcp/dhcpd.conf
```

Add this content:

```
# DDNS configuration
ddns-updates on;
ddns-update-style interim;
update-static-leases on;
use-host-decl-names on;

# Define the DDNS key (must match the one in named.conf)
key "ddns-key.est.intra" {
    algorithm hmac-md5;
    secret "YourGeneratedSecretKeyHere"; # Replace with the same key used in named.conf
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
default-lease-time 600;
max-lease-time 7200;
authoritative;

# Define the subnet
subnet 192.168.1.0 netmask 255.255.255.0 {
    range 192.168.1.50 192.168.1.150;
    option routers 192.168.1.1;
    option broadcast-address 192.168.1.255;
    
    # Set client hostname/domain to be updated in DNS
    ddns-hostname = concat("client-", binary-to-ascii(10, 8, "-", leased-address));
    ddns-domain-name = "est.intra";
    
    # Static IP assignment for specific client (optional)
    host client {
        hardware ethernet AA:BB:CC:DD:EE:FF;  # Replace with actual MAC address
        fixed-address 192.168.1.100;
        option host-name "client.est.intra";
        ddns-hostname "client";
        ddns-domain-name "est.intra";
    }
}
```

Make sure to replace "YourGeneratedSecretKeyHere" with the same key you used in the named.conf file.

## Step 4: Specify DHCP Interface

You might need to specify which network interface DHCP should listen on:

```bash
sudo nano /etc/sysconfig/dhcpd
```

Add this line (replace eth0 with your actual network interface name):

```
DHCPDARGS=eth0
```

## Step 5: Set SELinux Context (if SELinux is enabled)

```bash
sudo restorecon -r /var/named
sudo restorecon -r /etc/named.conf
sudo restorecon -r /etc/dhcp
```

## Step 6: Start and Enable Services

```bash
# Enable and start DNS
sudo systemctl enable named
sudo systemctl start named

# Enable and start DHCP
sudo systemctl enable dhcpd
sudo systemctl start dhcpd

# Check if the services are running
sudo systemctl status named
sudo systemctl status dhcpd
```

## Step 7: Open Firewall Ports

```bash
sudo firewall-cmd --permanent --add-service=dns
sudo firewall-cmd --permanent --add-service=dhcp
sudo firewall-cmd --reload
```

## Verification

Once the client connects to the DHCP server, it should receive:
1. An IP address (e.g., 192.168.1.100)
2. A hostname based on the IP (e.g., client-192-168-1-100.est.intra)
3. The DNS server will be automatically updated with this hostname and IP

You can verify this on the client with:

```bash
# On client
hostname
ip addr
cat /etc/resolv.conf
```

And on the server with:

```bash
# On server
dig @192.168.1.10 client-192-168-1-100.est.intra
```

If you encounter any specific errors, check the logs:

```bash
sudo journalctl -u named
sudo journalctl -u dhcpd
```

This configuration will ensure that any device connecting to your DHCP server will automatically get registered in DNS with a hostname derived from its IP address.
