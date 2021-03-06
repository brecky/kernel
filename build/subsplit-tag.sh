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

git subsplit publish --heads="master 3.4 3.3 3.1" --tags=$1 src/Config:git@github.com:orchestral/config.git
git subsplit publish --heads="master 3.4 3.3 3.1" --tags=$1 src/Database:git@github.com:orchestral/database.git
git subsplit publish --heads="master 3.4 3.3 3.1" --tags=$1 src/Http:git@github.com:orchestral/http.git
git subsplit publish --heads="master 3.4 3.3" --tags=$1 src/Notifications:git@github.com:orchestral/notifications.git
git subsplit publish --heads="master 3.4 3.3 3.1" --tags=$1 src/Routing:git@github.com:orchestral/routing.git
