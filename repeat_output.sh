address="192.168.43.128"
port="10008"

for i in {1..1000};
do
	telnet $address $port >  /dev/null &
done
