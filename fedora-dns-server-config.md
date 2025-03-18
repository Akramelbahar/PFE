I see the issue. Your DHCP client is receiving an IP address (192.168.1.108) successfully, but there's a permission denied error when trying to update /etc/resolv.conf, and DNS reverse lookup is still failing.

Let's fix the configuration on both the server and client sides:

### 1. Server-side fixes

First, fix the reverse DNS lookup issue:

1. Edit the reverse zone file on your DNS server:
```bash
sudo nano /var/named/est.intra.rev
```

2. Ensure it contains these lines:
```
$TTL 86400
@       IN SOA  dns.est.intra. admin.est.intra. (
                2023011006 ; Serial
                3600       ; Refresh
                1800       ; Retry
                604800     ; Expire
                86400      ; Minimum TTL
)
@       IN NS   dns.est.intra.
10      IN PTR  dns.est.intra.
108     IN PTR  client.est.intra.
```

3. Restart the named service:
```bash
sudo systemctl restart named
```

### 2. Client-side fixes

The "Permission denied" error suggests the dhclient script doesn't have permission to update /etc/resolv.conf. This could be because resolvconf is running or the file is write-protected.

1. Fix the resolv.conf permissions:
```bash
sudo chattr -i /etc/resolv.conf  # Remove immutable flag if present
sudo chmod 644 /etc/resolv.conf  # Make it writable
```

2. Manually update resolv.conf to use your DNS server:
```bash
echo "nameserver 192.168.1.10" | sudo tee /etc/resolv.conf
echo "search est.intra" | sudo tee -a /etc/resolv.conf
```

3. Request a new IP lease:
```bash
sudo dhclient -r
sudo dhclient
```

4. Verify that DNS is working:
```bash
nslookup dns.est.intra
nslookup client.est.intra
```

5. Verify the hostname is set correctly:
```bash
hostname
hostname -f
```

If the hostname isn't set to "client.est.intra", set it manually:
```bash
sudo hostnamectl set-hostname client.est.intra
```

These changes should fix the DNS resolution and ensure your client machine gets the domain name "client.est.intra" as intended.
