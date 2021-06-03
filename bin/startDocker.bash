#!/bin/bash

rm -rf tmp/ && mkdir tmp/  && chmod 777 tmp/;

docker-compose up -d
