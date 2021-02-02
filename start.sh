#!/bin/bash

screen -d -m -S api php artisan serve --port 8002
screen -d -m -S web php artisan serve --port 8000
