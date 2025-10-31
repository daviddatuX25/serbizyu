#!/bin/bash

# Start Laravel Sail services in detached mode
./vendor/bin/sail up -d

# Start Vite development server
npm run dev