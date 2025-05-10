# --------------------------------------------------------
# HOW TO RUN THIS SCRIPT:
# --------------------------------------------------------
# 1. Open PowerShell (Admin not required unless Docker needs it)
# 2. Set execution policy temporarily:
#    Set-ExecutionPolicy Bypass -Scope Process -Force
# 3. Run:
#    .\build-and-push.ps1
# --------------------------------------------------------

# Variables
$imageName = "ghaithbenali/eventify-v2:latest"
$dockerfilePath = "docker\Dockerfile"
$deploymentName = "eventify-app"
$namespace = "pfe"

Write-Host "🔨 Building Docker image without cache..."
docker build --no-cache -f $dockerfilePath -t $imageName .

if ($LASTEXITCODE -ne 0) {
    Write-Host "❌ Docker build failed. Exiting..."
    exit 1
}

Write-Host "📤 Pushing image to DockerHub..."
docker push $imageName

if ($LASTEXITCODE -ne 0) {
    Write-Host "❌ Docker push failed. Exiting..."
    exit 1
}

Write-Host "`n✅ Docker image pushed successfully: $imageName"
