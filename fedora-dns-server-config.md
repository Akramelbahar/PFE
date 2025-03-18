It sounds like there might be an issue with your DNS configuration or domain name setup. The domain `dns.client.intra` appears to be incorrectly formatted based on your previous setup.

Based on our previous discussion, you were trying to set up:
- DNS server at `dns.est.intra` (IP: 192.168.1.10)
- Clients would get the hostname `client.est.intra`

If `dns.client.intra` stopped working, it might be because:

1. The domain structure got mixed up (it should be `dns.est.intra` for your DNS server)
2. There might be a typo in your zone files or configuration
3. The DNS service might have stopped or restarted with incorrect configuration

To fix this issue:

1. Check if your named (DNS) service is running:
```bash
sudo systemctl status named
```

2. Verify your zone files are correctly configured:
```bash
sudo cat /var/named/est.intra.zone
```

3. Make sure your dhcpd.conf is correctly setting the domain name:
```bash
sudo cat /etc/dhcp/dhcpd.conf
```

In your dhcpd.conf, confirm you have these lines:
```
option domain-name "est.intra";
option domain-name-servers 192.168.1.10;
```

And in your zone file, make sure you have:
```
dns     IN A    192.168.1.10
```

If you need to access your DNS server, use `dns.est.intra` or simply the IP address 192.168.1.10 directly.
