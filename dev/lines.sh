#!/bin/bash

DIR=$( cd "$( dirname "${BASH_SOURCE[0]} " )" && pwd )
cd $DIR

#get number of lines of code in src directory that isn't external
find ../src -type f ! -name '.DS_Store' \
    ! -path '*/docs/*' \
| xargs wc -l

exit
