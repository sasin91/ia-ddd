#!/usr/bin/env bash

./vessel init
./vessel build
./vessel start
./vessel art key:generate
./vessel art migrate --seed
./vessel art storage:link