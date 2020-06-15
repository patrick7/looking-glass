Please note: This repo is outdated and not maintained anymore. 


# looking-glass

Implements a Looking Glass for FRRouting.

## Server side

Currently we use [lg2http](https://github.com/msoap/shell2http) to fetch the JSON output from vtysh and forward the port to the remote server using autossh.
This unitfiles are compatible starting from FRRouting v5.0 (vtysh -u parameter).


**Unitfile for shell2http**
```
[Unit]
Description=Starts the FRR LG daemon
After=network.target

[Service]
Type=simple
User=lg
WorkingDirectory=/home/lg/
Restart=on-failure
ExecStart=/home/lg/shell2http -form -host 127.0.0.1 -port 55500 \
        /neighbors      "sudo vtysh -u --vty_socket=/var/run/frr -c \"sh bgp nei json\"|grep -v \"Command not allowed\" " \
        /v4route        "sudo vtysh -u --vty_socket=/var/run/frr -c \"sh bgp ipv4 $v_route json\"|grep -v \"Command not allowed\" " \
        /v6route        "sudo vtysh -u --vty_socket=/var/run/frr -c \"sh bgp ipv6 $v_route json\"|grep -v \"Command not allowed\" "

[Install]
WantedBy=multi-user.target
```

**Unitfile for autossh**
```
[Unit]
Description=AutoSSH tunnel service for LG
After=network.target

[Service]
User=lg
WorkingDirectory=/home/lg/
ExecStart=/usr/bin/autossh -M 0 -N -q -o "ServerAliveInterval 60" -o "ServerAliveCountMax 3" -p 22  -R 55500:localhost:55500 -i /home/lg/.ssh/id_rsa ##remoteserver##

[Install]
WantedBy=multi-user.target
```

