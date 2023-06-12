pipeline {
  agent any

  stages {
    stage('Checkout') {
      steps {
        git 'https://github.com/Aahil13/WordPress-CI-CD-Pipeline-Using-Jenkins.giit'
      }
    }

    stage('Build') {
      steps {
        docker compose build
      }
    }

    stage('Deploy') {
      steps {
        docker compose up -d
      }
    }
  }
}
