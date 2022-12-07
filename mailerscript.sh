#!/bin/bash

if [[ -z "${PHP8_PATH}" ]]; then
  PHP8_PATH=php
fi

$PHP8_PATH bin/console messenger:stop-workers
$PHP8_PATH bin/console messenger:consume async &
