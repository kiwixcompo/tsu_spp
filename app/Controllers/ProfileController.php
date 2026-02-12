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
     * Returns associative array: ['Faculty Name' => ['Dept 1', 'Dept 2']]
     */
    private function getFacultiesWithDepartments(): array
    {
        if (!$this->db) {
            return [];
        }

        try {
            // Fetch raw data
            $data = $this->db->fetchAll(
                "SELECT faculty, department FROM faculties_departments ORDER BY faculty, department"
            );

            $faculties = [];
            foreach ($data as $row) {
                // Group departments under their faculty name
                $faculties[$row['faculty']][] = $row['department'];
            }

            return $faculties;
        } catch (\Exception $e) {
            error_log("Error fetching faculties: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Get all units/offices
     */
    private function getUnits(): array
    {
        if (!$this->db) {
            return [];
        }
        try {
            $stmt = $this->db->query("SELECT name FROM units_offices ORDER BY name");
            return $stmt->fetchAll(\PDO::FETCH_COLUMN);
        } catch (\Exception $e) {
            error_log("Error fetching units: " . $e->getMessage());
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
        // Disable error display for clean JSON response
        ini_set('display_errors', '0');
        error_reporting(E_ALL);

        // Ensure clean output buffer for JSON response
        while (ob_get_level()) {
            ob_end_clean();
        }
        ob_start();

        $this->requireAuth();

        if (!$this->verifyCSRFToken()) {
            ob_end_clean();
            header('Content-Type: application/json');
            http_response_code(403);
            echo json_encode(['error' => 'Invalid CSRF token']);
            exit;
        }

        $errors = $this->validate([
            'title' => 'required',
            'first_name' => 'required|min:2|max:100',
            'last_name' => 'required|min:2|max:100',
            'designation' => 'required',
            'staff_prefix' => 'required',
            'staff_number' => 'required',
            'blood_group' => 'required',
        ]);

        if (!empty($errors)) {
            ob_end_clean();
            header('Content-Type: application/json');
            http_response_code(422);
            echo json_encode(['errors' => $errors]);
            exit;
        }

        try {
            $user = $this->getCurrentUser();
            if (!$user) {
                ob_end_clean();
                header('Content-Type: application/json');
                http_response_code(404);
                echo json_encode(['error' => 'User not found']);
                exit;
            }

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

            if (!empty($staffPrefix) && in_array($staffPrefix, ['TSU/SP/', 'TSU/JP/'], true)) {
                $combinedStaffNumber = $staffPrefix . $staffNumberRaw;
            } elseif (!empty($registrationData['staff_number'])) {
                $combinedStaffNumber = $registrationData['staff_number'];
            }

            // Handle profile photo upload
            $profilePhotoPath = null;
            if (isset($_FILES['profile_photo']) && $_FILES['profile_photo']['error'] === UPLOAD_ERR_OK) {
                try {
                    $profilePhotoPath = FileUploadHelper::uploadProfilePhoto($_FILES['profile_photo'], $user['id']);
                } catch (\Exception $e) {
                    error_log('Photo upload failed: ' . $e->getMessage());
                }
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
                'staff_type' => $registrationData['staff_type'] ?? 'teaching',
                'title' => $this->sanitizeInput($this->input('title')),
                'first_name' => $firstName,
                'middle_name' => $this->sanitizeInput($this->input('middle_name')),
                'last_name' => $lastName,
                'faculty' => $registrationData['faculty'] ?? '',
                'department' => $registrationData['department'] ?? '',
                'unit' => $registrationData['unit'] ?? null,
                'designation' => $this->sanitizeInput($this->input('designation')),
                'blood_group' => $this->sanitizeInput($this->input('blood_group')),
                'office_location' => $this->sanitizeInput($this->input('office_location')),
                'office_phone' => $this->sanitizeInput($this->input('office_phone')),
                'professional_summary' => $this->sanitizeInput($this->input('professional_summary')),
                'research_interests' => $this->sanitizeInput($this->input('research_interests')),
                'expertise_keywords' => $this->sanitizeInput($this->input('expertise_keywords')),
                'profile_visibility' => $registrationData['profile_visibility'] ?? 'public',
                'profile_slug' => $profileSlug,
            ];

            // Add profile photo if uploaded
            if ($profilePhotoPath) {
                $profileData['profile_photo'] = $profilePhotoPath;
            }

            $profileId = $this->profileModel->create($profileData);

            if ($profileId) {
                // Generate QR code for profile
                try {
                    require_once __DIR__ . '/../Helpers/QRCodeHelper.php';
                    $qrCodePath = \App\Helpers\QRCodeHelper::generateProfileQRCode($user['id'], $profileSlug);
                    if ($qrCodePath) {
                        $this->db->update(
                            'profiles',
                            ['qr_code_path' => $qrCodePath],
                            'user_id = ?',
                            [$user['id']]
                        );
                    }
                } catch (\Exception $e) {
                    error_log('QR code generation failed: ' . $e->getMessage());
                }

                // Clear registration data from session
                unset($_SESSION['registration_data']);

                // Activate account now that profile is complete
                if ($this->db) {
                    try {
                        $this->db->update(
                            'users',
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
                        $this->db->update(
                            'users',
                            ['profile_completion' => $stats['completion']],
                            'id = ?',
                            [$user['id']]
                        );
                    } catch (\Exception $e) {
                        error_log('Profile completion update failed: ' . $e->getMessage());
                    }
                }

                // Log activity
                try {
                    $this->logActivity('profile_created', ['profile_id' => $profileId]);
                    $this->logActivity('account_activated', ['user_id' => $user['id']]);
                } catch (\Exception $e) {
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
            ob_end_clean();
            header('Content-Type: application/json');
            http_response_code(500);
            echo json_encode([
                'error' => 'Profile creation failed: ' . $e->getMessage()
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
                    'title',
                    'first_name',
                    'middle_name',
                    'last_name',
                    'faculty',
                    'department',
                    'designation',
                    'office_location',
                    'office_phone',
                    'professional_summary',
                    'research_interests',
                    'expertise_keywords',
                    'blood_group',
                    'unit'
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

        // Get faculties, departments, and units
        $faculties = $this->getFacultiesWithDepartments();
        $units = $this->getUnits();

        $this->view('profile/edit', [
            'csrf_token' => $this->generateCSRFToken(),
            'profile' => $profile,
            'faculties' => $faculties,
            'units' => $units,
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

        // Get staff type for conditional validation
        $staffType = $this->sanitizeInput($this->input('staff_type')) ?: ($profile['staff_type'] ?? 'teaching');

        // Base validation rules
        $validationRules = [
            'title' => 'required',
            'designation' => 'required|max:255',
            'blood_group' => 'required',
            'office_location' => 'max:100',
            'office_phone' => 'max:20',
            'professional_summary' => 'max:1000',
            'research_interests' => 'max:500',
            'expertise_keywords' => 'max:500',
        ];

        // Conditional validation based on staff type
        if ($staffType === 'teaching') {
            $validationRules['faculty'] = 'required|max:255';
            $validationRules['department'] = 'required|max:255';
        }

        $errors = $this->validate($validationRules);

        // Additional validation for non-teaching staff
        if ($staffType === 'non-teaching') {
            $unit = $this->sanitizeInput($this->input('unit'));
            $faculty = $this->sanitizeInput($this->input('faculty'));
            $department = $this->sanitizeInput($this->input('department'));

            // Must have either unit OR (faculty + department)
            if (empty($unit) && empty($faculty) && empty($department)) {
                $errors['staff_location'] = 'Please select either a Unit/Office OR Faculty/Department';
            }

            // If faculty selected, department must also be selected
            if (!empty($faculty) && empty($department)) {
                $errors['department'] = 'Please select a department for the selected faculty';
            }
        }

        if (!empty($errors)) {
            $this->json(['errors' => $errors], 422);
            return;
        }

        try {
            // Combine staff prefix and number
            $staffPrefix = $this->sanitizeInput($this->input('staff_prefix'));
            $staffNumber = $this->sanitizeInput($this->input('staff_number'));

            // Only update staff number if provided
            $fullStaffNumber = (!empty($staffPrefix) && !empty($staffNumber)) ? $staffPrefix . $staffNumber : $profile['staff_number'];

            // Check if staff number is being changed and if new number already exists
            if ($fullStaffNumber !== $profile['staff_number']) {
                try {
                    $existingStaff = $this->db->fetch(
                        "SELECT id FROM profiles WHERE staff_number = ? AND user_id != ?",
                        [$fullStaffNumber, $user['id']]
                    );
                    if ($existingStaff) {
                        $this->json(['error' => 'This staff number is already registered to another user'], 422);
                        return;
                    }
                } catch (\Exception $e) {
                    error_log("Staff number check failed: " . $e->getMessage());
                }
            }

            $updateData = [
                'first_name' => $this->sanitizeInput($this->input('first_name')),
                'middle_name' => $this->sanitizeInput($this->input('middle_name')),
                'last_name' => $this->sanitizeInput($this->input('last_name')),
                'staff_number' => $fullStaffNumber,
                'staff_type' => $staffType,
                'title' => $this->sanitizeInput($this->input('title')),
                'designation' => $this->sanitizeInput($this->input('designation')),
                'blood_group' => $this->sanitizeInput($this->input('blood_group')),
                'office_location' => $this->sanitizeInput($this->input('office_location')),
                'office_phone' => $this->sanitizeInput($this->input('office_phone')),
                'professional_summary' => $this->sanitizeInput($this->input('professional_summary')),
                'research_interests' => $this->sanitizeInput($this->input('research_interests')),
                'expertise_keywords' => $this->sanitizeInput($this->input('expertise_keywords')),
                'profile_visibility' => $this->sanitizeInput($this->input('profile_visibility')) ?: 'public',
                'updated_at' => date('Y-m-d H:i:s'),
            ];

            // Handle staff type specific fields
            if ($staffType === 'teaching') {
                $updateData['faculty'] = $this->sanitizeInput($this->input('faculty'));
                $updateData['department'] = $this->sanitizeInput($this->input('department'));
                $updateData['unit'] = null;
            } else {
                // Non-teaching staff
                $unit = $this->sanitizeInput($this->input('unit'));
                $faculty = $this->sanitizeInput($this->input('faculty'));
                $department = $this->sanitizeInput($this->input('department'));

                if (!empty($unit)) {
                    // Unit selected, clear faculty/department
                    $updateData['unit'] = $unit;
                    $updateData['faculty'] = null;
                    $updateData['department'] = null;
                } else {
                    // Faculty/Department selected, clear unit
                    $updateData['unit'] = null;
                    $updateData['faculty'] = $faculty;
                    $updateData['department'] = $department;
                }
            }

            // Handle profile photo upload
            if (isset($_FILES['profile_photo']) && $_FILES['profile_photo']['error'] === UPLOAD_ERR_OK) {
                try {
                    $photoPath = FileUploadHelper::uploadProfilePhoto($_FILES['profile_photo'], $user['id']);
                    $updateData['profile_photo'] = $photoPath;

                    // Delete old photo if exists
                    if (!empty($profile['profile_photo'])) {
                        FileUploadHelper::deleteFile($profile['profile_photo']);
                    }
                } catch (\Exception $e) {
                    $this->json(['error' => 'Photo upload failed: ' . $e->getMessage()], 422);
                    return;
                }
            }

            // Handle CV upload
            if (isset($_FILES['cv_file']) && $_FILES['cv_file']['error'] === UPLOAD_ERR_OK) {
                try {
                    $cvPath = FileUploadHelper::uploadDocument($_FILES['cv_file'], $user['id'], 'cv');
                    $updateData['cv_file'] = $cvPath;

                    // Delete old CV if exists
                    if (!empty($profile['cv_file'])) {
                        FileUploadHelper::deleteFile($profile['cv_file']);
                    }
                } catch (\Exception $e) {
                    $this->json(['error' => 'CV upload failed: ' . $e->getMessage()], 422);
                    return;
                }
            }

            $success = $this->profileModel->update($user['id'], $updateData);

            if ($success) {
                // Update profile completion
                if ($this->db) {
                    $stats = $this->profileModel->getProfileStats($user['id']);
                    $this->db->update(
                        'users',
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
        } else {
            // document
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
                error_log("Education fetch error: " . $e->getMessage());
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
        if (!$this->verifyCSRFToken()) {
            $this->json(['error' => 'Invalid CSRF token'], 403);
            return;
        }

        $user = $this->getCurrentUser();
        try {
            $data = [
                'user_id' => $user['id'],
                'institution' => $this->sanitizeInput($this->input('institution')),
                'degree' => $this->sanitizeInput($this->input('degree')),
                'field_of_study' => $this->sanitizeInput($this->input('field_of_study')),
                'start_year' => $this->sanitizeInput($this->input('start_year')),
                'end_year' => $this->sanitizeInput($this->input('end_year')),
                'description' => $this->sanitizeInput($this->input('description')),
            ];
            $this->db->insert('education', $data);
            $this->json(['success' => true, 'message' => 'Education added successfully']);
        } catch (\Exception $e) {
            $this->json(['error' => 'Failed to add education'], 500);
        }
    }

    public function updateEducation(int $id): void
    {
        $this->requireAuth();
        if (!$this->verifyCSRFToken()) {
            $this->json(['error' => 'Invalid CSRF token'], 403);
            return;
        }

        $user = $this->getCurrentUser();
        try {
            $data = [
                'institution' => $this->sanitizeInput($this->input('institution')),
                'degree' => $this->sanitizeInput($this->input('degree')),
                'field_of_study' => $this->sanitizeInput($this->input('field_of_study')),
                'start_year' => $this->sanitizeInput($this->input('start_year')),
                'end_year' => $this->sanitizeInput($this->input('end_year')),
                'description' => $this->sanitizeInput($this->input('description')),
            ];
            $this->db->update('education', $data, 'id = ? AND user_id = ?', [$id, $user['id']]);
            $this->json(['success' => true, 'message' => 'Education updated successfully']);
        } catch (\Exception $e) {
            $this->json(['error' => 'Failed to update education'], 500);
        }
    }

    public function deleteEducation(int $id): void
    {
        $this->requireAuth();
        if (!$this->verifyCSRFToken()) {
            $this->json(['error' => 'Invalid CSRF token'], 403);
            return;
        }

        $user = $this->getCurrentUser();
        try {
            $this->db->delete('education', 'id = ? AND user_id = ?', [$id, $user['id']]);
            $this->json(['success' => true, 'message' => 'Education deleted successfully']);
        } catch (\Exception $e) {
            $this->json(['error' => 'Failed to delete education'], 500);
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
                error_log("Experience fetch error: " . $e->getMessage());
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

        $user = $this->getCurrentUser();
        try {
            $data = [
                'user_id' => $user['id'],
                'job_title' => $this->sanitizeInput($this->input('job_title')),
                'company' => $this->sanitizeInput($this->input('company')),
                'location' => $this->sanitizeInput($this->input('location')),
                'start_date' => $this->sanitizeInput($this->input('start_date')),
                'end_date' => $this->sanitizeInput($this->input('end_date')),
                'description' => $this->sanitizeInput($this->input('description')),
                'is_current' => $this->input('is_current') ? 1 : 0,
            ];
            $this->db->insert('experience', $data);
            $this->json(['success' => true, 'message' => 'Experience added successfully']);
        } catch (\Exception $e) {
            $this->json(['error' => 'Failed to add experience'], 500);
        }
    }

    public function updateExperience(int $id): void
    {
        $this->requireAuth();
        if (!$this->verifyCSRFToken()) {
            $this->json(['error' => 'Invalid CSRF token'], 403);
            return;
        }

        $user = $this->getCurrentUser();
        try {
            $data = [
                'job_title' => $this->sanitizeInput($this->input('job_title')),
                'company' => $this->sanitizeInput($this->input('company')),
                'location' => $this->sanitizeInput($this->input('location')),
                'start_date' => $this->sanitizeInput($this->input('start_date')),
                'end_date' => $this->sanitizeInput($this->input('end_date')),
                'description' => $this->sanitizeInput($this->input('description')),
                'is_current' => $this->input('is_current') ? 1 : 0,
            ];
            $this->db->update('experience', $data, 'id = ? AND user_id = ?', [$id, $user['id']]);
            $this->json(['success' => true, 'message' => 'Experience updated successfully']);
        } catch (\Exception $e) {
            $this->json(['error' => 'Failed to update experience'], 500);
        }
    }

    public function deleteExperience(int $id): void
    {
        $this->requireAuth();
        if (!$this->verifyCSRFToken()) {
            $this->json(['error' => 'Invalid CSRF token'], 403);
            return;
        }

        $user = $this->getCurrentUser();
        try {
            $this->db->delete('experience', 'id = ? AND user_id = ?', [$id, $user['id']]);
            $this->json(['success' => true, 'message' => 'Experience deleted successfully']);
        } catch (\Exception $e) {
            $this->json(['error' => 'Failed to delete experience'], 500);
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
                error_log("Skills fetch error: " . $e->getMessage());
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

        $user = $this->getCurrentUser();
        try {
            $data = [
                'user_id' => $user['id'],
                'skill_name' => $this->sanitizeInput($this->input('skill_name')),
                'proficiency_level' => $this->sanitizeInput($this->input('proficiency_level')),
                'years_of_experience' => $this->sanitizeInput($this->input('years_of_experience')),
            ];
            $this->db->insert('skills', $data);
            $this->json(['success' => true, 'message' => 'Skill added successfully']);
        } catch (\Exception $e) {
            $this->json(['error' => 'Failed to add skill'], 500);
        }
    }

    public function updateSkill(int $id): void
    {
        $this->requireAuth();
        if (!$this->verifyCSRFToken()) {
            $this->json(['error' => 'Invalid CSRF token'], 403);
            return;
        }

        $user = $this->getCurrentUser();
        try {
            $data = [
                'skill_name' => $this->sanitizeInput($this->input('skill_name')),
                'proficiency_level' => $this->sanitizeInput($this->input('proficiency_level')),
                'years_of_experience' => $this->sanitizeInput($this->input('years_of_experience')),
            ];
            $this->db->update('skills', $data, 'id = ? AND user_id = ?', [$id, $user['id']]);
            $this->json(['success' => true, 'message' => 'Skill updated successfully']);
        } catch (\Exception $e) {
            $this->json(['error' => 'Failed to update skill'], 500);
        }
    }

    public function deleteSkill(int $id): void
    {
        $this->requireAuth();
        if (!$this->verifyCSRFToken()) {
            $this->json(['error' => 'Invalid CSRF token'], 403);
            return;
        }

        $user = $this->getCurrentUser();
        try {
            $this->db->delete('skills', 'id = ? AND user_id = ?', [$id, $user['id']]);
            $this->json(['success' => true, 'message' => 'Skill deleted successfully']);
        } catch (\Exception $e) {
            $this->json(['error' => 'Failed to delete skill'], 500);
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
                    "SELECT * FROM publications WHERE user_id = ? ORDER BY year DESC, title ASC",
                    [$user['id']]
                );
            } catch (\Exception $e) {
                error_log("Publications fetch error: " . $e->getMessage());
            }
        }

        $this->view('profile/publications', [
            'csrf_token' => $this->generateCSRFToken(),
            'publications' => $publications,
        ]);
    }

    public function addPublication(): void
    {
        $this->requireAuth();
        if (!$this->verifyCSRFToken()) {
            $this->json(['error' => 'Invalid CSRF token'], 403);
            return;
        }

        $user = $this->getCurrentUser();
        try {
            $data = [
                'user_id' => $user['id'],
                'title' => $this->sanitizeInput($this->input('title')),
                'authors' => $this->sanitizeInput($this->input('authors')),
                'publication_type' => $this->sanitizeInput($this->input('publication_type')),
                'journal_conference' => $this->sanitizeInput($this->input('journal_conference')),
                'year' => $this->sanitizeInput($this->input('year')),
                'volume' => $this->sanitizeInput($this->input('volume')),
                'pages' => $this->sanitizeInput($this->input('pages')),
                'doi' => $this->sanitizeInput($this->input('doi')),
                'url' => $this->sanitizeInput($this->input('url')),
                'abstract' => $this->sanitizeInput($this->input('abstract')),
            ];
            $this->db->insert('publications', $data);
            $this->json(['success' => true, 'message' => 'Publication added successfully']);
        } catch (\Exception $e) {
            $this->json(['error' => 'Failed to add publication'], 500);
        }
    }

    public function updatePublication(int $id): void
    {
        $this->requireAuth();
        if (!$this->verifyCSRFToken()) {
            $this->json(['error' => 'Invalid CSRF token'], 403);
            return;
        }

        $user = $this->getCurrentUser();
        try {
            $data = [
                'title' => $this->sanitizeInput($this->input('title')),
                'authors' => $this->sanitizeInput($this->input('authors')),
                'publication_type' => $this->sanitizeInput($this->input('publication_type')),
                'journal_conference' => $this->sanitizeInput($this->input('journal_conference')),
                'year' => $this->sanitizeInput($this->input('year')),
                'volume' => $this->sanitizeInput($this->input('volume')),
                'pages' => $this->sanitizeInput($this->input('pages')),
                'doi' => $this->sanitizeInput($this->input('doi')),
                'url' => $this->sanitizeInput($this->input('url')),
                'abstract' => $this->sanitizeInput($this->input('abstract')),
            ];
            $this->db->update('publications', $data, 'id = ? AND user_id = ?', [$id, $user['id']]);
            $this->json(['success' => true, 'message' => 'Publication updated successfully']);
        } catch (\Exception $e) {
            $this->json(['error' => 'Failed to update publication'], 500);
        }
    }

    public function deletePublication(int $id): void
    {
        $this->requireAuth();
        if (!$this->verifyCSRFToken()) {
            $this->json(['error' => 'Invalid CSRF token'], 403);
            return;
        }

        $user = $this->getCurrentUser();
        try {
            $this->db->delete('publications', 'id = ? AND user_id = ?', [$id, $user['id']]);
            $this->json(['success' => true, 'message' => 'Publication deleted successfully']);
        } catch (\Exception $e) {
            $this->json(['error' => 'Failed to delete publication'], 500);
        }
    }
}