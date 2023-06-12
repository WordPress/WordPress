pipeline {
  agent any

  stages {
    stage('Checkout') {
      steps {
        git 'https://github.com/Aahil13/WordPress-CI-CD-Pipeline-Using-Jenkins.git'
      }
    }

    stage('Build') {
      steps {
         sh 'docker-compose -f docker-compose.yml up -d'
      }
    }
  }
}
