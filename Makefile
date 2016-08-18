VERSION=$(shell head -n 1 VERSION)

all: zip

clean:
	rm -fr *-stamp
	rm -fr debian/zentaophp
	rm -fr zentaoPHP-*.tgz
	rm -fr package.xml
	rm -fr ztphp*
	rm -fr *.zip
	rm -fr zentaophp
	rm -fr ztphpapi
pear:
	cp app/cli/ztphp.sh ./ztphp
	cp app/cli/ztphp.bat ./ztphp.bat
	cp package/pear/package.xml .
	pear package

pear-install:
	sudo pear uninstall zentaophp
	sudo pear install zentaoPHP*.tgz

deb:
	dpkg-buildpackage -rfakeroot

zip:
	mkdir zentaophp
	cp -fr {config,db,favicon.ico,framework,index.php,js,lib,module,theme,.htaccess} zentaophp
	rm -fr zentaophp/config/my.php
	rm -fr zentaophp/framework/tests
	rm -fr zentaophp/config/sites/*
	mkdir zentaophp/tmp/log -p
	chmod 777 -R zentaophp/tmp/log
	find zentaophp -name .git |xargs rm -fr
	zip -r -9 zentaoPHP.$(VERSION).zip zentaophp
	rm -fr zentaophp
ztphpdoc:
	phpdoc -d framework,lib -t ztphpapi -o HTML:frames:phphtmllib -ti "zentaophp framework help" -s on -pp on -i *test*
	phpdoc -d framework,lib -t ztphpapi.chm -o chm:default:default -ti "zentaophp framework help" -s on -pp on -i *test*
