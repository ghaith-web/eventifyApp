#!/bin/bash

set -e

# Variables
IMAGE_NAME="ghaithbenali/eventify-v2:latest"
DEPLOYMENT_NAME="eventify-app"
NAMESPACE="pfe"

echo "🔨 Building Docker image without cache..."
docker build --no-cache -f docker/Dockerfile -t $IMAGE_NAME .

echo "📤 Pushing image to DockerHub..."
docker push $IMAGE_NAME

