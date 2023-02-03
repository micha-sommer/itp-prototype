#!/bin/bash

PHP8_PATH="/usr/local/php8.1/bin/php"

cd www.thueringer-judoverband.de/anmeldung

$PHP8_PATH bin/console messenger:stop-workers
$PHP8_PATH bin/console messenger:consume async &
