# looking-glass

Implements a Looking Glass for FRRouting.

Server side:
./shell2http -form -host ## IP ## -port ## Port ## \
	/neighbors "vtysh -c \"sh bgp nei json\"" \
	/v4route "vtysh -c \"sh bgp ipv4 \$v_route json\"" \
	/v6route "vtysh -c \"sh bgp ipv6 \$v_route json\""

