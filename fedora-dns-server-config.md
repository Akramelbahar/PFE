sudo firewall-cmd --list-all
# If needed, open port 53:
sudo firewall-cmd --permanent --add-service=dns
sudo firewall-cmd --reload
