If you can't ping from enp0s3 to enp0s8 on the same relay agent, that's a significant issue that needs to be addressed first. This is likely why the client can't reach the DMZ network either.

Let's troubleshoot this internal routing issue:

1. **Check the interface configuration**:
```bash
ip addr show enp0s3
ip addr show enp0s8
```
Make sure both interfaces have their correct IP addresses assigned.

2. **Check the local routing table**:
```bash
ip route show
```
You should see direct routes for both networks.

3. **Check firewall zones**:
```bash
sudo firewall-cmd --get-active-zones
```
Make sure both interfaces aren't in different zones with restrictive rules.

4. **Try adding explicit firewall rules to allow traffic between interfaces**:
```bash
sudo firewall-cmd --permanent --direct --add-rule ipv4 filter INPUT 0 -i enp0s3 -d 192.168.2.10 -j ACCEPT
sudo firewall-cmd --permanent --direct --add-rule ipv4 filter INPUT 0 -i enp0s8 -d 192.168.1.10 -j ACCEPT
sudo firewall-cmd --reload
```

5. **Check for any interface-specific settings**:
```bash
sudo sysctl -a | grep net.ipv4.conf
```
Look for any settings that might be blocking traffic between interfaces.

6. **Try temporarily disabling the firewall to test**:
```bash
sudo systemctl stop firewalld
```
Then try pinging between interfaces. If it works, the firewall is the issue.

7. **Check for any custom iptables rules**:
```bash
sudo iptables -L -v
```
Look for any rules that might be blocking traffic.

8. **Test loopback connectivity**:
```bash
ping -c 4 127.0.0.1
```
Make sure the basic networking stack is working.

9. **Create a simple test rule to allow all traffic**:
```bash
sudo iptables -P FORWARD ACCEPT
sudo iptables -P INPUT ACCEPT
sudo iptables -P OUTPUT ACCEPT
sudo iptables -F
```
This will temporarily clear all iptables rules. Try pinging between interfaces after this.

The fact that you can't ping between interfaces on the same machine suggests either a firewall issue or a fundamental networking configuration problem. Once we resolve this, the client should be able to reach the DMZ network as well.







































Looking at your network configuration, I can see several issues preventing the relay agent from accessing est.intra and the client from pinging the DNS server. Let me diagnose and provide specific fixes:

## Key Issues

1. **Relay Agent DNS Configuration**: The relay agent isn't properly configured to use the DNS server at 192.168.2.1
2. **Routing Problems**: There appear to be routing issues preventing proper communication 
3. **Firewall Rules**: The current firewall configuration may be blocking DNS traffic

Let's address each of these:

## Fixes for Relay Agent

1. **Configure DNS on Relay Agent**:
```bash
# Set DNS on relay agent for both interfaces
sudo nmcli connection modify enp0s3 ipv4.dns 192.168.2.1
sudo nmcli connection modify enp0s8 ipv4.dns 192.168.2.1
sudo nmcli connection modify enp0s3 ipv4.dns-search "est.intra"
sudo nmcli connection modify enp0s8 ipv4.dns-search "est.intra"
sudo nmcli connection down enp0s3 && sudo nmcli connection up enp0s3
sudo nmcli connection down enp0s8 && sudo nmcli connection up enp0s8
```

2. **Fix IP Forwarding and Routing**:
```bash
# Ensure IP forwarding is enabled
sudo sysctl -w net.ipv4.ip_forward=1

# Check direct routes are correct (no default routes to internal networks)
ip route show
```

3. **Adjust Firewall to Allow DNS Traffic**:
```bash
sudo firewall-cmd --permanent --add-service=dns
sudo firewall-cmd --permanent --direct --add-rule ipv4 filter FORWARD 0 -p udp --dport 53 -j ACCEPT
sudo firewall-cmd --permanent --direct --add-rule ipv4 filter FORWARD 0 -p tcp --dport 53 -j ACCEPT
sudo firewall-cmd --reload
```

## Fixes for LAN Client

1. **DNS Configuration for Client**:
```bash
# Make sure client is using the correct DNS
sudo nmcli connection modify lan-network ipv4.dns 192.168.2.1
sudo nmcli connection modify lan-network ipv4.dns-search "est.intra"
sudo nmcli connection down lan-network && sudo nmcli connection up lan-network
```

2. **Check ICMP Forwarding**:
```bash
# On relay agent, ensure ICMP is forwarded
sudo firewall-cmd --permanent --direct --add-rule ipv4 filter FORWARD 0 -p icmp -j ACCEPT
sudo firewall-cmd --reload
```

## Fixes for DNS Server

1. **Update DNS Server Configuration**:
```bash
# On the DMZ server (192.168.2.1), modify the named.conf
sudo nano /etc/named.conf
```

Update the options block to ensure DNS requests from relay are accepted:
```
options {
    listen-on port 53 { 127.0.0.1; 192.168.2.1; };
    listen-on-v6 port 53 { ::1; };
    directory "/var/named";
    
    allow-query { localhost; 192.168.1.0/24; 192.168.2.0/24; };
    allow-recursion { localhost; 192.168.1.0/24; 192.168.2.0/24; };
    
    recursion yes;
    
    /* For DDNS */
    allow-update { 192.168.2.1; };
};
```

Then restart the DNS service:
```bash
sudo systemctl restart named
```

2. **Check DNS Server Firewall**:
```bash
# On DMZ server
sudo firewall-cmd --permanent --add-service=dns
sudo firewall-cmd --reload
```

After applying these changes, test connectivity again:

1. On the relay agent: `nslookup www.est.intra`
2. On the LAN client: `ping 192.168.2.1` and `nslookup www.est.intra`

These changes should address the connectivity issues between your relay agent, client, and DNS server.








\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\
# Complete Configuration Updates for Your Network

Here are all the necessary updates for each machine in your network setup. I'll provide detailed configurations for each component based on the network interfaces I observed.

## 1. DMZ Server (192.168.2.1)

### DHCP Configuration
```bash
sudo nano /etc/dhcp/dhcpd.conf
```

Replace the content with:
```
# Global DHCP configuration
ddns-update-style interim;
ddns-domainname "est.intra.";
ddns-rev-domainname "in-addr.arpa.";
ddns-updates on;
ignore client-updates;
update-static-leases on;

# LAN Subnet
subnet 192.168.1.0 netmask 255.255.255.0 {
  range 192.168.1.100 192.168.1.200;
  option routers 192.168.1.10;  # Relay LAN interface
  option domain-name "est.intra";
  option domain-name-servers 192.168.2.1;
  default-lease-time 3600;
  max-lease-time 7200;
}

# DMZ Subnet
subnet 192.168.2.0 netmask 255.255.255.0 {
  range 192.168.2.100 192.168.2.200;
  option routers 192.168.2.10;  # Relay DMZ interface
  option domain-name "est.intra";
  option domain-name-servers 192.168.2.1;
  default-lease-time 3600;
  max-lease-time 7200;
}
```

Restart DHCP:
```bash
sudo systemctl restart dhcpd
sudo systemctl status dhcpd
```

### DNS Configuration
```bash
sudo nano /etc/named.conf
```

Make sure it contains:
```
options {
    listen-on port 53 { 127.0.0.1; 192.168.2.1; };
    listen-on-v6 port 53 { ::1; };
    directory "/var/named";
    dump-file "/var/named/data/cache_dump.db";
    statistics-file "/var/named/data/named_stats.txt";
    memstatistics-file "/var/named/data/named_mem_stats.txt";
    
    allow-query { localhost; 192.168.1.0/24; 192.168.2.0/24; };
    allow-transfer { localhost; };
    
    recursion yes;
    dnssec-validation yes;
    
    /* For DDNS */
    allow-update { 192.168.2.1; };
};

logging {
    channel default_debug {
        file "data/named.run";
        severity dynamic;
    };
};

zone "." IN {
    type hint;
    file "named.ca";
};

include "/etc/named.rfc1912.zones";
include "/etc/named.root.key";

/* Forward zone for est.intra */
zone "est.intra" IN {
    type master;
    file "est.intra.zone";
    allow-update { 192.168.2.1; };
};

/* Reverse zone for 192.168.1.0/24 */
zone "1.168.192.in-addr.arpa" IN {
    type master;
    file "1.168.192.in-addr.arpa.zone";
    allow-update { 192.168.2.1; };
};

/* Reverse zone for 192.168.2.0/24 */
zone "2.168.192.in-addr.arpa" IN {
    type master;
    file "2.168.192.in-addr.arpa.zone";
    allow-update { 192.168.2.1; };
};
```

Update DNS zone files:
```bash
sudo nano /var/named/est.intra.zone
```

Add:
```
$TTL 86400
@       IN      SOA     dns.est.intra. admin.est.intra. (
                        2024031902      ; Serial
                        3600            ; Refresh
                        1800            ; Retry
                        604800          ; Expire
                        86400 )         ; Minimum TTL

@       IN      NS      dns.est.intra.
@       IN      A       192.168.2.1

dns     IN      A       192.168.2.1
www     IN      A       192.168.2.1

; Relay agent entries
relay   IN      A       192.168.2.10
lan-relay IN    A       192.168.1.10
```

Update reverse zone files:
```bash
sudo nano /var/named/1.168.192.in-addr.arpa.zone
```

Add:
```
$TTL 86400
@       IN      SOA     dns.est.intra. admin.est.intra. (
                        2024031902      ; Serial
                        3600            ; Refresh
                        1800            ; Retry
                        604800          ; Expire
                        86400 )         ; Minimum TTL

@       IN      NS      dns.est.intra.
10      IN      PTR     lan-relay.est.intra.
```

```bash
sudo nano /var/named/2.168.192.in-addr.arpa.zone
```

Add:
```
$TTL 86400
@       IN      SOA     dns.est.intra. admin.est.intra. (
                        2024031902      ; Serial
                        3600            ; Refresh
                        1800            ; Retry
                        604800          ; Expire
                        86400 )         ; Minimum TTL

@       IN      NS      dns.est.intra.
1       IN      PTR     dns.est.intra.
1       IN      PTR     www.est.intra.
10      IN      PTR     relay.est.intra.
```

Set correct permissions and restart DNS:
```bash
sudo chown named:named /var/named/est.intra.zone
sudo chown named:named /var/named/1.168.192.in-addr.arpa.zone
sudo chown named:named /var/named/2.168.192.in-addr.arpa.zone
sudo systemctl restart named
sudo systemctl status named
```

### Web Server Configuration
```bash
sudo nano /etc/httpd/conf.d/est.intra.conf
```

Add:
```
<VirtualHost *:80>
    ServerName www.est.intra
    ServerAlias www.est.intra
    DocumentRoot /var/www/html/est.intra
    ErrorLog /var/log/httpd/est.intra-error.log
    CustomLog /var/log/httpd/est.intra-access.log combined
    
    <Directory /var/www/html/est.intra>
        Options Indexes FollowSymLinks
        AllowOverride All
        Require all granted
    </Directory>
</VirtualHost>
```

Create web content:
```bash
sudo mkdir -p /var/www/html/est.intra
sudo nano /var/www/html/est.intra/index.php
```

Add:
```php
<?php
echo "<h1>Welcome to www.est.intra!</h1>";
echo "<p>Server is running on " . $_SERVER['SERVER_ADDR'] . "</p>";
echo "<p>Your IP address is: " . $_SERVER['REMOTE_ADDR'] . "</p>";
echo "<h2>PHP Information</h2>";
phpinfo();
?>
```

Set permissions and restart Apache:
```bash
sudo chown -R apache:apache /var/www/html/est.intra
sudo restorecon -R /var/www/html/est.intra
sudo systemctl restart httpd
sudo systemctl status httpd
```

### DMZ Server Firewall
```bash
sudo firewall-cmd --permanent --add-service=dhcp
sudo firewall-cmd --permanent --add-service=dns
sudo firewall-cmd --permanent --add-service=http
sudo firewall-cmd --reload
```

## 2. Relay Agent Machine (192.168.1.10 and 192.168.2.10)

### IP Forwarding
```bash
sudo sysctl -w net.ipv4.ip_forward=1
sudo sh -c 'echo "net.ipv4.ip_forward=1" >> /etc/sysctl.conf'
sudo sysctl -p
```

### Direct DHCP Relay (Alternative to dhcrelay)
Since the dhcrelay service is having issues, we'll use iptables rules to handle DHCP forwarding:

```bash
# Clear existing rules
sudo iptables -F
sudo iptables -t nat -F

# Allow forwarding between interfaces
sudo iptables -A FORWARD -i enp0s3 -o enp0s8 -j ACCEPT
sudo iptables -A FORWARD -i enp0s8 -o enp0s3 -j ACCEPT

# Handle DHCP traffic specifically
sudo iptables -t raw -I PREROUTING -i enp0s3 -p udp --dport 67:68 -j NOTRACK
sudo iptables -t raw -I PREROUTING -i enp0s8 -p udp --dport 67:68 -j NOTRACK
sudo iptables -t raw -I OUTPUT -p udp --sport 67:68 -j NOTRACK

# Make DHCP broadcasts reach the server
sudo iptables -t nat -A PREROUTING -i enp0s3 -p udp --dport 67 -j DNAT --to-destination 192.168.2.1:67
sudo iptables -t nat -A POSTROUTING -o enp0s8 -p udp --dport 67 -j MASQUERADE

# Save iptables rules
sudo dnf install -y iptables-services
sudo systemctl enable iptables
sudo systemctl start iptables
sudo service iptables save
```

### Relay Agent Firewall
```bash
sudo firewall-cmd --permanent --direct --add-rule ipv4 filter FORWARD 0 -i enp0s3 -o enp0s8 -j ACCEPT
sudo firewall-cmd --permanent --direct --add-rule ipv4 filter FORWARD 0 -i enp0s8 -o enp0s3 -j ACCEPT
sudo firewall-cmd --permanent --add-service=dhcp
sudo firewall-cmd --reload
```

## 3. LAN Client

### Network Configuration
```bash
# Configure network to use DHCP
sudo nmcli connection add type ethernet con-name lan-network ifname enp0s3
sudo nmcli connection modify lan-network ipv4.method auto
sudo nmcli connection modify lan-network ipv4.dns-search "est.intra"
sudo nmcli connection up lan-network
```

To force a DHCP renewal:
```bash
sudo dhclient -r
sudo dhclient
```

## Testing Steps

1. **On DMZ Server**:
   ```bash
   sudo systemctl status dhcpd named httpd
   ```

2. **On Relay Agent**:
   ```bash
   # Test forwarding
   ping -c 2 192.168.2.1 
   # Check iptables rules
   sudo iptables -L
   sudo iptables -t nat -L
   ```

3. **On LAN Client**:
   ```bash
   # Check received IP
   ip addr show
   # Check DNS configuration
   cat /etc/resolv.conf
   # Test DNS resolution
   nslookup www.est.intra
   # Test web access
   curl www.est.intra
   ```

These comprehensive configurations address all components of your network setup and should resolve the issues you've been experiencing. The key changes include correctly configuring the DHCP server to handle both networks, setting up proper DNS entries, and implementing direct packet forwarding on the relay agent to bypass the problematic dhcrelay service.



# On the relay agent
# Remove the incorrect default routes
sudo ip route del default via 192.168.1.10 dev enp0s8
sudo ip route del default via 10.0.4.2 dev enp0s9

# The relay agent doesn't need a default route since it's connecting two networks
# Just make sure the direct routes to both networks are correct:
ip route show | grep -v default









frehgirngir
I see the issue in your routing table. Your default route on the relay agent is pointing to 192.168.1.10 (itself) via enp0s8, which is incorrect. This is causing a routing loop. Let's fix this:

```bash
# On the relay agent
# Remove the incorrect default routes
sudo ip route del default via 192.168.1.10 dev enp0s8
sudo ip route del default via 10.0.4.2 dev enp0s9

# The relay agent doesn't need a default route since it's connecting two networks
# Just make sure the direct routes to both networks are correct:
ip route show | grep -v default
```

You should only see direct routes to the networks:
```
192.168.1.0/24 dev enp0s3 proto kernel scope link src 192.168.1.10
192.168.2.0/24 dev enp0s8 proto kernel scope link src 192.168.2.10
10.0.4.0/24 dev enp0s9 proto kernel scope link src 10.0.4.15
```

If you need a default route for external internet access, you can add it through another interface, but not through the internal networks:

```bash
# Only if needed for internet access, add a default route through your external interface
# For example, if enp0s9 is connected to the internet:
sudo ip route add default via 10.0.4.2 dev enp0s9
```

After fixing the routing table, make sure IP forwarding is enabled:
```bash
sudo sysctl -w net.ipv4.ip_forward=1
```

Then try the ping test again from the DMZ server to the LAN interface.
