#!/bin/bash
echo "Pruning git repository..."
git gc --aggressive --prune=now
git reflog expire --all --expire=now
if [ "$?" != "0" ]; then
	echo "Prune failed. Check above for errors."
else
	echo "Successfully pruned."
fi
exit
