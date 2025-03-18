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
