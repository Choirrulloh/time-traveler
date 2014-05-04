time-traveler (beta)
====================
Linux incremental backup utility

This program can used used to backup any Linux/Unix system to an external HDD, a mounted network storage location, etc.

Requirements
------------

- Linux or any Unix based OS
- Rsync
- PHP 5+

Installation
------------

1. Download the tarball for the release you wish to use:

	```shell
	wget https://github.com/virajchitnis/time-traveler/releases/download/v1.0_beta/time-traveler-1.0_beta.tar.gz
	```

2. Untar it in some temporary location using:

	`tar -zxvf time-traveler-1.0_beta.tar.gz`
	
3. Go to the extracted directory:

	`cd time-traveler-1.0_beta`
	
4. Run the installation script:

	`./install.sh`
	
5. Modify the config file to suit your needs:

	`sudo nano /etc/timetraveler/config`
	
6. Add a crontab to the root user:

	`sudo crontab -e`
	
	`@hourly nice -n 10 timetraveler >> /var/log/timetraveler/backup.log 2>&1`
	
License
-------

This software is available under the [MIT License](https://github.com/virajchitnis/time-traveler/blob/master/LICENSE)

Support
-------

If you happen to find any bugs in this software, or have a feature request, please create an issue for it on the  [GitHub](https://github.com/virajchitnis/time-traveler) page. Please also mark the issue appropriately, so if you have discovered a bug, mark the issue as a 'bug', if you have a feature request, mark the issue as an 'enhancement'.