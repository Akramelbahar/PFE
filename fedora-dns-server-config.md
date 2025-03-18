I see the error now. You have permission issues with your named configuration:

```
named[3009]: isc_stdio_open '/var/log/named.ddns.log' failed: permission denied
named[3009]: configuring logging: permission denied
named[3009]: loading configuration: permission denied
named[3009]: exiting (due to fatal error)
```

This indicates that the named process doesn't have permission to access the log file and possibly other configuration files. Let's fix these permission issues:

1. First, create the log directory and file with proper permissions:
```bash
sudo mkdir -p /var/log
sudo touch /var/log/named.ddns.log
sudo chown named:named /var/log/named.ddns.log
sudo chmod 664 /var/log/named.ddns.log
```

2. Make sure the named user has access to all relevant files:
```bash
sudo chown -R named:named /var/named
sudo chown named:named /etc/named.conf
```

3. Check SELinux contexts (if SELinux is enabled):
```bash
# Set the correct SELinux context for the log file
sudo semanage fcontext -a -t named_log_t "/var/log/named.ddns.log"
sudo restorecon -v /var/log/named.ddns.log

# Allow named to write logs
sudo setsebool -P named_write_master_zones 1
```

4. Simplify your named.conf temporarily by removing the logging section to see if that's causing issues. You can add it back later once the service is working.

After making these changes, try to restart the named service:

```bash
sudo systemctl restart named
```

These permission fixes should resolve the issues with starting the named service. Once it's running correctly, you can test the DNS and DHCP integration.To update your named (BIND) configuration to work with the modified DHCP configuration where all clients will have the hostname "client.est.intra", I'll provide you with the necessary changes.

Here's an updated named.conf file that will work properly with your new DHCP configuration:

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
    dnssec-validation no;
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

logging {
    channel default_debug {
        file "data/named.run";
        severity dynamic;
    };
    
    channel ddns_log {
        file "/var/log/named.ddns.log";
        severity debug;
        print-severity yes;
        print-time yes;
    };
    
    category update { ddns_log; };
    category security { ddns_log; };
};
```

Here's an updated forward zone file (est.intra.zone):

```
$TTL 86400
@       IN SOA  dns.est.intra. admin.est.intra. (
                2023011003 ; Serial
                3600       ; Refresh
                1800       ; Retry
                604800     ; Expire
                86400      ; Minimum TTL
)
@       IN NS   dns.est.intra.
dns     IN A    192.168.1.10
; The following line is optional but can be a placeholder
; for the dynamic entry that DHCP will update
client  IN A    192.168.1.100
```

And here's an updated reverse zone file (est.intra.rev):

```
$TTL 86400
@       IN SOA  dns.est.intra. admin.est.intra. (
                2023011003 ; Serial
                3600       ; Refresh
                1800       ; Retry
                604800     ; Expire
                86400      ; Minimum TTL
)
@       IN NS   dns.est.intra.
10      IN PTR  dns.est.intra.
; The following line is optional but can be a placeholder
; for the dynamic entry that DHCP will update
100     IN PTR  client.est.intra.
```

After updating these files, set the proper permissions and restart the services:

```bash
sudo chown named:named /var/named/est.intra.zone
sudo chown named:named /var/named/est.intra.rev
sudo chmod 664 /var/named/est.intra.zone
sudo chmod 664 /var/named/est.intra.rev
sudo touch /var/log/named.ddns.log
sudo chown named:named /var/log/named.ddns.log

sudo systemctl restart named
sudo systemctl restart dhcpd
```

This configuration will allow your BIND server to accept dynamic updates from the DHCP server, which will register all clients with the same hostname "client.est.intra". Just be aware that since all clients will have the same DNS name, the most recent client to connect will be the one that resolves when you look up "client.est.intra".
