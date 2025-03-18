If your new clients aren't being automatically added to the DNS zone files, there could be several reasons:

1. **Permission issues**: The named process needs write access to the zone files and their directories.
   ```bash
   sudo chown -R named:named /var/named
   sudo chmod 664 /var/named/est.intra.zone
   sudo chmod 664 /var/named/est.intra.rev
   ```

2. **SELinux restrictions**: SELinux might be preventing named from modifying the zone files.
   ```bash
   sudo setsebool -P named_write_master_zones 1
   sudo restorecon -rv /var/named
   ```

3. **DDNS key issues**: The key must be identical in both named.conf and dhcpd.conf.

4. **Check logs for errors**: Look for specific error messages.
   ```bash
   sudo journalctl -u named
   sudo journalctl -u dhcpd
   ```

5. **Verify named is listening for dynamic updates**:
   ```bash
   sudo netstat -tulnp | grep 53
   ```

6. **Test a manual update** to see if the issue is with DHCP or DNS:
   ```bash
   echo "server 127.0.0.1
   zone est.intra
   update add testclient.est.intra 86400 A 192.168.1.123
   send" > /tmp/nsupdate.txt
   
   sudo nsupdate -k /etc/named/ddns-key.est.intra -v /tmp/nsupdate.txt
   ```

7. **Check zone file setup**: Make sure your zone files have the correct format with SOA records.

8. **Restart services after making changes**:
   ```bash
   sudo systemctl restart named
   sudo systemctl restart dhcpd
   ```

9. **Zone journal files** might be corrupted. Try removing them:
   ```bash
   sudo rm /var/named/*.jnl
   sudo systemctl restart named
   ```

The most common issues are permission problems and SELinux restrictions. Make those changes first and restart the services, then try connecting a new client.
