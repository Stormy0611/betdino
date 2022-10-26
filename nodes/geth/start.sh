#!/bin/bash
rm nohup.out
nohup ./geth --ws --ws.addr "127.0.0.1" --ws.port 9546 --ws.origins 127.0.0.1 --http --syncmode "light" --http.addr "127.0.0.1" --http.api "admin,personal,eth,net,web3" --ws.api "admin,personal,eth,net,web3" --allow-insecure-unlock & disown
