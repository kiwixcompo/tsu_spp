<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\Profile;
use App\Helpers\FileUploadHelper;

class ProfileController extends Controller
{
    private $profileModel;

    public function __construct()
    {
        parent::__construct();
        try {
            $this->profileModel = new Profile();
        } catch (\Exception $e) {
            $this->profileModel = null;
        }
    }

    public function showSetup(): void
    {
        $this->requireAuth();
        
        // Check if user already has a profile
        $user = $this->getCurrentUser();
        $existingProfile = null;
        
        if ($this->profileModel) {
            $existingProfile = $this->profileModel->findByUserId($user['id']);
        }

        // If profile exists, redirect to edit
        if ($existingProfile) {
            $this->redirect('profile/edit');
            return;
        }

        // Get registration data from session
        $registrationData = $_SESSION['registration_data'] ?? [];

        $this->view('profile/setup', [
            'csrf_token' => $this->generateCSRFToken(),
            'profile' => $existingProfile ?: [],
            'registration_data' => $registrationData,
        ]);
    }

    /**
     * Get faculties with their departments
     */
    private function getFacultiesWithDepartments(): array
    {
        if (!$this->db) {
            return [];
        }

        try {
            $stmt = $this->db->query("SELECT faculty, department FROM departments ORDER BY faculty, department");
            $data = $stmt->fetchAll();
            
            $faculties = [];
            foreach ($data as $row) {
                $facultyName = $row['faculty'];
                if (!isset($faculties[$facultyName])) {
                    $faculties[$facultyName] = [
                        'name' => $facultyName,
                        'departments' => []
                    ];
                }
                $faculties[$facultyName]['departments'][] = $row['department'];
            }
            
            return array_values($faculties);
        } catch (\Exception $e) {
            return [];
        }
    }

    /**
     * API endpoint for faculties
     */
    public function getFaculties(): void
    {
        $faculties = $this->getFacultiesWithDepartments();
        $this->json($faculties);
    }

    public function setup(): void
    {
        // Log the start of setup
        error_log("=== Profile Setup Started ===");
        error_log("User ID: " . ($_SESSION['user_id'] ?? 'NOT SET'));
        error_log("Request Method: " . ($_SERVER['REQUEST_METHOD'] ?? 'UNKNOWN'));
        
        // Disable error display for clean JSON response
        ini_set('display_errors', '0');
        error_reporting(E_ALL);
        
        // Ensure clean output buffer for JSON response
        while (ob_get_level()) {
            ob_end_clean();
        }
        ob_start();
        
        $this->requireAuth();
        
        error_log("Auth check passed");

        if (!$this->verifyCSRFToken()) {
            error_log("CSRF token verification failed");
            ob_end_clean();
            header('Content-Type: application/json');
            http_response_code(403);
            echo json_encode(['error' => 'Invalid CSRF token']);
            exit;
        }
        
        error_log("CSRF check passed");

        $errors = $this->validate([
            'title' => 'required',
            'first_name' => 'required|min:2|max:100',
            'last_name' => 'required|min:2|max:100',
            'designation' => 'required',
            'staff_prefix' => 'required',
            'staff_number' => 'required',
        ]);

        if (!empty($errors)) {
            ob_end_clean();
            header('Content-Type: application/json');
            http_response_code(422);
            echo json_encode(['errors' => $errors]);
            exit;
        }

        try {
            error_log("Getting current user...");
            $user = $this->getCurrentUser();
            
            if (!$user) {
                error_log("User not found");
                ob_end_clean();
                header('Content-Type: application/json');
                http_response_code(404);
                echo json_encode(['error' => 'User not found']);
                exit;
            }
            
            error_log("User found: ID=" . $user['id'] . ", Email=" . $user['email']);

            // Check if profileModel is available
            if (!$this->profileModel) {
                ob_end_clean();
                header('Content-Type: application/json');
                http_response_code(500);
                echo json_encode(['error' => 'Profile service unavailable']);
                exit;
            }

            // Check if profile already exists
            $existingProfile = $this->profileModel->findByUserId($user['id']);
            if ($existingProfile) {
                ob_end_clean();
                header('Content-Type: application/json');
                http_response_code(200);
                echo json_encode([
                    'success' => true,
                    'message' => 'Profile already exists',
                    'redirect' => 'dashboard'
                ]);
                exit;
            }

            $registrationData = $_SESSION['registration_data'] ?? [];
            $staffPrefix = $this->sanitizeInput($this->input('staff_prefix'));
            $staffNumberRaw = $this->sanitizeInput($this->input('staff_number'));
            $combinedStaffNumber = '';
            if (!empty($staffPrefix) && in_array($staffPrefix, ['TSU/SP/', 'TSU/JP/'], true) && preg_match('/^[0-9]+$/', $staffNumberRaw)) {
                $combinedStaffNumber = $staffPrefix . $staffNumberRaw;
            } elseif (!empty($registrationData['staff_number'])) {
                $combinedStaffNumber = $registrationData['staff_number'];
            }

            // Handle profile photo upload
            $profilePhotoPath = null;
            error_log("Checking for profile photo upload...");
            error_log("FILES array: " . print_r($_FILES, true));
            
            if (isset($_FILES['profile_photo']) && $_FILES['profile_photo']['error'] === UPLOAD_ERR_OK) {
                try {
                    error_log("Uploading profile photo for user " . $user['id']);
                    $profilePhotoPath = FileUploadHelper::uploadProfilePhoto($_FILES['profile_photo'], $user['id']);
                    error_log("Photo uploaded successfully: " . $profilePhotoPath);
                } catch (\Exception $e) {
                    // Photo upload is optional, log but continue
                    error_log('Photo upload failed: ' . $e->getMessage());
                }
            } else {
                error_log("No photo uploaded or upload error: " . ($_FILES['profile_photo']['error'] ?? 'not set'));
            }

            // Generate profile slug
            $firstName = $this->sanitizeInput($this->input('first_name'));
            $lastName = $this->sanitizeInput($this->input('last_name'));
            $baseSlug = $this->generateSlug($firstName . '-' . $lastName);
            $profileSlug = $this->profileModel->generateUniqueSlug($baseSlug);

            // Create profile
            $profileData = [
                'user_id' => $user['id'],
                'staff_number' => $combinedStaffNumber,
                'title' => $this->sanitizeInput($this->input('title')),
                'first_name' => $firstName,
                'middle_name' => $this->sanitizeInput($this->input('middle_name')),
                'last_name' => $lastName,
                'faculty' => $registrationData['faculty'] ?? '',
                'department' => $registrationData['department'] ?? '',
                'designation' => $this->sanitizeInput($this->input('designation')),
                'office_location' => $this->sanitizeInput($this->input('office_location')),
                'office_phone' => $this->sanitizeInput($this->input('office_phone')),
                'professional_summary' => $this->sanitizeInput($this->input('professional_summary')),
                'research_interests' => $this->sanitizeInput($this->input('research_interests')),
                'expertise_keywords' => $this->sanitizeInput($this->input('expertise_keywords')),
                'profile_visibility' => $this->sanitizeInput($this->input('profile_visibility')) ?: 'public',
                'allow_contact' => $this->input('allow_contact') ? 1 : 0,
                'profile_slug' => $profileSlug,
            ];

            // Add profile photo if uploaded
            if ($profilePhotoPath) {
                error_log("Adding profile photo to data: " . $profilePhotoPath);
                $profileData['profile_photo'] = $profilePhotoPath;
            } else {
                error_log("No profile photo to add");
            }

            error_log("Creating profile with data: " . print_r($profileData, true));
            $profileId = $this->profileModel->create($profileData);

            if ($profileId) {
                // Generate QR code for profile
                try {
                    require_once __DIR__ . '/../Helpers/QRCodeHelper.php';
                    $qrCodePath = \App\Helpers\QRCodeHelper::generateProfileQRCode($user['id'], $profileSlug);
                    if ($qrCodePath) {
                        $this->db->update('profiles', 
                            ['qr_code_path' => $qrCodePath], 
                            'user_id = ?', 
                            [$user['id']]
                        );
                        error_log("✓ QR code generated for user " . $user['id']);
                    }
                } catch (\Exception $e) {
                    error_log('QR code generation failed: ' . $e->getMessage());
                }
                
                // Clear registration data from session
                unset($_SESSION['registration_data']);

                // Activate account now that profile is complete
                if ($this->db) {
                    try {
                        $this->db->update('users', 
                            ['account_status' => 'active'], 
                            'id = ?', 
                            [$user['id']]
                        );
                    } catch (\Exception $e) {
                        error_log('Account activation failed: ' . $e->getMessage());
                    }
                }

                // Update profile completion
                if ($this->db) {
                    try {
                        $stats = $this->profileModel->getProfileStats($user['id']);
                        $this->db->update('users', 
                            ['profile_completion' => $stats['completion']], 
                            'id = ?', 
                            [$user['id']]
                        );
                    } catch (\Exception $e) {
                        // Log but don't fail
                        error_log('Profile completion update failed: ' . $e->getMessage());
                    }
                }

                // Log activity
                try {
                    $this->logActivity('profile_created', ['profile_id' => $profileId]);
                    $this->logActivity('account_activated', ['user_id' => $user['id']]);
                } catch (\Exception $e) {
                    // Log but don't fail
                    error_log('Activity logging failed: ' . $e->getMessage());
                }

                ob_end_clean();
                header('Content-Type: application/json');
                http_response_code(200);
                echo json_encode([
                    'success' => true,
                    'message' => 'Profile created successfully! Your account is now active.',
                    'redirect' => 'dashboard',
                ]);
                exit;
            } else {
                ob_end_clean();
                header('Content-Type: application/json');
                http_response_code(500);
                echo json_encode(['error' => 'Failed to create profile. Please try again.']);
                exit;
            }

        } catch (\Exception $e) {
            $errorMessage = $e->getMessage();
            $errorFile = $e->getFile();
            $errorLine = $e->getLine();
            $errorTrace = $e->getTraceAsString();
            
            error_log("=== Profile Setup Error ===");
            error_log("Message: $errorMessage");
            error_log("File: $errorFile");
            error_log("Line: $errorLine");
            error_log("Stack trace: $errorTrace");
            error_log("=========================");
            
            ob_end_clean();
            header('Content-Type: application/json');
            http_response_code(500);
            echo json_encode([
                'error' => 'Profile creation failed: ' . $errorMessage,
                'debug' => [
                    'file' => basename($errorFile),
                    'line' => $errorLine
                ]
            ]);
            exit;
        }
    }

    public function showEdit(): void
    {
        $this->requireAuth();
        
        $user = $this->getCurrentUser();
        $profile = null;
        
        if ($this->profileModel) {
            $profile = $this->profileModel->findByUserId($user['id']);
            if ($profile) {
                $textFields = [
                    'title', 'first_name', 'middle_name', 'last_name', 'faculty', 'department',
                    'designation', 'office_location', 'office_phone', 'professional_summary',
                    'research_interests', 'expertise_keywords'
                ];
                foreach ($textFields as $field) {
                    if (isset($profile[$field])) {
                        $profile[$field] = html_entity_decode($profile[$field], ENT_QUOTES | ENT_HTML5, 'UTF-8');
                    }
                }
            }
        }

        if (!$profile) {
            $this->redirect('profile/setup');
            return;
        }

        // Get faculties and departments
        $faculties = $this->getFacultiesWithDepartments();

        $this->view('profile/edit', [
            'csrf_token' => $this->generateCSRFToken(),
            'profile' => $profile,
            'faculties' => $faculties,
        ]);
    }

    public function update(): void
    {
        $this->requireAuth();

        if (!$this->verifyCSRFToken()) {
            $this->json(['error' => 'Invalid CSRF token'], 403);
            return;
        }

        $user = $this->getCurrentUser();
        
        if (!$this->profileModel) {
            $this->json(['error' => 'Profile service unavailable'], 500);
            return;
        }

        $profile = $this->profileModel->findByUserId($user['id']);
        if (!$profile) {
            $this->json(['error' => 'Profile not found'], 404);
            return;
        }

        $errors = $this->validate([
            'title' => 'required',
            'designation' => 'required|max:255',
            'department' => 'required|max:255',
            'faculty' => 'required|max:255',
            'office_location' => 'max:100',
            'office_phone' => 'max:20',
            'professional_summary' => 'max:1000',
            'research_interests' => 'max:500',
            'expertise_keywords' => 'max:500',
        ]);

        if (!empty($errors)) {
            $this->json(['errors' => $errors], 422);
            return;
        }

        try {
            // Combine staff prefix and number
            $staffPrefix = $this->sanitizeInput($this->input('staff_prefix'));
            $staffNumber = $this->sanitizeInput($this->input('staff_number'));
            $fullStaffNumber = $staffPrefix . $staffNumber;

            $updateData = [
                'first_name' => $this->sanitizeInput($this->input('first_name')),
                'middle_name' => $this->sanitizeInput($this->input('middle_name')),
                'last_name' => $this->sanitizeInput($this->input('last_name')),
                'staff_number' => $fullStaffNumber,
                'title' => $this->sanitizeInput($this->input('title')),
                'designation' => $this->sanitizeInput($this->input('designation')),
                'department' => $this->sanitizeInput($this->input('department')),
                'faculty' => $this->sanitizeInput($this->input('faculty')),
                'office_location' => $this->sanitizeInput($this->input('office_location')),
                'office_phone' => $this->sanitizeInput($this->input('office_phone')),
                'professional_summary' => $this->sanitizeInput($this->input('professional_summary')),
                'research_interests' => $this->sanitizeInput($this->input('research_interests')),
                'expertise_keywords' => $this->sanitizeInput($this->input('expertise_keywords')),
                'updated_at' => date('Y-m-d H:i:s'),
            ];

            // Handle profile photo upload
            if (isset($_FILES['profile_photo']) && $_FILES['profile_photo']['error'] === UPLOAD_ERR_OK) {
                $photoResult = $this->handleFileUpload($_FILES['profile_photo'], 'photo');
                if ($photoResult['success']) {
                    $updateData['profile_photo'] = $photoResult['filename'];
                    // Delete old photo if exists
                    if (!empty($profile['profile_photo'])) {
                        $oldPhotoPath = __DIR__ . '/../../storage/uploads/' . $profile['profile_photo'];
                        if (file_exists($oldPhotoPath)) {
                            unlink($oldPhotoPath);
                        }
                    }
                } else {
                    $this->json(['error' => $photoResult['error']], 422);
                    return;
                }
            }

            // Handle CV upload
            if (isset($_FILES['cv_file']) && $_FILES['cv_file']['error'] === UPLOAD_ERR_OK) {
                $cvResult = $this->handleFileUpload($_FILES['cv_file'], 'document');
                if ($cvResult['success']) {
                    $updateData['cv_file'] = $cvResult['filename'];
                    // Delete old CV if exists
                    if (!empty($profile['cv_file'])) {
                        $oldCvPath = __DIR__ . '/../../storage/uploads/' . $profile['cv_file'];
                        if (file_exists($oldCvPath)) {
                            unlink($oldCvPath);
                        }
                    }
                } else {
                    $this->json(['error' => $cvResult['error']], 422);
                    return;
                }
            }

            $success = $this->profileModel->update($user['id'], $updateData);

            if ($success) {
                // Update profile completion
                if ($this->db) {
                    $stats = $this->profileModel->getProfileStats($user['id']);
                    $this->db->update('users', 
                        ['profile_completion' => $stats['completion']], 
                        'id = ?', 
                        [$user['id']]
                    );
                }

                // Log activity
                $this->logActivity('profile_updated', ['profile_id' => $profile['id']]);

                $this->json([
                    'success' => true,
                    'message' => 'Profile updated successfully!',
                ]);
            } else {
                $this->json(['error' => 'Failed to update profile'], 500);
            }

        } catch (\Exception $e) {
            $this->json(['error' => 'Profile update failed: ' . $e->getMessage()], 500);
        }
    }

    // Education Management
    public function showEducation(): void
    {
        $this->requireAuth();
        
        $user = $this->getCurrentUser();
        $education = [];
        
        if ($this->db) {
            try {
                $education = $this->db->fetchAll(
                    "SELECT * FROM education WHERE user_id = ? ORDER BY end_year DESC, start_year DESC",
                    [$user['id']]
                );
            } catch (\Exception $e) {
                $education = [];
            }
        }

        $this->view('profile/education', [
            'csrf_token' => $this->generateCSRFToken(),
            'education' => $education,
        ]);
    }

    public function addEducation(): void
    {
        $this->requireAuth();

        // Debug logging
        error_log("=== Education Add Request ===");
        error_log("Request method: " . $_SERVER['REQUEST_METHOD']);
        error_log("POST data: " . json_encode($_POST));
        error_log("Content-Type: " . ($_SERVER['CONTENT_TYPE'] ?? 'not set'));

        if (!$this->verifyCSRFToken()) {
            error_log("CSRF token verification failed");
            $this->json(['error' => 'Invalid CSRF token'], 403);
            return;
        }

        error_log("CSRF token verified successfully");

        $errors = $this->validate([
            'degree_type' => 'required|max:100',
            'institution' => 'required|max:200',
            'field_of_study' => 'required|max:100',
            'start_year' => 'required|integer|min:1950|max:' . (date('Y') + 10),
            'end_year' => 'integer|min:1950|max:' . (date('Y') + 10),
        ]);

        if (!empty($errors)) {
            error_log("Validation errors: " . json_encode($errors));
            $this->json(['errors' => $errors], 422);
            return;
        }

        $user = $this->getCurrentUser();
        error_log("Current user: " . ($user ? $user['id'] : 'null'));

        try {
            $educationData = [
                'user_id' => $user['id'],
                'degree_type' => $this->sanitizeInput($this->input('degree_type')),
                'institution' => $this->sanitizeInput($this->input('institution')),
                'field_of_study' => $this->sanitizeInput($this->input('field_of_study')),
                'start_year' => (int)$this->input('start_year'),
                'end_year' => $this->input('end_year') ? (int)$this->input('end_year') : null,
                'is_current' => $this->input('is_current') ? 1 : 0,
                'display_years' => $this->input('display_years') ? 1 : 0,
                'description' => $this->sanitizeInput($this->input('description')),
                'created_at' => date('Y-m-d H:i:s'),
            ];

            error_log("Education data prepared: " . json_encode($educationData));

            if ($this->db) {
                $educationId = $this->db->insert('education', $educationData);
                error_log("Insert result: " . ($educationId ? "success, ID=$educationId" : "failed"));
                
                if ($educationId) {
                    // Log activity
                    $this->logActivity('education_added', ['education_id' => $educationId]);

                    $this->json([
                        'success' => true,
                        'message' => 'Education added successfully!',
                    ]);
                } else {
                    error_log("Education insert returned falsy value");
                    $this->json(['error' => 'Failed to add education'], 500);
                }
            } else {
                error_log("Database not available");
                $this->json(['error' => 'Database unavailable'], 500);
            }

        } catch (\Exception $e) {
            error_log("Exception caught: " . $e->getMessage());
            error_log("Exception trace: " . $e->getTraceAsString());
            $this->json(['error' => 'Failed to add education: ' . $e->getMessage()], 500);
        }
    }

    // Experience Management
    public function showExperience(): void
    {
        $this->requireAuth();
        
        $user = $this->getCurrentUser();
        $experience = [];
        
        if ($this->db) {
            try {
                $experience = $this->db->fetchAll(
                    "SELECT * FROM experience WHERE user_id = ? ORDER BY end_date DESC, start_date DESC",
                    [$user['id']]
                );
            } catch (\Exception $e) {
                $experience = [];
            }
        }

        $this->view('profile/experience', [
            'csrf_token' => $this->generateCSRFToken(),
            'experience' => $experience,
        ]);
    }

    public function addExperience(): void
    {
        $this->requireAuth();

        if (!$this->verifyCSRFToken()) {
            $this->json(['error' => 'Invalid CSRF token'], 403);
            return;
        }

        $errors = $this->validate([
            'job_title' => 'required|max:100',
            'organization' => 'required|max:200',
            'start_date' => 'required|date',
            'end_date' => 'date',
        ]);

        if (!empty($errors)) {
            $this->json(['errors' => $errors], 422);
            return;
        }

        $user = $this->getCurrentUser();

        try {
            $experienceData = [
                'user_id' => $user['id'],
                'job_title' => $this->sanitizeInput($this->input('job_title')),
                'organization' => $this->sanitizeInput($this->input('organization')),
                'location' => $this->sanitizeInput($this->input('location')),
                'start_date' => $this->input('start_date'),
                'end_date' => $this->input('end_date') ?: null,
                'description' => $this->sanitizeInput($this->input('description')),
                'is_current' => $this->input('is_current') ? 1 : 0,
                'created_at' => date('Y-m-d H:i:s'),
            ];

            if ($this->db) {
                $experienceId = $this->db->insert('experience', $experienceData);
                
                if ($experienceId) {
                    // Log activity
                    $this->logActivity('experience_added', ['experience_id' => $experienceId]);

                    $this->json([
                        'success' => true,
                        'message' => 'Experience added successfully!',
                    ]);
                } else {
                    $this->json(['error' => 'Failed to add experience'], 500);
                }
            } else {
                $this->json(['error' => 'Database unavailable'], 500);
            }

        } catch (\Exception $e) {
            $this->json(['error' => 'Failed to add experience: ' . $e->getMessage()], 500);
        }
    }

    // Skills Management
    public function showSkills(): void
    {
        $this->requireAuth();
        
        $user = $this->getCurrentUser();
        $skills = [];
        
        if ($this->db) {
            try {
                $skills = $this->db->fetchAll(
                    "SELECT * FROM skills WHERE user_id = ? ORDER BY proficiency_level DESC, skill_name ASC",
                    [$user['id']]
                );
            } catch (\Exception $e) {
                $skills = [];
            }
        }

        $this->view('profile/skills', [
            'csrf_token' => $this->generateCSRFToken(),
            'skills' => $skills,
        ]);
    }

    public function addSkill(): void
    {
        $this->requireAuth();

        if (!$this->verifyCSRFToken()) {
            $this->json(['error' => 'Invalid CSRF token'], 403);
            return;
        }

        $errors = $this->validate([
            'skill_name' => 'required|max:100',
            'proficiency_level' => 'required',
        ]);

        if (!empty($errors)) {
            $this->json(['errors' => $errors], 422);
            return;
        }

        $user = $this->getCurrentUser();

        try {
            $skillData = [
                'user_id' => $user['id'],
                'skill_name' => $this->sanitizeInput($this->input('skill_name')),
                'proficiency_level' => $this->sanitizeInput($this->input('proficiency_level')),
                'skill_category' => $this->sanitizeInput($this->input('skill_category')) ?: 'other',
                'years_experience' => $this->input('years_experience') ? (int)$this->input('years_experience') : null,
                'created_at' => date('Y-m-d H:i:s'),
            ];

            if ($this->db) {
                $skillId = $this->db->insert('skills', $skillData);
                
                if ($skillId) {
                    // Log activity
                    $this->logActivity('skill_added', ['skill_id' => $skillId]);

                    $this->json([
                        'success' => true,
                        'message' => 'Skill added successfully!',
                    ]);
                } else {
                    $this->json(['error' => 'Failed to add skill'], 500);
                }
            } else {
                $this->json(['error' => 'Database unavailable'], 500);
            }

        } catch (\Exception $e) {
            $this->json(['error' => 'Failed to add skill: ' . $e->getMessage()], 500);
        }
    }

    // Publications Management
    public function showPublications(): void
    {
        $this->requireAuth();
        
        $user = $this->getCurrentUser();
        $publications = [];
        
        if ($this->db) {
            try {
                $publications = $this->db->fetchAll(
                    "SELECT * FROM publications WHERE user_id = ? ORDER BY year DESC, created_at DESC",
                    [$user['id']]
                );
            } catch (\Exception $e) {
                $publications = [];
            }
        }

        $this->view('profile/publications', [
            'csrf_token' => $this->generateCSRFToken(),
            'publications' => $publications,
            'user' => $user,
        ]);
    }

    public function addPublication(): void
    {
        $this->requireAuth();

        if (!$this->verifyCSRFToken()) {
            $this->json(['error' => 'Invalid CSRF token'], 403);
            return;
        }

        $errors = $this->validate([
            'title' => 'required|max:500',
            'publication_type' => 'required',
            'authors' => 'max:1000',
            'journal_conference_name' => 'max:500',
            'publisher' => 'max:191',
            'year' => 'integer|min:1900|max:' . (date('Y') + 5),
            'volume' => 'max:50',
            'issue' => 'max:50',
            'pages' => 'max:50',
            'doi' => 'max:191',
            'url' => 'url|max:500',
            'abstract' => 'max:5000',
            'citation_count' => 'integer|min:0',
        ]);

        if (!empty($errors)) {
            $this->json(['errors' => $errors], 422);
            return;
        }

        $user = $this->getCurrentUser();

        try {
            $publicationData = [
                'user_id' => $user['id'],
                'title' => $this->sanitizeInput($this->input('title')),
                'publication_type' => $this->sanitizeInput($this->input('publication_type')),
                'authors' => $this->sanitizeInput($this->input('authors')),
                'journal_conference_name' => $this->sanitizeInput($this->input('journal_conference_name')),
                'publisher' => $this->sanitizeInput($this->input('publisher')),
                'year' => $this->input('year') ? (int)$this->input('year') : null,
                'volume' => $this->sanitizeInput($this->input('volume')),
                'issue' => $this->sanitizeInput($this->input('issue')),
                'pages' => $this->sanitizeInput($this->input('pages')),
                'doi' => $this->sanitizeInput($this->input('doi')),
                'url' => $this->sanitizeInput($this->input('url')),
                'abstract' => $this->sanitizeInput($this->input('abstract')),
                'citation_count' => $this->input('citation_count') ? (int)$this->input('citation_count') : 0,
                'created_at' => date('Y-m-d H:i:s'),
            ];

            if ($this->db) {
                $publicationId = $this->db->insert('publications', $publicationData);
                
                if ($publicationId) {
                    // Log activity
                    $this->logActivity('publication_added', ['publication_id' => $publicationId]);

                    $this->json([
                        'success' => true,
                        'message' => 'Publication added successfully!',
                    ]);
                } else {
                    $this->json(['error' => 'Failed to add publication'], 500);
                }
            } else {
                $this->json(['error' => 'Database unavailable'], 500);
            }

        } catch (\Exception $e) {
            $this->json(['error' => 'Failed to add publication: ' . $e->getMessage()], 500);
        }
    }

    public function updatePublication(): void
    {
        $this->requireAuth();

        if (!$this->verifyCSRFToken()) {
            $this->json(['error' => 'Invalid CSRF token'], 403);
            return;
        }

        $user = $this->getCurrentUser();
        $publicationId = $this->getRouteParam('id');

        if (!$publicationId) {
            $this->json(['error' => 'Publication ID required'], 400);
            return;
        }

        $errors = $this->validate([
            'title' => 'required|max:500',
            'publication_type' => 'required',
            'authors' => 'max:1000',
            'journal_conference_name' => 'max:500',
            'publisher' => 'max:191',
            'year' => 'integer|min:1900|max:' . (date('Y') + 5),
            'volume' => 'max:50',
            'issue' => 'max:50',
            'pages' => 'max:50',
            'doi' => 'max:191',
            'url' => 'url|max:500',
            'abstract' => 'max:5000',
            'citation_count' => 'integer|min:0',
        ]);

        if (!empty($errors)) {
            $this->json(['errors' => $errors], 422);
            return;
        }

        try {
            if ($this->db) {
                // Verify ownership
                $publication = $this->db->fetchOne(
                    "SELECT id FROM publications WHERE id = ? AND user_id = ?",
                    [$publicationId, $user['id']]
                );

                if (!$publication) {
                    $this->json(['error' => 'Publication not found'], 404);
                    return;
                }

                $updateData = [
                    'title' => $this->sanitizeInput($this->input('title')),
                    'publication_type' => $this->sanitizeInput($this->input('publication_type')),
                    'authors' => $this->sanitizeInput($this->input('authors')),
                    'journal_conference_name' => $this->sanitizeInput($this->input('journal_conference_name')),
                    'publisher' => $this->sanitizeInput($this->input('publisher')),
                    'year' => $this->input('year') ? (int)$this->input('year') : null,
                    'volume' => $this->sanitizeInput($this->input('volume')),
                    'issue' => $this->sanitizeInput($this->input('issue')),
                    'pages' => $this->sanitizeInput($this->input('pages')),
                    'doi' => $this->sanitizeInput($this->input('doi')),
                    'url' => $this->sanitizeInput($this->input('url')),
                    'abstract' => $this->sanitizeInput($this->input('abstract')),
                    'citation_count' => $this->input('citation_count') ? (int)$this->input('citation_count') : 0,
                ];

                $success = $this->db->update('publications', $updateData, 'id = ? AND user_id = ?', [$publicationId, $user['id']]);
                
                if ($success) {
                    // Log activity
                    $this->logActivity('publication_updated', ['publication_id' => $publicationId]);

                    $this->json([
                        'success' => true,
                        'message' => 'Publication updated successfully!',
                    ]);
                } else {
                    $this->json(['error' => 'Failed to update publication'], 500);
                }
            } else {
                $this->json(['error' => 'Database unavailable'], 500);
            }

        } catch (\Exception $e) {
            $this->json(['error' => 'Failed to update publication: ' . $e->getMessage()], 500);
        }
    }

    public function deletePublication(): void
    {
        $this->requireAuth();

        if (!$this->verifyCSRFToken()) {
            $this->json(['error' => 'Invalid CSRF token'], 403);
            return;
        }

        $user = $this->getCurrentUser();
        $publicationId = $this->getRouteParam('id');

        if (!$publicationId) {
            $this->json(['error' => 'Publication ID required'], 400);
            return;
        }

        try {
            if ($this->db) {
                // Verify ownership
                $publication = $this->db->fetchOne(
                    "SELECT id FROM publications WHERE id = ? AND user_id = ?",
                    [$publicationId, $user['id']]
                );

                if (!$publication) {
                    $this->json(['error' => 'Publication not found'], 404);
                    return;
                }

                $success = $this->db->delete('publications', 'id = ? AND user_id = ?', [$publicationId, $user['id']]);
                
                if ($success) {
                    // Log activity
                    $this->logActivity('publication_deleted', ['publication_id' => $publicationId]);

                    $this->json([
                        'success' => true,
                        'message' => 'Publication deleted successfully!',
                    ]);
                } else {
                    $this->json(['error' => 'Failed to delete publication'], 500);
                }
            } else {
                $this->json(['error' => 'Database unavailable'], 500);
            }

        } catch (\Exception $e) {
            $this->json(['error' => 'Failed to delete publication: ' . $e->getMessage()], 500);
        }
    }

    // Education Edit/Delete Methods
    public function updateEducation(): void
    {
        $this->requireAuth();

        if (!$this->verifyCSRFToken()) {
            $this->json(['error' => 'Invalid CSRF token'], 403);
            return;
        }

        $user = $this->getCurrentUser();
        $educationId = $this->getRouteParam('id');

        if (!$educationId) {
            $this->json(['error' => 'Education ID required'], 400);
            return;
        }

        $errors = $this->validate([
            'degree_type' => 'required|max:100',
            'institution' => 'required|max:200',
            'field_of_study' => 'required|max:100',
            'start_year' => 'required|integer|min:1950|max:' . (date('Y') + 10),
            'end_year' => 'integer|min:1950|max:' . (date('Y') + 10),
        ]);

        if (!empty($errors)) {
            $this->json(['errors' => $errors], 422);
            return;
        }

        try {
            if ($this->db) {
                // Verify ownership
                $education = $this->db->fetchOne(
                    "SELECT id FROM education WHERE id = ? AND user_id = ?",
                    [$educationId, $user['id']]
                );

                if (!$education) {
                    $this->json(['error' => 'Education record not found'], 404);
                    return;
                }

                $updateData = [
                    'degree_type' => $this->sanitizeInput($this->input('degree_type')),
                    'institution' => $this->sanitizeInput($this->input('institution')),
                    'field_of_study' => $this->sanitizeInput($this->input('field_of_study')),
                    'start_year' => (int)$this->input('start_year'),
                    'end_year' => $this->input('end_year') ? (int)$this->input('end_year') : null,
                    'is_current' => $this->input('is_current') ? 1 : 0,
                    'display_years' => $this->input('display_years') ? 1 : 0,
                    'description' => $this->sanitizeInput($this->input('description')),
                ];

                $success = $this->db->update('education', $updateData, 'id = ? AND user_id = ?', [$educationId, $user['id']]);
                
                if ($success) {
                    $this->logActivity('education_updated', ['education_id' => $educationId]);
                    $this->json(['success' => true, 'message' => 'Education updated successfully!']);
                } else {
                    $this->json(['error' => 'Failed to update education'], 500);
                }
            } else {
                $this->json(['error' => 'Database unavailable'], 500);
            }
        } catch (\Exception $e) {
            $this->json(['error' => 'Failed to update education: ' . $e->getMessage()], 500);
        }
    }

    public function deleteEducation(): void
    {
        $this->requireAuth();

        if (!$this->verifyCSRFToken()) {
            $this->json(['error' => 'Invalid CSRF token'], 403);
            return;
        }

        $user = $this->getCurrentUser();
        $educationId = $this->getRouteParam('id');

        if (!$educationId) {
            $this->json(['error' => 'Education ID required'], 400);
            return;
        }

        try {
            if ($this->db) {
                $success = $this->db->delete('education', 'id = ? AND user_id = ?', [$educationId, $user['id']]);
                
                if ($success) {
                    $this->logActivity('education_deleted', ['education_id' => $educationId]);
                    $this->json(['success' => true, 'message' => 'Education deleted successfully!']);
                } else {
                    $this->json(['error' => 'Failed to delete education'], 500);
                }
            } else {
                $this->json(['error' => 'Database unavailable'], 500);
            }
        } catch (\Exception $e) {
            $this->json(['error' => 'Failed to delete education: ' . $e->getMessage()], 500);
        }
    }

    // Experience Edit/Delete Methods
    public function updateExperience(): void
    {
        $this->requireAuth();

        if (!$this->verifyCSRFToken()) {
            $this->json(['error' => 'Invalid CSRF token'], 403);
            return;
        }

        $user = $this->getCurrentUser();
        $experienceId = $this->getRouteParam('id');

        if (!$experienceId) {
            $this->json(['error' => 'Experience ID required'], 400);
            return;
        }

        $errors = $this->validate([
            'job_title' => 'required|max:100',
            'organization' => 'required|max:200',
            'start_date' => 'required|date',
            'end_date' => 'date',
        ]);

        if (!empty($errors)) {
            $this->json(['errors' => $errors], 422);
            return;
        }

        try {
            if ($this->db) {
                // Verify ownership
                $experience = $this->db->fetchOne(
                    "SELECT id FROM experience WHERE id = ? AND user_id = ?",
                    [$experienceId, $user['id']]
                );

                if (!$experience) {
                    $this->json(['error' => 'Experience record not found'], 404);
                    return;
                }

                $updateData = [
                    'job_title' => $this->sanitizeInput($this->input('job_title')),
                    'organization' => $this->sanitizeInput($this->input('organization')),
                    'location' => $this->sanitizeInput($this->input('location')),
                    'start_date' => $this->input('start_date'),
                    'end_date' => $this->input('end_date') ?: null,
                    'description' => $this->sanitizeInput($this->input('description')),
                    'is_current' => $this->input('is_current') ? 1 : 0,
                ];

                $success = $this->db->update('experience', $updateData, 'id = ? AND user_id = ?', [$experienceId, $user['id']]);
                
                if ($success) {
                    $this->logActivity('experience_updated', ['experience_id' => $experienceId]);
                    $this->json(['success' => true, 'message' => 'Experience updated successfully!']);
                } else {
                    $this->json(['error' => 'Failed to update experience'], 500);
                }
            } else {
                $this->json(['error' => 'Database unavailable'], 500);
            }
        } catch (\Exception $e) {
            $this->json(['error' => 'Failed to update experience: ' . $e->getMessage()], 500);
        }
    }

    public function deleteExperience(): void
    {
        $this->requireAuth();

        if (!$this->verifyCSRFToken()) {
            $this->json(['error' => 'Invalid CSRF token'], 403);
            return;
        }

        $user = $this->getCurrentUser();
        $experienceId = $this->getRouteParam('id');

        if (!$experienceId) {
            $this->json(['error' => 'Experience ID required'], 400);
            return;
        }

        try {
            if ($this->db) {
                $success = $this->db->delete('experience', 'id = ? AND user_id = ?', [$experienceId, $user['id']]);
                
                if ($success) {
                    $this->logActivity('experience_deleted', ['experience_id' => $experienceId]);
                    $this->json(['success' => true, 'message' => 'Experience deleted successfully!']);
                } else {
                    $this->json(['error' => 'Failed to delete experience'], 500);
                }
            } else {
                $this->json(['error' => 'Database unavailable'], 500);
            }
        } catch (\Exception $e) {
            $this->json(['error' => 'Failed to delete experience: ' . $e->getMessage()], 500);
        }
    }

    // Skills Edit/Delete Methods
    public function updateSkill(): void
    {
        $this->requireAuth();

        if (!$this->verifyCSRFToken()) {
            $this->json(['error' => 'Invalid CSRF token'], 403);
            return;
        }

        $user = $this->getCurrentUser();
        $skillId = $this->getRouteParam('id');

        if (!$skillId) {
            $this->json(['error' => 'Skill ID required'], 400);
            return;
        }

        $errors = $this->validate([
            'skill_name' => 'required|max:100',
            'proficiency_level' => 'required',
        ]);

        if (!empty($errors)) {
            $this->json(['errors' => $errors], 422);
            return;
        }

        try {
            if ($this->db) {
                // Verify ownership
                $skill = $this->db->fetchOne(
                    "SELECT id FROM skills WHERE id = ? AND user_id = ?",
                    [$skillId, $user['id']]
                );

                if (!$skill) {
                    $this->json(['error' => 'Skill not found'], 404);
                    return;
                }

                $updateData = [
                    'skill_name' => $this->sanitizeInput($this->input('skill_name')),
                    'proficiency_level' => $this->sanitizeInput($this->input('proficiency_level')),
                    'skill_category' => $this->sanitizeInput($this->input('skill_category')) ?: 'other',
                    'years_experience' => $this->input('years_experience') ? (int)$this->input('years_experience') : null,
                ];

                $success = $this->db->update('skills', $updateData, 'id = ? AND user_id = ?', [$skillId, $user['id']]);
                
                if ($success) {
                    $this->logActivity('skill_updated', ['skill_id' => $skillId]);
                    $this->json(['success' => true, 'message' => 'Skill updated successfully!']);
                } else {
                    $this->json(['error' => 'Failed to update skill'], 500);
                }
            } else {
                $this->json(['error' => 'Database unavailable'], 500);
            }
        } catch (\Exception $e) {
            $this->json(['error' => 'Failed to update skill: ' . $e->getMessage()], 500);
        }
    }

    public function deleteSkill(): void
    {
        $this->requireAuth();

        if (!$this->verifyCSRFToken()) {
            $this->json(['error' => 'Invalid CSRF token'], 403);
            return;
        }

        $user = $this->getCurrentUser();
        $skillId = $this->getRouteParam('id');

        if (!$skillId) {
            $this->json(['error' => 'Skill ID required'], 400);
            return;
        }

        try {
            if ($this->db) {
                $success = $this->db->delete('skills', 'id = ? AND user_id = ?', [$skillId, $user['id']]);
                
                if ($success) {
                    $this->logActivity('skill_deleted', ['skill_id' => $skillId]);
                    $this->json(['success' => true, 'message' => 'Skill deleted successfully!']);
                } else {
                    $this->json(['error' => 'Failed to delete skill'], 500);
                }
            } else {
                $this->json(['error' => 'Database unavailable'], 500);
            }
        } catch (\Exception $e) {
            $this->json(['error' => 'Failed to delete skill: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Handle file upload (photo or document)
     */
    private function handleFileUpload(array $file, string $type): array
    {
        // Create uploads directory if it doesn't exist
        $uploadDir = __DIR__ . '/../../storage/uploads/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        // Validate file type and size
        if ($type === 'photo') {
            $allowedTypes = ['image/jpeg', 'image/jpg', 'image/png'];
            $maxSize = 2 * 1024 * 1024; // 2MB
            $allowedExtensions = ['jpg', 'jpeg', 'png'];
        } else { // document
            $allowedTypes = ['application/pdf', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'];
            $maxSize = 5 * 1024 * 1024; // 5MB
            $allowedExtensions = ['pdf', 'doc', 'docx'];
        }

        // Check file size
        if ($file['size'] > $maxSize) {
            return ['success' => false, 'error' => 'File size too large. Maximum allowed: ' . ($maxSize / 1024 / 1024) . 'MB'];
        }

        // Check file type
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mimeType = finfo_file($finfo, $file['tmp_name']);
        finfo_close($finfo);

        if (!in_array($mimeType, $allowedTypes)) {
            return ['success' => false, 'error' => 'Invalid file type. Allowed: ' . implode(', ', $allowedExtensions)];
        }

        // Check file extension
        $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        if (!in_array($extension, $allowedExtensions)) {
            return ['success' => false, 'error' => 'Invalid file extension. Allowed: ' . implode(', ', $allowedExtensions)];
        }

        // Generate unique filename
        $filename = uniqid() . '_' . time() . '.' . $extension;
        $targetPath = $uploadDir . $filename;

        // Move uploaded file
        if (move_uploaded_file($file['tmp_name'], $targetPath)) {
            return ['success' => true, 'filename' => $filename];
        } else {
            return ['success' => false, 'error' => 'Failed to upload file'];
        }
    }
}
