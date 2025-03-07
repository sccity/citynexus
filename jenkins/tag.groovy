def branch = sh(script: 'git rev-parse --abbrev-ref HEAD', returnStdout: true).trim()

if (branch == 'HEAD') {
    branch = env.BRANCH_NAME
}

if (!branch || branch == 'HEAD') {
    error('Could not determine branch')
}

def commitHash = sh(script: 'git rev-parse --short HEAD', returnStdout: true).trim()

echo "Branch: ${branch} - Commit Hash: ${commitHash}"

def supportedBranches = ['dev', 'uat', 'prod']
if (!supportedBranches.contains(branch)) {
    error("Unsupported branch '${branch}'")
}

sh "git tag -a v${commitHash} -m 'Build ${commitHash}'"
sh "git tag -a v${commitHash}-${branch} -m 'Build ${commitHash} on ${branch}'"

sh "git push origin v${commitHash}"
sh "git push origin v${commitHash}-${branch}"

writeFile file: 'branch.txt', text: branch
writeFile file: 'commit_hash.txt', text: commitHash