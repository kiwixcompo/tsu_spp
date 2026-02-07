#!/bin/bash

echo "========================================"
echo "TSU Staff Profile - GitHub Deployment"
echo "========================================"
echo ""

# Check if git is installed
if ! command -v git &> /dev/null; then
    echo "ERROR: Git is not installed"
    echo "Please install Git first"
    exit 1
fi

echo "Checking Git status..."
git status

echo ""
echo "========================================"
echo "Step 1: Adding all changes to Git"
echo "========================================"
git add .

echo ""
echo "========================================"
echo "Step 2: Committing changes"
echo "========================================"
read -p "Enter commit message (or press Enter for default): " commit_message
if [ -z "$commit_message" ]; then
    commit_message="Update: ID Card system improvements and bug fixes"
fi

git commit -m "$commit_message"

echo ""
echo "========================================"
echo "Step 3: Pushing to GitHub"
echo "========================================"
git push origin main

if [ $? -ne 0 ]; then
    echo ""
    echo "ERROR: Failed to push to GitHub"
    echo ""
    echo "Possible reasons:"
    echo "1. You need to authenticate with GitHub"
    echo "2. The branch name might be 'master' instead of 'main'"
    echo "3. You don't have push permissions"
    echo ""
    echo "Trying 'master' branch..."
    git push origin master
    
    if [ $? -ne 0 ]; then
        echo ""
        echo "Still failed. Please check your GitHub credentials and permissions."
        exit 1
    fi
fi

echo ""
echo "========================================"
echo "SUCCESS! Code pushed to GitHub"
echo "========================================"
echo ""
echo "Repository: https://github.com/kiwixcompo/TSU_Staff_Profile"
echo ""
echo "The .cpanel.yml file will automatically deploy to:"
echo "/home4/tsuniity/staff.tsuniversity.edu.ng/"
echo ""
echo "Deployment will happen automatically when cPanel detects the push."
echo "This may take a few minutes."
echo ""
echo "========================================"
