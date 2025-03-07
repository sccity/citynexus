def branch = env.BRANCH_NAME
if (!branch) {
    branch = sh(script: 'git rev-parse --abbrev-ref HEAD', returnStdout: true).trim()
}
def commitHash = sh(script: 'git rev-parse --short HEAD', returnStdout: true).trim()

echo "Branch: ${branch} - Commit Hash: ${commitHash}"

def supportedBranches = ['dev', 'uat', 'prod']
if (!supportedBranches.contains(branch)) {
    error("Unsupported branch '${branch}'")
}

withCredentials([usernamePassword(credentialsId: 'github', usernameVariable: 'GIT_USERNAME', passwordVariable: 'GIT_PASSWORD')]) {
    sh '''
        git config --global user.email "jenkins@santaclarautah.gov"
        git config --global user.name "Jenkins CI"
        git remote set-url origin https://${GIT_USERNAME}:${GIT_PASSWORD}@github.com/sccity/citynexus.git
    '''
}

sh "git tag -a v${commitHash} -m 'Build ${commitHash}'"
sh "git tag -a v${commitHash}-${branch} -m 'Build ${commitHash} on ${branch}'"
sh "git push origin v${commitHash}"
sh "git push origin v${commitHash}-${branch}"

writeFile file: 'branch.txt', text: branch
writeFile file: 'commit_hash.txt', text: commitHash