# DDNS configuration settings
ddns-updates on;
ddns-update-style interim;
update-static-leases on;
use-host-decl-names on;

# DDNS security key
key "ddns-key.est.intra" {
    algorithm hmac-md5;
    secret "abcdefghijklmnopqrstuvwxyz123456";
};

# Zone definitions for DNS updates
zone est.intra. {
    key ddns-key.est.intra;
    primary 192.168.1.10;
}

zone 1.168.192.in-addr.arpa. {
    key ddns-key.est.intra;
    primary 192.168.1.10;
}

# Global options
option domain-name "est.intra";
option domain-name-servers 192.168.1.10;
default-lease-time 3600;
max-lease-time 86400;
authoritative;

# Subnet configuration
subnet 192.168.1.0 netmask 255.255.255.0 {
    range 192.168.1.50 192.168.1.150;
    option routers 192.168.1.1;
    option broadcast-address 192.168.1.255;
    
    # Generate hostname based on MAC address or let client send hostname
    if exists host-name {
        ddns-hostname = option host-name;
    } else {
        ddns-hostname = concat("client-", binary-to-ascii(16, 8, "-", substring(hardware, 1, 6)));
    }
    
    ddns-domain-name = "est.intra";
    
    # FQDN options to control hostname registration
    option fqdn.no-client-update on;
    option fqdn.server-update on;
    option fqdn.encoded on;
}
