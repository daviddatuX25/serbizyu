#!/bin/bash

# Start Laravel Sail services in detached mode
./vendor/bin/sail up -d

# Reinstall node modules inside the container if missing
./vendor/bin/sail bash -c 'if [ ! -d node_modules ]; then npm install; fi'

# Start Vite inside the Sail container
./vendor/bin/sail npm run dev
