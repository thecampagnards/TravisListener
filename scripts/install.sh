#!/bin/bash
set -e

if [[ $# -eq 0 ]] ; then
    echo "Require pseudo/repo-name like thecampagnards/Portfolio"
    exit 1
fi

repo_git=$1
# repo name in lowercase
project_lower=$(echo "$repo_git" | cut -d / -f 2 | tr '[:upper:]' '[:lower:]')
project_dir=/home/"$project_lower"

if [ ! -d "$project_dir" ]; then
    mkdir "$project_dir"
fi

cd "$project_dir"
curl https://api.github.com/repos/"$repo_git"/releases/latest | grep "$project_lower-.*.zip" | cut -d : -f 2,3 | tr -d \" | wget -O output.zip -qi - && wait
if [ $? -ne 0 ]; then
    # Delete the www dir
    if [ -d www ]; then
        rm -rf www
    fi
    # Unpackage archive
    unzip output.zip -d www
    # File to keep
    if [ -d .keep ]; then
        cp -aR .keep/. www/
    fi
    chown -R www-data:www-data www
    # Special script for project
    if [ -f install.sh ]; then
        ./install.sh
    fi

    rm output.zip
    exit 0
fi

rm output.zip
exit 1
