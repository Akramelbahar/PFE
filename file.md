If you can reach the IP address but not the domain name from the relay agent, this is definitely a DNS resolution issue rather than a connectivity problem. Let's fix this step by step:

1. **First, let's verify connectivity to the IP address**:
```bash
ping -c 4 192.168.2.1
curl http://192.168.2.1
```

2. **Check your current DNS configuration**:
```bash
cat /etc/resolv.conf
```

3. **Fix DNS resolution by updating NetworkManager config**:
```bash
# Create a custom NetworkManager DNS configuration
sudo nano /etc/NetworkManager/conf.d/dns.conf
```

Add these lines:
```
[main]
dns=none
systemd-resolved=false
```

4. **Create a custom resolv.conf that won't be overwritten**:
```bash
sudo rm /etc/resolv.conf
sudo nano /etc/resolv.conf
```

Add these lines:
```
nameserver 192.168.2.1
search est.intra
```

5. **Make resolv.conf immutable so it doesn't get overwritten**:
```bash
sudo chattr +i /etc/resolv.conf
```

6. **Restart NetworkManager**:
```bash
sudo systemctl restart NetworkManager
```

7. **Test DNS resolution**:
```bash
nslookup www.est.intra
```

8. **If the above doesn't work, check if your DNS server is actually responding to queries**:
```bash
# Test if the DNS server responds
dig @192.168.2.1 www.est.intra
```

9. **Check firewall on DNS server**:
Make sure the DNS server allows DNS traffic from the relay agent:
```bash
# On DNS server
sudo firewall-cmd --permanent --add-rich-rule='rule family="ipv4" source address="192.168.2.10" service name="dns" accept'
sudo firewall-cmd --reload
```

10. **Verify zone files on DNS server**:
Make sure the zone files on the DNS server have the correct entries for www.est.intra.

If after all these steps you still can't resolve the domain but can reach the IP, there might be an issue with the DNS server configuration itself. You might need to check the DNS server logs:

```bash
# On DNS server
sudo journalctl -u named
```

This systematic approach should help identify and resolve the DNS resolution issue on your relay agent.
