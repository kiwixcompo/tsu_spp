<?php
/**
 * API Routes for TSU Staff Portal
 * Base URL: /api/v1
 */

// Public API endpoints
$router->get('/api/v1/profiles', 'Api\ProfileController@index');
$router->get('/api/v1/profiles/{slug}', 'Api\ProfileController@show');
$router->get('/api/v1/search', 'Api\SearchController@search');
$router->get('/api/v1/faculties', 'Api\UtilityController@getFaculties');
$router->get('/api/v1/departments/{faculty}', 'Api\UtilityController@getDepartments');
$router->get('/api/v1/stats', 'Api\UtilityController@getStats');

// Simple faculties endpoint for profile setup
$router->get('/api/faculties', 'ProfileController@getFaculties');

// Authenticated API endpoints
$router->get('/api/v1/user/profile', 'Api\UserController@getProfile', ['Auth']);
$router->put('/api/v1/user/profile', 'Api\UserController@updateProfile', ['Auth']);

// Education endpoints
$router->get('/api/v1/user/education', 'Api\EducationController@index', ['Auth']);
$router->post('/api/v1/user/education', 'Api\EducationController@store', ['Auth']);
$router->put('/api/v1/user/education/{id}', 'Api\EducationController@update', ['Auth']);
$router->delete('/api/v1/user/education/{id}', 'Api\EducationController@delete', ['Auth']);

// Experience endpoints
$router->get('/api/v1/user/experience', 'Api\ExperienceController@index', ['Auth']);
$router->post('/api/v1/user/experience', 'Api\ExperienceController@store', ['Auth']);
$router->put('/api/v1/user/experience/{id}', 'Api\ExperienceController@update', ['Auth']);
$router->delete('/api/v1/user/experience/{id}', 'Api\ExperienceController@delete', ['Auth']);

// Publications endpoints
$router->get('/api/v1/user/publications', 'Api\PublicationController@index', ['Auth']);
$router->post('/api/v1/user/publications', 'Api\PublicationController@store', ['Auth']);
$router->put('/api/v1/user/publications/{id}', 'Api\PublicationController@update', ['Auth']);
$router->delete('/api/v1/user/publications/{id}', 'Api\PublicationController@delete', ['Auth']);

// Skills endpoints
$router->get('/api/v1/user/skills', 'Api\SkillController@index', ['Auth']);
$router->post('/api/v1/user/skills', 'Api\SkillController@store', ['Auth']);
$router->put('/api/v1/user/skills/{id}', 'Api\SkillController@update', ['Auth']);
$router->delete('/api/v1/user/skills/{id}', 'Api\SkillController@delete', ['Auth']);

// Certifications endpoints
$router->get('/api/v1/user/certifications', 'Api\CertificationController@index', ['Auth']);
$router->post('/api/v1/user/certifications', 'Api\CertificationController@store', ['Auth']);
$router->put('/api/v1/user/certifications/{id}', 'Api\CertificationController@update', ['Auth']);
$router->delete('/api/v1/user/certifications/{id}', 'Api\CertificationController@delete', ['Auth']);

// Awards endpoints
$router->get('/api/v1/user/awards', 'Api\AwardController@index', ['Auth']);
$router->post('/api/v1/user/awards', 'Api\AwardController@store', ['Auth']);
$router->put('/api/v1/user/awards/{id}', 'Api\AwardController@update', ['Auth']);
$router->delete('/api/v1/user/awards/{id}', 'Api\AwardController@delete', ['Auth']);

// Memberships endpoints
$router->get('/api/v1/user/memberships', 'Api\MembershipController@index', ['Auth']);
$router->post('/api/v1/user/memberships', 'Api\MembershipController@store', ['Auth']);
$router->put('/api/v1/user/memberships/{id}', 'Api\MembershipController@update', ['Auth']);
$router->delete('/api/v1/user/memberships/{id}', 'Api\MembershipController@delete', ['Auth']);

// Admin API endpoints
$router->get('/api/v1/admin/users', 'Api\AdminController@getUsers', ['Auth', 'Admin']);
$router->get('/api/v1/admin/analytics', 'Api\AdminController@getAnalytics', ['Auth', 'Admin']);
$router->post('/api/v1/admin/users/{id}/suspend', 'Api\AdminController@suspendUser', ['Auth', 'Admin']);
$router->post('/api/v1/admin/users/{id}/activate', 'Api\AdminController@activateUser', ['Auth', 'Admin']);