def branch = env.BRANCH_NAME
if (!branch) {
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

sh 'git config --global user.email "jenkins@santaclarautah.gov"'
sh 'git config --global user.name "Jenkins CI"'

withCredentials([usernamePassword(credentialsId: 'git', usernameVariable: 'GIT_USERNAME', passwordVariable: 'GIT_PASSWORD')]) {
    sh """
        git config credential.helper '!f() { echo username=\${GIT_USERNAME}; echo password=\${GIT_PASSWORD}; }; f'
        git tag -a v${commitHash}-${branch} -m 'Build ${commitHash} on ${branch}'
        git push origin v${commitHash}-${branch}
    """
}

writeFile file: 'branch.txt', text: branch
writeFile file: 'commit_hash.txt', text: commitHash