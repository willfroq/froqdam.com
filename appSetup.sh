#!/bin/bash

/var/www/froqdam.com/bin/console maintenance
/var/www/froqdam.com/bin/console froq:asset:connect-organization-to-asset-resources
/var/www/froqdam.com/bin/console youwe:pimcore-elasticsearch:populate asset_library --processes=5
/var/www/froqdam.com/bin/console pimcore:search-backend-reindex