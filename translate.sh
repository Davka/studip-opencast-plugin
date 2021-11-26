#!/bin/bash

PO=locale/en/LC_MESSAGES/opencast.po
POTPHP=locale/en/LC_MESSAGES/opencast_php.pot
POTJS=locale/en/LC_MESSAGES/opencast_js.pot
POT=locale/en/LC_MESSAGES/opencast.pot
MO=locale/en/LC_MESSAGES/opencast.mo

rm -f $POT
rm -f $POTPHP

find * \( -iname "*.php" -o -iname "*.ihtml" \) | xargs xgettext --from-code=UTF-8 --add-location=full --package-name=Opencast --language=PHP -o $POTPHP

msgcat $POTJS $POTPHP -o $POT
msgmerge $PO $POT -o $PO
msgfmt $PO --output-file=$MO
