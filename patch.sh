#!/bin/bash
git archive $2 -o patch.zip $(git diff $1 $2 --name-only --no-renames --diff-filter=MA)
git diff $1 $2 --name-only --no-renames --diff-filter=D
