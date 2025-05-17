# PowerShell script to clean up Kubernetes Laravel app

$namespace = "pfe"
$ingressDomain = "back.eventify.local"
$k8sDir = "k8s_windows"

Write-Host "Deleting deployments and services..."
kubectl delete deployment eventify-app -n $namespace --ignore-not-found
kubectl delete deployment eventify-worker -n $namespace --ignore-not-found
kubectl delete deployment eventify-nginx -n $namespace --ignore-not-found
kubectl delete deployment eventify-pg -n $namespace --ignore-not-found
kubectl delete deployment redis -n $namespace --ignore-not-found

kubectl delete service eventify-app -n $namespace --ignore-not-found
kubectl delete service eventify-nginx -n $namespace --ignore-not-found
kubectl delete service eventify-pg -n $namespace --ignore-not-found
kubectl delete service redis -n $namespace --ignore-not-found

kubectl delete configmap nginx-config -n $namespace --ignore-not-found
kubectl delete ingress eventify-ingress -n $namespace --ignore-not-found

kubectl delete pvc eventify-pvc -n $namespace --ignore-not-found
kubectl delete pvc eventify-pgdata -n $namespace --ignore-not-found

Write-Host "Cleaning hosts file entry for '$ingressDomain'..."
$hostsPath = "$env:SystemRoot\System32\drivers\etc\hosts"
if (Test-Path $hostsPath) {
    $updatedHosts = Get-Content $hostsPath | Where-Object { $_ -notmatch $ingressDomain }
    $updatedHosts | Set-Content $hostsPath
    Write-Host "Hosts entry removed."
} else {
    Write-Host "Hosts file not found."
}

Write-Host "Cleanup complete."