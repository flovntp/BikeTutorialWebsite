#!/bin/bash
# Script used to prepare for eZ Publish/Platform archives
#
# Pre-requirement for LTS (EE) archives:
#     auth.json needs to be placed in either root directory or COMPOSER_HOME.
#     If auth.json is placed in COMPOSER_HOME, it needs to be for same user as the one executing script.


# Install all composer deps non-interactivly
composer install -n --prefer-dist --no-dev --no-scripts

# Rename .gitignore to make it optional as it is optimized for kernel development and not project development
mv .gitignore .gitignore.dist

# Archive: Remove cache (wrong paths), logs (generated by composer call above) & zeta tests (too big)
rm -Rf ezpublish/cache/*/* ezpublish/logs/* vendor/zetacomponents/*/tests

echo << EOF
Ready to archive the result

Assuming current folder is build/ezpublish5 this can be accomplished in the following way:
    cd ../..
    tar czf dist/ezpublish5-\$version-ee-gpl-full.tar.gz --directory=build ezpublish5
EOF
