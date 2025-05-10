#!/bin/bash

set -e

# Variables
NAMESPACE="pfe"
INGRESS_DOMAIN="back.eventify.local"
APP_PATH="/home/gaith/pfe/eventify"

echo "🔧 Creating namespace '$NAMESPACE'..."
kubectl apply -f k8s/namespace.yaml

echo "📦 Setting up Local Path StorageClass..."
kubectl apply -f k8s/eventify-local-path-storage.yaml

echo "🗄️ Creating PersistentVolumeClaim for PostgreSQL..."
kubectl apply -f k8s/postgres-pvc.yaml

echo "🐘 Deploying PostgreSQL..."
kubectl apply -f k8s/postgres-deployment.yaml
kubectl apply -f k8s/postgres-service.yaml

echo "🟥 Deploying Redis..."
kubectl apply -f k8s/redis-deployment.yaml
kubectl apply -f k8s/redis-service.yaml

echo "🚀 Deploying Laravel app with local code mount..."
kubectl apply -f k8s/laravel-deployment.yaml

echo "📡 Creating Laravel Service (PHP-FPM)..."
kubectl apply -f k8s/laravel-service.yaml

echo "🌐 Setting up Nginx reverse proxy..."
kubectl apply -f k8s/nginx-config.yaml
kubectl apply -f k8s/nginx-deployment.yaml
kubectl apply -f k8s/eventify-service.yaml

echo "🌍 Creating Ingress rule..."
kubectl apply -f k8s/ingress.yaml

echo "🔍 Verifying /etc/hosts entry for '$INGRESS_DOMAIN'..."
if ! grep -q "$INGRESS_DOMAIN" /etc/hosts; then
  echo "➕ Adding 127.0.0.1 $INGRESS_DOMAIN to /etc/hosts (sudo required)..."
  echo "127.0.0.1 $INGRESS_DOMAIN" | sudo tee -a /etc/hosts > /dev/null
else
  echo "✔️ Entry already exists in /etc/hosts."
fi

echo "⏳ Waiting for Laravel pod to become ready..."
kubectl wait --for=condition=ready pod -l app=eventify-app -n "$NAMESPACE" --timeout=300s

echo "🔎 Verifying Laravel pod is running..."
if ! kubectl get pod -l app=eventify-app -n "$NAMESPACE" | grep Running; then
  echo "❌ Laravel pod is not running. Check image or deployment logs:"
  kubectl logs -l app=eventify-app -n "$NAMESPACE"
  exit 1
fi

echo "🔌 Setting up PostgreSQL port-forwarding (15432 -> 5432)..."

# Kill any existing port-forward running on 15432
OLD_PID=$(lsof -ti:15432) || true
if [ ! -z "$OLD_PID" ]; then
  echo "🛑 Closing old port-forward on 15432..."
  kill -9 $OLD_PID
fi

# Start new port-forward silently in background
kubectl port-forward svc/eventify-pg 15432:5432 -n "$NAMESPACE" >/dev/null 2>&1 &
sleep 2

echo ""
echo "✅ Setup complete!"
echo "🌐 Access Laravel at: http://$INGRESS_DOMAIN"
echo "🐘 Access PostgreSQL at: localhost:15432 (user: postgres / password: postgres)"
echo "🎯 Deploying Laravel worker..."
kubectl apply -f k8s/laravel-worker-deployment.yaml
echo ""
