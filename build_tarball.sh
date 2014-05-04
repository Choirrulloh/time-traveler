#!/bin/sh

VERSION="1.0_beta"

if [ ! -d tarballs ]; then
	mkdir tarballs
fi

if [ -d tarballs/temp ]; then
	rm -r tarballs/temp
fi

mkdir tarballs/temp
cp config tarballs/temp/.
cp install.sh tarballs/temp/.
cp LICENSE tarballs/temp/.
cp README.md tarballs/temp/.
cp time-traveler.php tarballs/temp/timetraveler
cp uninstall.sh tarballs/temp/.

cd tarballs
mv temp time-traveler-${VERSION}
tar -zcvf time-traveler-${VERSION}.tar.gz time-traveler-${VERSION}