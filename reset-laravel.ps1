# -------------------------------
# reset-laravel.ps1
# -------------------------------

# Projektpfad
$projectPath = "D:\XAMPPP\htdocs\laravel-tutorial\oliver-rein"

# PHP & Composer Pfade
$phpPath = "D:\XAMPPP\php\php.exe"
$composerPath = "C:\ProgramData\ComposerSetup\bin\composer.phar"

# In Projektverzeichnis wechseln
Set-Location $projectPath

Write-Host "=== Beende hängende PHP/Composer/Node Prozesse ===" -ForegroundColor Yellow
Get-Process php, composer, node, esbuild -ErrorAction SilentlyContinue | Stop-Process -Force

Write-Host "=== Lösche alten Vendor und composer.lock ===" -ForegroundColor Yellow
Remove-Item -Recurse -Force "$projectPath\vendor" -ErrorAction SilentlyContinue
Remove-Item -Force "$projectPath\composer.lock" -ErrorAction SilentlyContinue

Write-Host "=== Composer Install mit maximalem Memory (-vvv) ===" -ForegroundColor Yellow

# Prozessinfo erstellen
$processInfo = New-Object System.Diagnostics.ProcessStartInfo
$processInfo.FileName = $phpPath
$processInfo.Arguments = "-d memory_limit=-1 `"$composerPath`" install -vvv"
$processInfo.RedirectStandardOutput = $true
$processInfo.RedirectStandardError  = $true
$processInfo.UseShellExecute = $false
$processInfo.CreateNoWindow = $false

# Prozess starten
$process = New-Object System.Diagnostics.Process
$process.StartInfo = $processInfo
$process.Start() | Out-Null

# Live-Ausgabe der Composer Logs
while (-not $process.HasExited) {
    if (-not $process.StandardOutput.EndOfStream) {
        $outLine = $process.StandardOutput.ReadLine()
        if ($outLine) { Write-Host $outLine }
    }
    if (-not $process.StandardError.EndOfStream) {
        $errLine = $process.StandardError.ReadLine()
        if ($errLine) { Write-Host $errLine -ForegroundColor Red }
    }
}

# Restliche Zeilen nach Prozessende
while (-not $process.StandardOutput.EndOfStream) {
    Write-Host $process.StandardOutput.ReadLine()
}
while (-not $process.StandardError.EndOfStream) {
    Write-Host $process.StandardError.ReadLine() -ForegroundColor Red
}

$process.WaitForExit()
if ($process.ExitCode -ne 0) {
    Write-Host "=== Composer Install fehlgeschlagen! ExitCode: $($process.ExitCode) ===" -ForegroundColor Red
    exit $process.ExitCode
}

Write-Host "=== Composer Install erfolgreich ? ===" -ForegroundColor Green
