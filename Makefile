VERSION=$(shell head -n 1 VERSION)

all: pear zip

clean:
	rm -fr *-stamp
	rm -fr debian/zentaophp
	rm -fr ZenTaoPHP-*.tgz
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
	sudo pear install ZenTaoPHP*.tgz

deb:
	dpkg-buildpackage -rfakeroot

zip:
	mkdir zentaophp
	cp -fr app zentaophp
	cp -fr framework zentaophp
	cp -fr lib zentaophp
	cp doc/COPY* zentaophp
	cp doc/README zentaophp
	rm -fr zentaophp/app/pms
	rm -fr zentaophp/app/cli
	rm -fr zentaophp/framework/tests
	rm -fr zentaophp/app/cli/test
	find zentaophp -name .svn |xargs rm -fr
	zip -r -9 ZenTaoPHP.$(VERSION).zip zentaophp
	rm -fr zentaophp
ztphpdoc:
	phpdoc -d framework,lib -t ztphpapi -o HTML:frames:phphtmllib -ti ZenTaoPHP¿ò¼ÜAPI²Î¿¼ÊÖ²á -s on -pp on -i *test*
	phpdoc -d framework,lib -t ztphpapi.chm -o chm:default:default -ti ZenTaoPHP¿ò¼ÜAPI²Î¿¼ÊÖ²á -s on -pp on -i *test*
