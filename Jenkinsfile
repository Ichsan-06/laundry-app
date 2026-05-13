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
                script {
                    echo 'Memperbarui kontainer Laravel app di VPS...'
                    sh '''
                        # Menggunakan user ubuntu sesuai kredensial yang baru dibuat
                        ssh -o StrictHostKeyChecking=no ubuntu@43.173.1.182'
                            cd /laundry-app-production
                            docker compose pull app
                            docker compose up -d app
                        '
                    '''
                }
            }
        }
    }
}
