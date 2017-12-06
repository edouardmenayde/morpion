#!/bin/sh

mysql -u root -e "DROP DATABASE morpion";
mysql -u root -e "CREATE DATABASE IF NOT EXISTS morpion";
mysql -u root morpion < seed/main.sql;
mysql -u root morpion < seed/MarkModel.sql;