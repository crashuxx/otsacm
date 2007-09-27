RM = rm
RM_FLAGS = -fr
GZIP = gzip
GZIP_FLAGS = -f9
TAR = tar
TAR_FLAGS = -cf
PHP = php
PHPDOC = phpdoc
PHPDOC_FLAGS = -d ./ -t documentation -i config.php -ric README,LICENSE -ti 'Account Manager for OTServ'
VERSION = 3Alpha
PACKAGE = acm$(VERSION)
FILES = Makefile README kernel kernel/footer.php kernel/class/db/mysqli.php kernel/class/db/sqlite.php kernel/class/db/mysql.php kernel/class/phpmailer.php kernel/class/session.php kernel/class/smtp.php kernel/header_admin.php kernel/finalize.php kernel/index.html kernel/template/error.htpl kernel/template/colors.php kernel/template/message.htpl kernel/template/main.htpl kernel/.htaccess kernel/debug.php kernel/footer_admin.php kernel/common.php kernel/functions.php kernel/header.php admin_profiles.php lang/English/options.php lang/English/mail lang/English/mail/activate_password.tpl lang/English/mail/register.tpl lang/English/mail/index.html lang/English/players.php lang/English/phpmailer.php lang/English/create.php lang/English/register.php lang/English/editor.php lang/English/common.php lang/English/login.php LICENSE admin_editor.php cron.php create.php admin_options.php install.php index.php login.php style/default.css cache/index.html cache/.htaccess admin_editprofile.php players.php install/mysql.sql install/style.css chpass.php register.php .htaccess img/delete.png admin.php uninstall.php

all: check package

clean:
	$(RM) $(RM_FLAGS) documentation $(PACKAGE).tar.gz cache/*.dump cache/*.tmp
	
check:
	find . -name "*.php" -exec php -l {} \;

doc:
	$(RM) $(RM_FLAGS) documentation
	$(PHPDOC) $(PHPDOC_FLAGS) -o HTML:frames:earthli
	
docpdf: 
	$(RM) $(RM_FLAGS) documentation/*.pdf
	$(PHPDOC) $(PHPDOC_FLAGS) -o PDF:default:default

package:
	$(TAR) $(TAR_FLAGS) $(PACKAGE).tar $(FILES)
	$(GZIP) $(GZIP_FLAGS) $(PACKAGE).tar

uninstall:
	$(PHP) uninstall.php
