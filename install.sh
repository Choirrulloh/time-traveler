#!/bin/sh

# Make necessary directories
sudo mkdir /etc/timetraveler
sudo mkdir /var/log/timetraveler

# Copy files to proper locations
sudo cp config /etc/timetraveler/config
sudo cp time-traveler.php /usr/bin/timetraveler

# Make the program executable
sudo chmod +x /usr/bin/timetraveler