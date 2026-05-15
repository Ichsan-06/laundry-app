pipeline {
    agent any
    environment {
        // GANTI username_dockerhub dengan username Anda & laundry-app dengan nama repositori Docker Hub Anda
        DOCKER_HUB_REGISTRY = 'ichsan06/washkita-app' 
        DOCKER_CREDENTIALS_ID = 'docker-hub-credentials'
    }
    stages {
        stage('Sediakan Kod') {
            steps {
                checkout scm
            }
        }
        stage('Bina Imej Docker') {
            steps {
                script {
                    // Membuat imej docker berdasarkan nomor build Jenkins & tag latest
                    dockerImage = docker.build("${DOCKER_HUB_REGISTRY}:${env.BUILD_NUMBER}")
                    dockerImageLatest = docker.build("${DOCKER_HUB_REGISTRY}:latest")
                }
            }
        }
        stage('Muat Naik ke Docker Hub') {
            steps {
                script {
                    // Melakukan login dan push otomatis ke Docker Hub
                    docker.withRegistry('', DOCKER_CREDENTIALS_ID) {
                        dockerImage.push()
                        dockerImageLatest.push()
                    }
                }
            }
        }
        stage('Deploy ke VPS') {
            steps {
                sshagent(['vps-ssh-credentials']) {
                    sh """
                        ssh -o StrictHostKeyChecking=no ubuntu@43.173.1.182 "cd ~/laundry-app && docker compose -f docker-compose.dev.yml pull app && docker compose -f docker-compose.dev.yml up -d app"
                    """
                }
            }
        }

    }
}
