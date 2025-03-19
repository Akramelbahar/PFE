# DDNS with DHCP Configuration Guide for VirtualBox

This guide will help you set up Dynamic DNS (DDNS) with DHCP in VirtualBox so that clients automatically receive domain information from your DNS server. The configuration is based on Fedora Server 39.

## Network Architecture

We'll follow the architecture from the PDF:
- DNS + DHCP Server: Placed in DMZ zone with IP 192.168.2.1
- Clients: LAN zone with IP range 192.168.1.11-254

## Step 1: VirtualBox Network Setup

1. Create two VirtualBox machines:
   - Server (Fedora Server 39): Will function as both DNS and DHCP server
   - Client: Will receive IP address and domain info from the server

2. Configure VirtualBox networking:
   - Create two host-only networks in VirtualBox:
     - vboxnet0 (192.168.1.0/24) for the LAN
     - vboxnet1 (192.168.2.0/24) for the DMZ

3. Server VM network adapters:
   - Adapter 1: Host-only adapter attached to vboxnet1 (DMZ network)
   - Adapter 2: Host-only adapter attached to vboxnet0 (LAN network)
   
4. Client VM network adapter:
   - Adapter 1: Host-only adapter attached to vboxnet0 (LAN network)

## Step 2: Install Required Packages on Server

```bash
sudo dnf update
sudo dnf install bind bind-utils dhcp-server
```

## Step 3: Configure DNS Server (BIND)

1. Edit the main DNS configuration file:

```bash
sudo nano /etc/named.conf
```

2. Configure the DNS server with the following content:

```
options {
        listen-on port 53 { 127.0.0.1; 192.168.2.1; 192.168.1.2; };
        listen-on-v6 port 53 { ::1; };
        directory "/var/named";
        dump-file "/var/named/data/cache_dump.db";
        statistics-file "/var/named/data/named_stats.txt";
        memstatistics-file "/var/named/data/named_mem_stats.txt";
        allow-query { localhost; 192.168.1.0/24; 192.168.2.0/24; };
        recursion yes;
        
        # For DDNS
        allow-new-zones yes;
        dnssec-validation no;
        allow-transfer { none; };
};

logging {
        channel default_debug {
                file "data/named.run";
                severity dynamic;
        };
};

# Key for secure DDNS updates
key "dhcp-key" {
        algorithm hmac-md5;
        secret "YourSecretKeyHere"; # Generate with dnssec-keygen
};

# Forward zone
zone "est.intra" {
        type master;
        file "est.intra.zone";
        allow-update { key "dhcp-key"; };
};

# Reverse zone for DMZ network
zone "2.168.192.in-addr.arpa" {
        type master;
        file "2.168.192.in-addr.arpa";
        allow-update { key "dhcp-key"; };
};

# Reverse zone for LAN network
zone "1.168.192.in-addr.arpa" {
        type master;
        file "1.168.192.in-addr.arpa";
        allow-update { key "dhcp-key"; };
};
```

3. Generate a DNSSEC key for secure updates:

```bash
sudo dnssec-keygen -a HMAC-MD5 -b 128 -n USER dhcp-key
```

4. Get the key from the generated file:

```bash
sudo cat Kdhcp-key.*.private | grep Key
```

5. Replace "YourSecretKeyHere" in the named.conf file with the actual key.

## Step 4: Create DNS Zone Files

1. Create the forward zone file:

```bash
sudo nano /var/named/est.intra.zone
```

2. Add the following content:

```
$TTL 86400
@       IN SOA  dns.est.intra. admin.est.intra. (
                2023030101 ; serial
                3600       ; refresh (1 hour)
                1800       ; retry (30 minutes)
                604800     ; expire (1 week)
                86400      ; minimum (1 day)
                )
        IN NS   dns.est.intra.
dns     IN A    192.168.2.1
        IN A    192.168.1.2
```

3. Create the reverse zone file for the DMZ network:

```bash
sudo nano /var/named/2.168.192.in-addr.arpa
```

4. Add the following content:

```
$TTL 86400
@       IN SOA  ns1.est.intra. admin.est.intra. (
                2023030101 ; serial
                3600       ; refresh (1 hour)
                1800       ; retry (30 minutes)
                604800     ; expire (1 week)
                86400      ; minimum (1 day)
                )
        IN NS   ns1.est.intra.
1       IN PTR  dns.est.intra.
```

5. Create the reverse zone file for the LAN network:

```bash
sudo nano /var/named/1.168.192.in-addr.arpa
```

6. Add the following content:

```
$TTL 86400
@       IN SOA  ns1.est.intra. admin.est.intra. (
                2023030101 ; serial
                3600       ; refresh (1 hour)
                1800       ; retry (30 minutes)
                604800     ; expire (1 week)
                86400      ; minimum (1 day)
                )
        IN NS   ns1.est.intra.
2       IN PTR  dns.est.intra.
```

7. Set proper permissions for zone files:

```bash
sudo chown named:named /var/named/est.intra.zone
sudo chown named:named /var/named/2.168.192.in-addr.arpa
sudo chown named:named /var/named/1.168.192.in-addr.arpa
sudo chmod 640 /var/named/est.intra.zone
sudo chmod 640 /var/named/2.168.192.in-addr.arpa
sudo chmod 640 /var/named/1.168.192.in-addr.arpa
```

## Step 5: Configure DHCP Server with DDNS

1. Edit the DHCP configuration file:

```bash
sudo nano /etc/dhcp/dhcpd.conf
```

2. Add the following content:

```
# DHCP Configuration with DDNS

# DDNS update style
ddns-update-style interim;
ddns-domainname "est.intra.";
ddns-rev-domainname "in-addr.arpa.";

# Client domain prefix
option host-name = concat("client", ".", option host-name);

# DDNS security
key "dhcp-key" {
        algorithm hmac-md5;
        secret "YourSecretKeyHere"; # Use the same key as in named.conf
}

# DDNS zones
zone est.intra. {
        key "dhcp-key";
        primary 127.0.0.1;
}

zone 1.168.192.in-addr.arpa. {
        key "dhcp-key";
        primary 127.0.0.1;
}

# Global options
option domain-name "est.intra";
option domain-name-servers 192.168.2.1;

# Update settings
ddns-updates on;
ignore client-updates;
update-static-leases on;
update-conflict-detection off;
update-optimization off;

# Lease time
default-lease-time 3600;
max-lease-time 7200;

# LAN subnet
subnet 192.168.1.0 netmask 255.255.255.0 {
        range 192.168.1.11 192.168.1.254;
        option routers 192.168.1.2;
        option broadcast-address 192.168.1.255;
}

# DMZ subnet (if needed)
subnet 192.168.2.0 netmask 255.255.255.0 {
        range 192.168.2.11 192.168.2.254;
        option routers 192.168.2.1;
        option broadcast-address 192.168.2.255;
}
```

## Step 6: Configure Network Interfaces

1. Configure the network interfaces on the server:

```bash
sudo nano /etc/sysconfig/network-scripts/ifcfg-enp0s3
```

2. Add the following content for the DMZ interface:

```
TYPE=Ethernet
BOOTPROTO=static
DEFROUTE=yes
NAME=enp0s3
DEVICE=enp0s3
ONBOOT=yes
IPADDR=192.168.2.1
PREFIX=24
```

3. Configure the LAN interface:

```bash
sudo nano /etc/sysconfig/network-scripts/ifcfg-enp0s8
```

4. Add the following content:

```
TYPE=Ethernet
BOOTPROTO=static
NAME=enp0s8
DEVICE=enp0s8
ONBOOT=yes
IPADDR=192.168.1.2
PREFIX=24
```

5. Restart the network service:

```bash
sudo systemctl restart NetworkManager
```

## Step 7: Configure IP Forwarding

1. Enable IP forwarding to allow the server to route between networks:

```bash
sudo echo "net.ipv4.ip_forward = 1" >> /etc/sysctl.conf
sudo sysctl -p
```

## Step 8: Start and Enable Services

1. Start and enable the DNS server:

```bash
sudo systemctl enable --now named
```

2. Start and enable the DHCP server:

```bash
sudo systemctl enable --now dhcpd
```

3. Allow DNS and DHCP services through the firewall:

```bash
sudo firewall-cmd --permanent --add-service=dns
sudo firewall-cmd --permanent --add-service=dhcp
sudo firewall-cmd --reload
```

## Step 9: Configure SELinux (if enabled)

1. Allow BIND to modify zone files:

```bash
sudo setsebool -P named_write_master_zones 1
```

2. Allow DHCP to update DNS zones:

```bash
sudo setsebool -P dhcpd_read_db 1
```

## Step 10: Client Configuration

1. Boot up the client VM.
2. The client should automatically receive an IP address from the DHCP server.
3. The client should also receive the domain name "est.intra" and the DNS server IP address.

## Step 11: Verify the Setup

1. On the server, check that the DHCP and DNS services are running:

```bash
sudo systemctl status named
sudo systemctl status dhcpd
```

2. Check the DHCP leases:

```bash
sudo cat /var/lib/dhcpd/dhcpd.leases
```

3. Check the DNS zone files to see if client records have been added:

```bash
sudo cat /var/named/est.intra.zone
sudo cat /var/named/1.168.192.in-addr.arpa
```

4. On the client, verify that it can resolve DNS names:

```bash
nslookup dns.est.intra
```

5. Verify that the client has been added to the DNS zone:

```bash
nslookup $(hostname)
```

## Troubleshooting

If you encounter issues:

1. Check the DNS server logs:

```bash
sudo journalctl -u named
```

2. Check the DHCP server logs:

```bash
sudo journalctl -u dhcpd
```

3. Test the DNS configuration:

```bash
sudo named-checkconf /etc/named.conf
sudo named-checkzone est.intra /var/named/est.intra.zone
sudo named-checkzone 1.168.192.in-addr.arpa /var/named/1.168.192.in-addr.arpa
```

4. Test the DHCP configuration:

```bash
sudo dhcpd -t
```

5. Ensure that SELinux is not blocking the services:

```bash
sudo ausearch -m avc -ts recent
```

## Final Notes

This configuration implements Dynamic DNS with DHCP in a VirtualBox environment, allowing clients to automatically register in the DNS zone. The DNS server is configured to accept secure updates from the DHCP server using a shared key.

Remember to adapt interface names (enp0s3, enp0s8) to match your actual VirtualBox network interfaces, which may differ depending on your system.
