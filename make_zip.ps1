# ============================================================
# cPanel ZIP Builder — Fast version using CreateFromDirectory
# ============================================================
Set-Location $PSScriptRoot
Add-Type -AssemblyName System.IO.Compression.FileSystem

$root    = $PSScriptRoot
$staging = "$root\_staging_deploy"
$zipOut  = "$root\sunglass_cpanel.zip"

Write-Host ""
Write-Host "=== cPanel ZIP Builder ===" -ForegroundColor Cyan

# Cleanup
if (Test-Path $staging) { Remove-Item $staging -Recurse -Force }
if (Test-Path $zipOut)  { Remove-Item $zipOut  -Force }
if (Test-Path "$root\public\hot") {
    Remove-Item "$root\public\hot" -Force
    Write-Host "[OK] Removed public/hot" -ForegroundColor Green
}

# Folders to exclude entirely (fast dir-level skip)
$excludeDirs = @(
    "_staging_deploy", ".git", "node_modules",
    "tests", ".tools", "dist", "storage/framework/cache",
    "storage/framework/sessions", "storage/framework/views",
    "bootstrap/cache/*.php"
)

# Individual files to exclude
$excludeFiles = @(
    "sunglass_deploy.zip", "sunglass_cpanel.zip", "make_zip.ps1",
    ".env.bak", ".phpunit.result.cache", "phpunit.xml",
    "vite.config.js", "package.json", "package-lock.json",
    ".npmrc", "README.md", ".gitignore", ".gitattributes", ".editorconfig"
)

Write-Host "Copying files to staging..." -ForegroundColor Yellow
New-Item -ItemType Directory -Path $staging | Out-Null

# Get all top-level items and recursively copy, skipping excluded dirs
function Copy-Filtered {
    param($Source, $Dest)
    Get-ChildItem -Path $Source | ForEach-Object {
        $item = $_
        $relName = $item.Name

        # Skip excluded dirs
        if ($item.PSIsContainer -and ($excludeDirs -contains $relName)) { return }

        # Skip excluded files
        if (-not $item.PSIsContainer -and ($excludeFiles -contains $relName)) { return }

        $destPath = Join-Path $Dest $relName

        if ($item.PSIsContainer) {
            New-Item -ItemType Directory -Path $destPath -Force | Out-Null
            Copy-Filtered -Source $item.FullName -Dest $destPath
        } else {
            Copy-Item -Path $item.FullName -Destination $destPath -Force
        }
    }
}

Copy-Filtered -Source $root -Dest $staging

# Ensure storage directories exist but are empty (except for .gitignore)
$storageDirs = @("storage/framework/cache", "storage/framework/sessions", "storage/framework/views", "storage/logs")
foreach ($dir in $storageDirs) {
    $path = Join-Path $staging $dir
    if (-not (Test-Path $path)) {
        New-Item -ItemType Directory -Path $path -Force | Out-Null
    }
}



# Ensure bootstrap/cache is empty (except for .gitignore)
$cachePath = Join-Path $staging "bootstrap/cache"
if (Test-Path $cachePath) {
    Get-ChildItem $cachePath -Exclude ".gitignore", "packages.php", "services.php" | Remove-Item -Force
}
# Actually, for zero-config, it's safer to remove packages.php and services.php too so Laravel regenerates them on the fly
Get-ChildItem $cachePath -Exclude ".gitignore" | Remove-Item -Force

$fileCount = (Get-ChildItem $staging -Recurse -File).Count
Write-Host "[OK] Staged $fileCount files" -ForegroundColor Green

# Make it 100% plug-and-play for cPanel without SSH
Write-Host "Configuring .env for zero-config cPanel deploy..." -ForegroundColor Yellow
if (Test-Path "$staging\.env.production") {
    # Copy production env to .env
    Copy-Item "$staging\.env.production" "$staging\.env" -Force
    
    # Remove hardcoded DB_DATABASE so Laravel uses dynamic database_path('database.sqlite')
    $envContent = Get-Content "$staging\.env"
    $envContent = $envContent | Where-Object { $_ -notmatch '^DB_DATABASE=' }
    $envContent | Set-Content "$staging\.env"
    
    Write-Host "[OK] .env configured automatically" -ForegroundColor Green
}

# ZIP using CreateFromDirectory — single fast call
Write-Host "Compressing..." -ForegroundColor Yellow
[System.IO.Compression.ZipFile]::CreateFromDirectory(
    $staging,
    $zipOut,
    [System.IO.Compression.CompressionLevel]::Fastest,
    $false
)

# Cleanup staging
Remove-Item $staging -Recurse -Force
Write-Host "[OK] Staging cleaned up" -ForegroundColor Green

# Report
$sizeMB = [math]::Round((Get-Item $zipOut).Length / 1MB, 2)
Write-Host ""
Write-Host "=========================================" -ForegroundColor Green
Write-Host " ZIP Ready  : sunglass_cpanel.zip"        -ForegroundColor Green
Write-Host " Size       : $sizeMB MB"
Write-Host " Files      : $fileCount"
Write-Host "=========================================" -ForegroundColor Green
Write-Host ""
Write-Host "UPLOAD STEPS:" -ForegroundColor Yellow
Write-Host "  1. Upload sunglass_cpanel.zip to cPanel File Manager -> public_html"
Write-Host "  2. Extract it there (right-click -> Extract)"
Write-Host "  3. SSH and run:  bash cpanel_setup.sh"
Write-Host "  4. Edit .env -> set APP_URL=https://yourdomain.com"
Write-Host ""
