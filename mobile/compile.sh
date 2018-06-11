#!/bin/sh
rm -r app/www/*
cp -r dist/* app/www
cd app
cordova build android
echo 'Done'