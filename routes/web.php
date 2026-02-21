<?php
/**
 * Web Routes for TSU Staff Portal
 */

// Home and public pages
$router->get('/', 'HomeController@index');
$router->get('/about', 'HomeController@about');
$router->get('/search', 'SearchController@index');
$router->get('/directory', 'DirectoryController@index');

// Authentication routes
$router->get('/register', 'AuthController@showRegister');
$router->post('/register', 'AuthController@register');
$router->get('/login', 'AuthController@showLogin');
$router->post('/login', 'AuthController@login');
$router->get('/logout', 'AuthController@logout');
$router->get('/verify-email', 'AuthController@showVerifyEmail');
$router->post('/verify-email', 'AuthController@verifyEmail');
$router->post('/resend-verification', 'AuthController@resendVerification');
$router->post('/check-verification-status', 'AuthController@checkVerificationStatus');
$router->get('/forgot-password', 'AuthController@showForgotPassword');
$router->post('/forgot-password', 'AuthController@forgotPassword');
$router->get('/reset-password', 'AuthController@showResetPassword');
$router->post('/reset-password', 'AuthController@resetPassword');

// Protected routes (require authentication)
$router->get('/dashboard', 'DashboardController@index', ['Auth']);
$router->get('/profile/setup', 'ProfileController@showSetup', ['Auth']);
$router->post('/profile/setup', 'ProfileController@setup', ['Auth']);
$router->get('/profile/edit', 'ProfileController@showEdit', ['Auth']);
$router->post('/profile/update', 'ProfileController@update', ['Auth']);

// Profile sections management
$router->get('/profile/education', 'ProfileController@showEducation', ['Auth']);
$router->post('/profile/education', 'ProfileController@addEducation', ['Auth']);
$router->put('/profile/education/{id}', 'ProfileController@updateEducation', ['Auth']);
$router->delete('/profile/education/{id}', 'ProfileController@deleteEducation', ['Auth']);

$router->get('/profile/experience', 'ProfileController@showExperience', ['Auth']);
$router->post('/profile/experience', 'ProfileController@addExperience', ['Auth']);
$router->put('/profile/experience/{id}', 'ProfileController@updateExperience', ['Auth']);
$router->delete('/profile/experience/{id}', 'ProfileController@deleteExperience', ['Auth']);

$router->get('/profile/publications', 'ProfileController@showPublications', ['Auth']);
$router->post('/profile/publications/add', 'ProfileController@addPublication', ['Auth']);
$router->put('/profile/publications/{id}', 'ProfileController@updatePublication', ['Auth']);
$router->delete('/profile/publications/delete/{id}', 'ProfileController@deletePublication', ['Auth']);

$router->get('/profile/skills', 'ProfileController@showSkills', ['Auth']);
$router->post('/profile/skills', 'ProfileController@addSkill', ['Auth']);
$router->put('/profile/skills/{id}', 'ProfileController@updateSkill', ['Auth']);
$router->delete('/profile/skills/{id}', 'ProfileController@deleteSkill', ['Auth']);

$router->get('/profile/certifications', 'ProfileController@showCertifications', ['Auth']);
$router->post('/profile/certifications', 'ProfileController@addCertification', ['Auth']);
$router->put('/profile/certifications/{id}', 'ProfileController@updateCertification', ['Auth']);
$router->delete('/profile/certifications/{id}', 'ProfileController@deleteCertification', ['Auth']);

$router->get('/profile/awards', 'ProfileController@showAwards', ['Auth']);
$router->post('/profile/awards', 'ProfileController@addAward', ['Auth']);
$router->put('/profile/awards/{id}', 'ProfileController@updateAward', ['Auth']);
$router->delete('/profile/awards/{id}', 'ProfileController@deleteAward', ['Auth']);

$router->get('/profile/memberships', 'ProfileController@showMemberships', ['Auth']);
$router->post('/profile/memberships', 'ProfileController@addMembership', ['Auth']);
$router->put('/profile/memberships/{id}', 'ProfileController@updateMembership', ['Auth']);
$router->delete('/profile/memberships/{id}', 'ProfileController@deleteMembership', ['Auth']);

// File upload routes
$router->post('/upload/photo', 'UploadController@profilePhoto', ['Auth']);
$router->post('/upload/document', 'UploadController@document', ['Auth']);

// Public profile routes
$router->get('/profile/{slug}', 'DirectoryController@show');
$router->get('/directory/profile/{slug}', 'DirectoryController@show');
$router->get('/profile/{slug}/vcard', 'ProfileController@downloadVCard');

// Settings routes
$router->get('/settings', 'SettingsController@index', ['Auth']);
$router->post('/settings/profile', 'SettingsController@updateProfile', ['Auth']);
$router->post('/settings/privacy', 'SettingsController@updatePrivacy', ['Auth']);
$router->post('/settings/password', 'SettingsController@updatePassword', ['Auth']);
$router->post('/settings/delete-account', 'SettingsController@deleteAccount', ['Auth']);

// Admin routes (require admin role)
$router->get('/admin', 'AdminController@dashboard', ['Auth', 'Admin']);
$router->get('/admin/dashboard', 'AdminController@dashboard', ['Auth', 'Admin']);
$router->get('/admin/users', 'AdminController@users', ['Auth', 'Admin']);
$router->get('/admin/user-details', 'AdminController@userDetails', ['Auth', 'Admin']);
$router->get('/admin/users/{id}', 'AdminController@showUser', ['Auth', 'Admin']);
$router->post('/admin/users/{id}/suspend', 'AdminController@suspendUser', ['Auth', 'Admin']);
$router->post('/admin/users/{id}/activate', 'AdminController@activateUser', ['Auth', 'Admin']);
$router->post('/admin/activate-user', 'AdminController@activateUser', ['Auth', 'Admin']);
$router->post('/admin/suspend-user', 'AdminController@suspendUser', ['Auth', 'Admin']);
$router->post('/admin/reinstate-user', 'AdminController@reinstateUser', ['Auth', 'Admin']);
$router->post('/admin/verify-user', 'AdminController@verifyUser', ['Auth', 'Admin']);
$router->post('/admin/delete-user', 'AdminController@deleteUser', ['Auth', 'Admin']);
$router->post('/admin/bulk-delete-users', 'AdminController@bulkDeleteUsers', ['Auth', 'Admin']);
$router->post('/admin/bulk-suspend-users', 'AdminController@bulkSuspendUsers', ['Auth', 'Admin']);
$router->post('/admin/bulk-activate-users', 'AdminController@bulkActivateUsers', ['Auth', 'Admin']);
$router->post('/admin/bulk-verify-users', 'AdminController@bulkVerifyUsers', ['Auth', 'Admin']);
$router->get('/admin/publications', 'AdminController@publications', ['Auth', 'Admin']);
$router->get('/admin/analytics', 'AdminController@analytics', ['Auth', 'Admin']);
$router->get('/admin/activity-logs', 'AdminController@activityLogs', ['Auth', 'Admin']);
$router->get('/admin/faculties-departments', 'AdminController@facultiesDepartments', ['Auth', 'Admin']);
$router->post('/admin/add-faculty', 'AdminController@addFaculty', ['Auth', 'Admin']);
$router->post('/admin/add-department', 'AdminController@addDepartment', ['Auth', 'Admin']);
$router->delete('/admin/delete-department', 'AdminController@deleteDepartment', ['Auth', 'Admin']);
$router->get('/admin/units', 'AdminController@unitsManagement', ['Auth', 'Admin']);
$router->post('/admin/units/add', 'AdminController@addUnit', ['Auth', 'Admin']);
$router->post('/admin/units/update', 'AdminController@updateUnit', ['Auth', 'Admin']);
$router->post('/admin/units/delete', 'AdminController@deleteUnit', ['Auth', 'Admin']);
$router->get('/admin/settings', 'AdminController@settings', ['Auth', 'Admin']);
$router->post('/admin/settings', 'AdminController@updateSettings', ['Auth', 'Admin']);

// ID Card routes (Admin only)
$router->get('/admin/id-cards', 'IDCardController@index', ['Auth', 'Admin']);
$router->get('/admin/id-cards/preview/{id}', 'IDCardController@preview', ['Auth', 'Admin']);
$router->get('/admin/id-cards/generated', 'IDCardController@generatedCards', ['Auth', 'Admin']);
$router->post('/admin/id-cards/generate/{id}', 'IDCardController@generate', ['Auth', 'Admin']);
$router->post('/admin/id-cards/regenerate-qr/{id}', 'IDCardController@regenerateQR', ['Auth', 'Admin']);
$router->post('/admin/id-cards/bulk-generate', 'IDCardController@bulkGenerate', ['Auth', 'Admin']);

// QR Code serving route (public access for ID cards)
$router->get('/qrcode/{filename}', 'IDCardController@serveQRCode');

// Utility routes
$router->get('/departments/{faculty}', 'UtilityController@getDepartments');
$router->get('/faculties-departments', 'UtilityController@getAllFacultiesAndDepartments');
$router->get('/health', 'UtilityController@healthCheck');

// ID Card Manager routes (for id_card_manager role and admin)
$router->get('/id-card-manager/dashboard', 'IDCardManagerController@dashboard', ['Auth', 'IDCardManager']);
$router->get('/id-card-manager/browse', 'IDCardManagerController@browse', ['Auth', 'IDCardManager']);
$router->get('/id-card-manager/preview/{id}', 'IDCardManagerController@preview', ['Auth', 'IDCardManager']);
$router->get('/id-card-manager/print-history', 'IDCardManagerController@printHistory', ['Auth', 'IDCardManager']);
$router->post('/id-card-manager/bulk-print', 'IDCardManagerController@bulkPrint', ['Auth', 'IDCardManager']);
$router->post('/id-card-manager/print-single', 'IDCardManagerController@printSingle', ['Auth', 'IDCardManager']);
$router->get('/id-card-manager/generated-cards', 'IDCardManagerController@generatedCards', ['Auth', 'IDCardManager']);
$router->get('/id-card-manager/settings', 'IDCardManagerController@settings', ['Auth', 'IDCardManager']);
$router->post('/id-card-manager/settings', 'IDCardManagerController@settings', ['Auth', 'IDCardManager']);

// Admin can also access ID card generator
$router->get('/admin/id-card-generator', 'IDCardController@generator', ['Auth', 'Admin']);
$router->get('/admin/id-card-preview', 'IDCardController@preview', ['Auth', 'Admin']);
