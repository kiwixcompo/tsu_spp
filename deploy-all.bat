@echo off
echo ========================================
echo TSU Staff Profile - Complete Deployment
echo ========================================
echo.

REM Check if git is installed
git --version >nul 2>&1
if errorlevel 1 (
    echo ERROR: Git is not installed or not in PATH
    echo.
    echo Please install Git from: https://git-scm.com/
    echo After installation, run this script again.
    echo.
    pause
    exit /b 1
)

echo Git is installed: 
git --version
echo.

REM Check if this is already a git repository
git rev-parse --git-dir >nul 2>&1
if errorlevel 1 (
    echo ========================================
    echo Git repository not found. Setting up...
    echo ========================================
    echo.
    
    echo Step 1: Initializing Git repository...
    git init
    
    echo.
    echo Step 2: Setting up Git configuration...
    set /p git_name="Enter your name: "
    set /p git_email="Enter your email: "
    
    git config user.name "%git_name%"
    git config user.email "%git_email%"
    
    echo.
    echo Step 3: Adding remote repository...
    git remote add origin https://github.com/kiwixcompo/TSU_Staff_Profile.git
    
    echo.
    echo Step 4: Creating .gitignore file...
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
    
    echo.
    echo Step 5: Setting main branch...
    git branch -M main
    
    echo.
    echo ========================================
    echo Git Setup Complete!
    echo ========================================
    echo.
) else (
    echo ========================================
    echo Git repository found. Proceeding...
    echo ========================================
    echo.
)

echo Current Git status:
git status
echo.

echo ========================================
echo Step 1: Adding all changes to Git
echo ========================================
git add .

echo.
echo ========================================
echo Step 2: Committing changes
echo ========================================
set /p commit_message="Enter commit message (or press Enter for default): "
if "%commit_message%"=="" set commit_message=Update: ID Card system improvements and bug fixes

git commit -m "%commit_message%"

echo.
echo ========================================
echo Step 3: Pushing to GitHub
echo ========================================
echo.
echo Attempting to push to 'main' branch...
git push -u origin main

if errorlevel 1 (
    echo.
    echo Push to 'main' failed. Trying 'master' branch...
    git push -u origin master
    
    if errorlevel 1 (
        echo.
        echo ========================================
        echo ERROR: Failed to push to GitHub
        echo ========================================
        echo.
        echo Possible reasons:
        echo 1. You need to authenticate with GitHub
        echo 2. You don't have push permissions
        echo 3. The repository doesn't exist
        echo.
        echo To authenticate:
        echo - Username: Your GitHub username
        echo - Password: Use a Personal Access Token
        echo.
        echo Get a token from:
        echo GitHub Settings ^> Developer settings ^> Personal access tokens
        echo.
        echo Or try running:
        echo   git push -u origin main
        echo.
        pause
        exit /b 1
    )
)

echo.
echo ========================================
echo SUCCESS! Code pushed to GitHub
echo ========================================
echo.
echo Repository: https://github.com/kiwixcompo/TSU_Staff_Profile
echo.
echo The .cpanel.yml file will automatically deploy to:
echo /home4/tsuniity/staff.tsuniversity.edu.ng/
echo.
echo Deployment will happen automatically when cPanel detects the push.
echo This may take a few minutes.
echo.
echo Check deployment status in cPanel ^> Git Version Control
echo.
echo ========================================
echo.
pause
