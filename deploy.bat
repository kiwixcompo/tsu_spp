@echo off
cls
echo ╔═══════════════════════════════════════════════════════════════╗
echo ║                                                               ║
echo ║           TSU STAFF PROFILE - ONE-CLICK DEPLOY                ║
echo ║                                                               ║
echo ╚═══════════════════════════════════════════════════════════════╝
echo.

REM Check if git is installed
git --version >nul 2>&1
if errorlevel 1 (
    echo ❌ ERROR: Git is not installed
    echo.
    echo Please install Git from: https://git-scm.com/
    echo After installation, run this script again.
    echo.
    pause
    exit /b 1
)

echo ✓ Git is installed
echo.

REM Check if this is a git repository
git rev-parse --git-dir >nul 2>&1
if errorlevel 1 (
    echo ⚙️  Initializing Git repository...
    git init
    
    echo ⚙️  Setting up remote repository...
    git remote add origin https://github.com/kiwixcompo/tsu_spp.git
    
    echo ⚙️  Creating .gitignore...
    (
    echo .env
    echo .env.local
    echo error_log
    echo *.log
    echo .vscode/
    echo .idea/
    echo node_modules/
    echo vendor/
    echo storage/qrcodes/*.png
    echo public/uploads/profiles/*
    echo !public/uploads/profiles/.gitkeep
    echo *.bak
    echo *.tmp
    echo Thumbs.db
    echo .DS_Store
    ) > .gitignore
    
    echo ⚙️  Setting main branch...
    git branch -M main
    
    echo.
    echo ✓ Git initialized successfully!
    echo.
)

echo ═══════════════════════════════════════════════════════════════
echo  DEPLOYING TO GITHUB
echo ═══════════════════════════════════════════════════════════════
echo.

echo 📦 Adding all files...
git add .

echo.
echo 💾 Committing changes...
set commit_msg=Update: %date% %time%
git commit -m "%commit_msg%"

echo.
echo 🚀 Pushing to GitHub...
git push -u origin main

if errorlevel 1 (
    echo.
    echo ⚠️  Push to 'main' failed. Trying 'master'...
    git push -u origin master
    
    if errorlevel 1 (
        echo.
        echo ╔═══════════════════════════════════════════════════════════════╗
        echo ║  ❌ DEPLOYMENT FAILED                                         ║
        echo ╚═══════════════════════════════════════════════════════════════╝
        echo.
        echo Possible reasons:
        echo  1. You need to authenticate with GitHub
        echo  2. You don't have push permissions
        echo.
        echo To authenticate:
        echo  • Username: Your GitHub username
        echo  • Password: Use a Personal Access Token
        echo.
        echo Get token from:
        echo  GitHub.com ^> Settings ^> Developer settings ^> 
        echo  Personal access tokens ^> Generate new token
        echo.
        pause
        exit /b 1
    )
)

echo.
echo ╔═══════════════════════════════════════════════════════════════╗
echo ║  ✅ DEPLOYMENT SUCCESSFUL!                                    ║
echo ╚═══════════════════════════════════════════════════════════════╝
echo.
echo 📍 Repository: https://github.com/kiwixcompo/tsu_spp
echo 📍 Production: https://staff.tsuniversity.edu.ng/public/
echo 📍 Deploy Path: /home4/tsuniity/staff.tsuniversity.edu.ng/
echo.
echo ⏱️  cPanel will auto-deploy in 1-2 minutes
echo.
echo Check deployment status:
echo  cPanel ^> Git Version Control
echo.
echo ═══════════════════════════════════════════════════════════════
echo.
pause
