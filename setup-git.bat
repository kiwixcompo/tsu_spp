@echo off
echo ========================================
echo TSU Staff Profile - Git Setup
echo ========================================
echo.

REM Check if git is installed
git --version >nul 2>&1
if errorlevel 1 (
    echo ERROR: Git is not installed or not in PATH
    echo Please install Git from https://git-scm.com/
    pause
    exit /b 1
)

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
echo Step 5: Adding all files...
git add .

echo.
echo Step 6: Creating initial commit...
git commit -m "Initial commit: TSU Staff Profile Portal"

echo.
echo Step 7: Setting main branch...
git branch -M main

echo.
echo ========================================
echo Git Setup Complete!
echo ========================================
echo.
echo Next steps:
echo 1. Make sure you have access to the GitHub repository
echo 2. Run deploy.bat to push your code
echo.
echo If you need to authenticate with GitHub:
echo - Use your GitHub username
echo - Use a Personal Access Token as password
echo   (Get it from: GitHub Settings ^> Developer settings ^> Personal access tokens)
echo.
echo ========================================
pause
