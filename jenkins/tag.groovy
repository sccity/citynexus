def branch = env.BRANCH_NAME
if (!branch) {
    // Try to get branch from remote tracking branch
    branch = sh(script: 'git branch -r --contains HEAD | grep -v HEAD | sed "s/origin\\///" | head -n1 | tr -d " "', returnStdout: true).trim()
    if (!branch) {
        error("Could not determine branch name")
    }
}
def commitHash = sh(script: 'git rev-parse --short HEAD', returnStdout: true).trim()

echo "Branch: ${branch} - Commit Hash: ${commitHash}"

def supportedBranches = ['dev', 'uat', 'prod']
if (!supportedBranches.contains(branch)) {
    error("Unsupported branch '${branch}'")
}

// Configure Git user
sh 'git config --global user.email "jenkins@santaclarautah.gov"'
sh 'git config --global user.name "Jenkins CI"'

sh "git tag -a v${commitHash} -m 'Build ${commitHash}'"
sh "git tag -a v${commitHash}-${branch} -m 'Build ${commitHash} on ${branch}'"
sh "git push origin v${commitHash}"
sh "git push origin v${commitHash}-${branch}"

writeFile file: 'branch.txt', text: branch
writeFile file: 'commit_hash.txt', text: commitHash