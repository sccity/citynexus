#!/bin/bash

# Get the current branch
branch=$(git rev-parse --abbrev-ref HEAD)

# If we're in a detached HEAD state, try to get the branch from the environment
if [ "$branch" = "HEAD" ]; then
    branch=$BRANCH_NAME
fi

# If we still don't have a branch, error out
if [ -z "$branch" ] || [ "$branch" = "HEAD" ]; then
    echo "Error: Could not determine branch"
    exit 1
fi

# Get the commit hash
commit_hash=$(git rev-parse --short HEAD)

echo "Branch: ${branch} - Commit Hash: $commit_hash"

# Only proceed if we're on a supported branch
if [ "$branch" != "dev" ] && [ "$branch" != "uat" ] && [ "$branch" != "prod" ]; then
    echo "Error: Unsupported branch '$branch'"
    exit 1
fi

# Create tags
git tag -a "v${commit_hash}" -m "Build ${commit_hash}"
git tag -a "v${commit_hash}-${branch}" -m "Build ${commit_hash} on ${branch}"

# Push tags
git push origin "v${commit_hash}"
git push origin "v${commit_hash}-${branch}"

# Save branch for other scripts
echo $branch > branch.txt