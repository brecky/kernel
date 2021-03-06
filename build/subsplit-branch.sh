#!/bin/sh

if [ -z "$1" ]; then
    echo "No argument supplied";
    exit 1;
fi

if [ -d .subsplit ]; then
    git subsplit update
else
    git subsplit init git@github.com:orchestral/kernel.git
fi

git subsplit publish --heads=$1 --no-tags src/Config:git@github.com:orchestral/config.git
git subsplit publish --heads=$1 --no-tags src/Database:git@github.com:orchestral/database.git
git subsplit publish --heads=$1 --no-tags src/Http:git@github.com:orchestral/http.git
git subsplit publish --heads=$1 --no-tags src/Notifications:git@github.com:orchestral/notifications.git
git subsplit publish --heads=$1 --no-tags src/Routing:git@github.com:orchestral/routing.git
