VERSION=$(shell head -n 1 VERSION)

all: pear tgz

clean:
	rm -fr *-stamp
	rm -fr debian/zentaophp
	rm -fr ZenTaoPHP-*.tgz
	rm -fr package.xml
	rm -fr ztphp*
	rm -fr *.tar.gz
	rm -fr zentaophp
	rm -fr ztphpapi
pear:
	cp app/cli/ztphp.sh ./ztphp
	cp app/cli/ztphp.bat ./ztphp.bat
	cp package/pear/package.xml .
	pear package

pear-install:
	sudo pear uninstall zentaophp
	sudo pear install ZenTaoPHP*.tgz

deb:
	dpkg-buildpackage -rfakeroot

tgz:
	mkdir zentaophp
	cp -fr app zentaophp
	cp -fr framework zentaophp
	cp doc/COPY* zentaophp
	cp doc/README zentaophp
	rm -fr zentaophp/app/pms
	rm -fr zentaophp/framework/tests
	rm -fr zentaophp/app/cli/test
	find zentaophp -name .svn |xargs rm -fr
	tar czvf ZenTaoPHP.$(VERSION).tar.gz zentaophp
ztphpdoc:
	phpdoc -d framework -t ztphpapi -o HTML:frames:phphtmllib -ti ZenTaoPHP¿ò¼ÜAPI²Î¿¼ÊÖ²á -s on -pp on -i *test*
