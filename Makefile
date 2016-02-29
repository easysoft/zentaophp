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
	find zentaophp -name .git |xargs rm -fr
	zip -r -9 zentaoPHP.$(VERSION).zip zentaophp
	rm -fr zentaophp
ztphpdoc:
	phpdoc -d framework,lib -t ztphpapi -o HTML:frames:phphtmllib -ti "zentaophp framework help" -s on -pp on -i *test*
	phpdoc -d framework,lib -t ztphpapi.chm -o chm:default:default -ti "zentaophp framework help" -s on -pp on -i *test*
zentao:
	mkdir zentao
	cp -fr framework lib zentao
	rm -rf zentao/framework/tests
	find zentao -name .git |xargs rm -fr
	sed -i "s/^.* function submitButton(.*$$/     public static function submitButton(\$$label = '', \$$misc = '', \$$class = 'btn btn-primary')/g" zentao/lib/front/front.class.php
	sed -i "s/^.* function commonButton(.*$$/     public static function commonButton(\$$label = '', \$$misc = '', \$$class = 'btn btn-default', \$$icon = '')/g" zentao/lib/front/front.class.php
	sed -i "s/^.* function linkButton(.*$$/     public static function linkButton(\$$label = '', \$$link = '', \$$target = 'self', \$$misc = '', \$$class = 'btn btn-default')/g" zentao/lib/front/front.class.php
	php tools/zentao/process.php
	zip -rm -9 zentao.zip zentao
ranzhi:
	mkdir ranzhi
	cp -fr framework lib ranzhi
	rm -rf ranzhi/framework/tests
	find ranzhi -name .git |xargs rm -fr
	php tools/ranzhi/process.php
	zip -rm -9 ranzhi.zip ranzhi
chanzhi:
	mkdir chanzhi
	cp -fr framework lib chanzhi
	rm -rf chanzhi/framework/tests
	find chanzhi -name .git |xargs rm -fr
	sed -i "/\$$this->setModuleName(/i\        \$$this->setCurrentDevice();" chanzhi/framework/control.class.php
	sed -i "/\$$this->setMethodName(/a\        \$$this->setTplRoot();" chanzhi/framework/control.class.php
	sed -i "/\$$this->view->title/a\        if(RUN_MODE == 'front') \$$this->view->layouts = \$$this->loadModel('block')->getPageBlocks(\$$this->moduleName, \$$this->methodName);" chanzhi/framework/control.class.php
	sed -i "/\$$this->view->title/a\        \$$this->view->session = \$$app->session;" chanzhi/framework/control.class.php
	php tools/chanzhi/process.php
	zip -rm -9 chanzhi.zip chanzhi
