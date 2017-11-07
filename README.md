# nav_elastic


## Install

Enable mod_rewrite on Apache2

Add to Apache2 sites-available
`<Directory /var/www/html/>`
        Options Indexes FollowSymLinks MultiViews
        AllowOverride All
        Order allow,deny
        allow from all
`</Directory>`


Run: 

mkdir upload

chown -R www-data:www-data upload

create inc/config.php

curl -s http://getcomposer.org/installer | php

php composer.phar install --no-dev

git submodule init

git submodule update


## System requirements

php5-curl

php5-json > 1.3.7


## Developers 

Tiago Rodrigo Marçal Murakami


Jan Leduc de Lara

## Credits

jQuery Form Validator - http://www.formvalidator.net

Uikit - https://getuikit.com

Elasticsearch - https://www.elastic.co/products/elasticsearch

OAI-PMH (Package Metadata Harvesting) 2.0 Data Provider - https://github.com/danielneis/oai_pmh
