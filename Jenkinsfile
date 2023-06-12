pipeline {
  agent any

  stages {
    stage('Checkout') {
      steps {
        git 'https://github.com/<username>/<repository>.git'
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
testing...