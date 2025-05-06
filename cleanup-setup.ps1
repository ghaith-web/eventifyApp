# --------------------------------------------------------
# HOW TO RUN THIS SCRIPT:
# --------------------------------------------------------
# 1. Open PowerShell as Administrator (required for hosts file edit)
# 2. Set execution policy (if needed):
#    Set-ExecutionPolicy Bypass -Scope Process -Force
# 3. Run:
#    .\cleanup.ps1
# --------------------------------------------------------

# Variables
# PowerShell script to clean up Kubernetes resources for eventify in Windows

$ErrorActionPreference = "Stop"

$namespace = "pfe"
$ingressDomain = "back.eventify.local"
$hostsPath = "$env:SystemRoot\System32\drivers\etc\hosts"

Write-Host "🧹 Cleaning up deployed resources in namespace '$namespace'..."

# Delete Laravel app and Nginx
Write-Host "🗑️ Deleting Laravel and Nginx Deployments and Services..."
kubectl delete deployment eventify-app -n $namespace --ignore-not-found
kubectl delete deployment eventify-nginx -n $namespace --ignore-not-found
kubectl delete service eventify-nginx -n $namespace --ignore-not-found
kubectl delete service eventify-app -n $namespace --ignore-not-found

# Delete PostgreSQL
Write-Host "🗑️ Deleting PostgreSQL Deployment and Service..."
kubectl delete deployment eventify-pg -n $namespace --ignore-not-found
kubectl delete service eventify-pg -n $namespace --ignore-not-found

# Delete ConfigMap and Ingress
Write-Host "🗑️ Removing ConfigMap and Ingress..."
kubectl delete configmap nginx-config -n $namespace --ignore-not-found
kubectl delete ingress eventify-ingress -n $namespace --ignore-not-found

# Delete PVCs and PVs
Write-Host "📦 Removing PVCs and PVs..."
kubectl delete pvc eventify-pvc -n $namespace --ignore-not-found
kubectl delete pvc eventify-pgdata -n $namespace --ignore-not-found
kubectl delete pv eventify-pv --ignore-not-found
kubectl delete pv eventify-pgdata-pv --ignore-not-found

# Optional: Clean up hosts file
Write-Host "🧽 Checking hosts file for '$ingressDomain' entry..."
if (Select-String -Path $hostsPath -Pattern $ingressDomain -Quiet) {
    Write-Host "➖ Removing entry from hosts file (admin required)..."
    $original = Get-Content $hostsPath
    $filtered = $original | Where-Object {$_ -notmatch $ingressDomain}
    $filtered | Set-Content $hostsPath
    Write-Host "✔️ Removed entry for $ingressDomain"
} else {
    Write-Host "ℹ️ No entry found for $ingressDomain"
}

Write-Host ""
Write-Host "✅ Cleanup complete!"
Write-Host "🔁 Namespace '$namespace' is still available (you can delete it manually if needed)."
