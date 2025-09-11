#!/bin/bash
echo >&2 "	Deleted files :"
git diff $1 $2 --name-only --no-renames --diff-filter=D

echo >&2
echo >&2 -e "	All changed files :"
git diff $1 $2 --name-status --no-renames

if [[ -f patch.zip ]]
then rm patch.zip;
fi
git archive $2 -o patch.zip $(git diff $1 $2 --name-only --no-renames --diff-filter=MA)
