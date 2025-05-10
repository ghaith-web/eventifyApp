# PowerShell script to set up Kubernetes Laravel app using k8s_windows

$ErrorActionPreference = "Stop"

# Variables
$namespace = "pfe"
$ingressDomain = "back.eventify.local"
$k8sDir = "k8s_windows_full"

Write-Host "Creating namespace '$namespace'..."
kubectl apply -f "$k8sDir/namespace.yaml"

Write-Host "Creating PersistentVolumes and PVCs..."
kubectl apply -f "$k8sDir/eventify-local-path-storage.yaml"
kubectl apply -f "$k8sDir/eventify-pvc.yaml"
kubectl apply -f "$k8sDir/postgres-pv.yaml"
kubectl apply -f "$k8sDir/postgres-pvc.yaml"

Write-Host "Deploying PostgreSQL..."
kubectl apply -f "$k8sDir/postgres-deployment.yaml"
kubectl apply -f "$k8sDir/postgres-service.yaml"

Write-Host "Deploying Laravel app..."
kubectl apply -f "$k8sDir/laravel-deployment.yaml"
kubectl apply -f "$k8sDir/laravel-service.yaml"
kubectl apply -f "$k8sDir/laravel-worker-deployment.yaml"

Write-Host "Deploying Nginx reverse proxy..."
kubectl apply -f "$k8sDir/nginx-config.yaml"
kubectl apply -f "$k8sDir/nginx-deployment.yaml"
kubectl apply -f "$k8sDir/eventify-service.yaml"

Write-Host "Deploying Redis..."
kubectl apply -f "$k8sDir/redis-deployment.yaml"
kubectl apply -f "$k8sDir/redis-service.yaml"

Write-Host "Applying Ingress..."
kubectl apply -f "$k8sDir/ingress.yaml"

# Add to hosts file if missing
$hostsPath = "$env:SystemRoot\System32\drivers\etc\hosts"
$hostsEntry = "127.0.0.1 $ingressDomain"

if (-not (Select-String -Path $hostsPath -Pattern $ingressDomain -Quiet)) {
    Write-Host "Adding $hostsEntry to hosts file (admin rights required)..."
    Add-Content -Path $hostsPath -Value $hostsEntry
} else {
    Write-Host "Entry already exists in hosts file."
}

Write-Host "Waiting for Laravel pod to become ready..."
kubectl wait --for=condition=ready pod -l app=eventify-app -n $namespace --timeout=300s

Write-Host "Checking Laravel pod status..."
$laravelPodStatus = kubectl get pod -l app=eventify-app -n $namespace | Select-String "Running"
if (-not $laravelPodStatus) {
    Write-Host "Laravel pod is not running. Fetching logs..."
    kubectl logs -l app=eventify-app -n $namespace
    exit 1
}

Write-Host "Setting up PostgreSQL port-forwarding (15432 -> 5432)..."

# Find any existing port-forward on 15432
$existingPortForward = Get-Process | Where-Object {
    $_.ProcessName -like "kubectl" -and $_.StartInfo.Arguments -match "15432"
}

if ($existingPortForward) {
    Write-Host "Killing old port-forward on 15432..."
    $existingPortForward | Stop-Process -Force
}

Start-Process powershell -WindowStyle Hidden -ArgumentList "kubectl port-forward svc/eventify-pg 15432:5432 -n $namespace"

Start-Sleep -Seconds 2

Write-Host ""
Write-Host "Setup complete!"
Write-Host "Access Laravel at: http://$ingressDomain"
Write-Host "PostgreSQL available at: localhost:15432 (user: postgres / password: postgres)"
Write-Host ""