#!/bin/bash

# Get the current branch
def branch = sh(script: 'git rev-parse --abbrev-ref HEAD', returnStdout: true).trim()

# If we're in a detached HEAD state, try to get the branch from the environment
if (branch == 'HEAD') {
    branch = env.BRANCH_NAME
}

# If we still don't have a branch, error out
if (!branch || branch == 'HEAD') {
    error('Could not determine branch')
}

# Get the commit hash
def commitHash = sh(script: 'git rev-parse --short HEAD', returnStdout: true).trim()

echo "Branch: ${branch} - Commit Hash: ${commitHash}"

# Only proceed if we're on a supported branch
def supportedBranches = ['dev', 'uat', 'prod']
if (!supportedBranches.contains(branch)) {
    error("Unsupported branch '${branch}'")
}

# Create tags
sh "git tag -a v${commitHash} -m 'Build ${commitHash}'"
sh "git tag -a v${commitHash}-${branch} -m 'Build ${commitHash} on ${branch}'"

# Push tags
sh "git push origin v${commitHash}"
sh "git push origin v${commitHash}-${branch}"

# Save branch for other scripts
writeFile file: 'branch.txt', text: branch