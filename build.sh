#!/bin/bash

echo "gulp build"
gulp build

echo "собираем скрипты приложения"
node www/static/develop/builder/r.js -o www/static/develop/builder/app.config.js

node www/static/develop/builder/r.js -o www/static/develop/builder/add.config.js
