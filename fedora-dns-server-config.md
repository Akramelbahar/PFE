DNS Configuration (BIND):

Main config: /etc/named.conf
Forward zone file: /var/named/est.intra.zone
Reverse zone file: /var/named/est.intra.rev
DDNS key (if separate): /etc/named/ddns-key.est.intra
Log files: /var/log/named.log or /var/named/data/named.run


DHCP Configuration:

Main config: /etc/dhcp/dhcpd.conf
Interface settings: /etc/sysconfig/dhcpd
Leases database: /var/lib/dhcpd/dhcpd.leases
Log files: /var/log/messages or through journalctl




I see the issue - your reverse DNS lookup for 192.168.1.108 is failing with an NXDOMAIN error. This means the DNS server doesn't have a record for this IP address.

Let's fix your reverse DNS zone file to make it properly handle client IP addresses. The issue is that your reverse zone file doesn't contain entries for the client IPs, and the dynamic DNS updates aren't working properly.

Here's what you need to do:

1. Edit your reverse zone file:
```bash
sudo nano /var/named/192.168.1.rev
```

2. Make sure your reverse zone file has the correct format and includes an entry for 192.168.1.108:
```
$TTL 86400
@       IN SOA  dns.est.intra. root.est.intra. (
                2023011011      ; Serial
                3600            ; Refresh
                1800            ; Retry
                604800          ; Expire
                86400 )         ; Minimum TTL

@       IN NS   dns.est.intra.
10      IN PTR  dns.est.intra.
108     IN PTR  client.est.intra.
```

3. After saving the file, reset permissions and restart the DNS service:
```bash
sudo chown named:named /var/named/192.168.1.rev
sudo chmod 664 /var/named/192.168.1.rev
sudo systemctl restart named
```

4. Then try the reverse lookup again:
```bash
nslookup 192.168.1.108
```

If you want all client IPs to automatically get reverse entries, make sure your DHCP server's dynamic DNS updates are working properly. The key part in your dhcpd.conf is ensuring the reverse zone is configured correctly:

```
zone 1.168.192.in-addr.arpa. {
    primary 127.0.0.1;
    key ddns-key;
}
```

And make sure both your DNS and DHCP servers are restarted after making these changes.
