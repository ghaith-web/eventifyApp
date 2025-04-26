#!/bin/bash

set -e

NAMESPACE="pfe"
INGRESS_DOMAIN="back.eventify.local"

echo "🧹 Cleaning up deployed resources in namespace '$NAMESPACE'..."

# Delete Laravel app
echo "🗑️ Deleting Laravel Deployment and Services..."
kubectl delete deployment eventify-app -n "$NAMESPACE" --ignore-not-found
kubectl delete deployment eventify-nginx -n "$NAMESPACE" --ignore-not-found
kubectl delete service eventify-nginx -n "$NAMESPACE" --ignore-not-found

# Delete PostgreSQL
echo "🗑️ Deleting PostgreSQL Deployment and Service..."
kubectl delete deployment eventify-pg -n "$NAMESPACE" --ignore-not-found
kubectl delete service eventify-pg -n "$NAMESPACE" --ignore-not-found

# Delete ConfigMap and Ingress
echo "🗑️ Removing ConfigMap and Ingress..."
kubectl delete configmap nginx-config -n "$NAMESPACE" --ignore-not-found
kubectl delete ingress eventify-ingress -n "$NAMESPACE" --ignore-not-found

# Optional: Clean up /etc/hosts
echo "🧽 Checking /etc/hosts for '$INGRESS_DOMAIN' entry..."
if grep -q "$INGRESS_DOMAIN" /etc/hosts; then
  echo "➖ Removing entry from /etc/hosts (sudo required)..."
  sudo sed -i.bak "/$INGRESS_DOMAIN/d" /etc/hosts
  echo "✔️ Removed entry for $INGRESS_DOMAIN"
else
  echo "ℹ️ No entry found for $INGRESS_DOMAIN"
fi

echo ""
echo "✅ Cleanup complete!"
echo "🔁 Namespace '$NAMESPACE' is still available."
