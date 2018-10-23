A custom module for [discountkitchenfactory.co.uk](https://discountkitchenfactory.co.uk).

## How to install
```
/opt/plesk/php/7.1/bin/php bin/magento maintenance:enable
composer clear-cache
composer require discountkitchenfactory/core:*
/opt/plesk/php/7.1/bin/php bin/magento setup:upgrade
rm -rf var/di var/generation generated/code && /opt/plesk/php/7.1/bin/php bin/magento setup:di:compile
rm -rf pub/static/* && /opt/plesk/php/7.1/bin/php bin/magento setup:static-content:deploy en_US en_GB
/opt/plesk/php/7.1/bin/php bin/magento cache:clean && sudo apachectl restart && sudo nginx -s reload
/opt/plesk/php/7.1/bin/php bin/magento maintenance:disable
```

## How to upgrade
```
/opt/plesk/php/7.1/bin/php bin/magento maintenance:enable
composer clear-cache
composer update discountkitchenfactory/core
/opt/plesk/php/7.1/bin/php bin/magento setup:upgrade
rm -rf var/di var/generation generated/code && /opt/plesk/php/7.1/bin/php bin/magento setup:di:compile
rm -rf pub/static/* && /opt/plesk/php/7.1/bin/php bin/magento setup:static-content:deploy en_US en_GB
/opt/plesk/php/7.1/bin/php bin/magento cache:clean && sudo apachectl restart && sudo nginx -s reload
/opt/plesk/php/7.1/bin/php bin/magento maintenance:disable
```

If you have problems with these commands, please check the [detailed instruction](https://mage2.pro/t/263).