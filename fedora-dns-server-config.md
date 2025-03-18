ddns-updates on;
ddns-update-style interim;
update-static-leases on;
use-host-decl-names on;

key "ddns-key.est.intra" {
    algorithm hmac-md5;
    secret "abcdefghijklmnopqrstuvwxyz123456";
};

zone est.intra. {
    key ddns-key.est.intra;
    primary 192.168.1.10;
}

zone 1.168.192.in-addr.arpa. {
    key ddns-key.est.intra;
    primary 192.168.1.10;
}

option domain-name "est.intra";
option domain-name-servers 192.168.1.10;
default-lease-time 600;
max-lease-time 7200;
authoritative;

subnet 192.168.1.0 netmask 255.255.255.0 {
    range 192.168.1.50 192.168.1.150;
    option routers 192.168.1.1;
    option broadcast-address 192.168.1.255;
    
    ddns-hostname = concat("client-", binary-to-ascii(10, 8, "-", leased-address));
    ddns-domain-name = "est.intra";
}
