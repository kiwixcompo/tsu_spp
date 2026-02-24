<?php
if (!function_exists('url')) {
    require_once __DIR__ . '/../../Helpers/UrlHelper.php';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Terms and Conditions - TSU Staff Profile Portal</title>
    <link rel="icon" type="image/png" href="<?= asset('assets/images/tsu-logo.png') ?>">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        body { background: #f8f9fa; }
        .legal-header { background: linear-gradient(135deg, #1e40af 0%, #3b82f6 100%); color: white; padding: 60px 0; }
        .legal-content { background: white; border-radius: 12px; padding: 40px; margin: -40px auto 40px; box-shadow: 0 4px 20px rgba(0,0,0,0.1); }
        .section-title { color: #1e40af; margin-top: 30px; margin-bottom: 15px; font-weight: 600; }
        .last-updated { color: #6b7280; font-size: 0.9rem; font-style: italic; }
        h1 { font-size: 2.5rem; font-weight: 700; }
        h2 { font-size: 1.75rem; }
        h3 { font-size: 1.25rem; }
    </style>
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container">
            <a class="navbar-brand fw-bold" href="<?= url() ?>">
                <i class="fas fa-university me-2"></i>TSU Staff Profile Portal
            </a>
            <div class="navbar-nav ms-auto">
                <a class="nav-link" href="<?= url() ?>">Home</a>
                <a class="nav-link" href="<?= url('directory') ?>">Directory</a>
                <?php if (isset($_SESSION['user_id'])): ?>
                    <a class="nav-link" href="<?= url('dashboard') ?>">Dashboard</a>
                <?php else: ?>
                    <a class="nav-link" href="<?= url('login') ?>">Login</a>
                <?php endif; ?>
            </div>
        </div>
    </nav>

    <!-- Header -->
    <section class="legal-header">
        <div class="container text-center">
            <h1><i class="fas fa-file-contract me-3"></i>Terms and Conditions</h1>
            <p class="lead mb-0">Please read these terms carefully before using our services</p>
        </div>
    </section>

    <!-- Content -->
    <div class="container">
        <div class="legal-content">
            <p class="last-updated"><i class="fas fa-calendar-alt me-2"></i>Last Updated: February 24, 2026</p>

            <h2 class="section-title">1. Acceptance of Terms</h2>
            <p>By accessing and using the Taraba State University (TSU) Staff Profile Portal ("the Portal"), you accept and agree to be bound by these Terms and Conditions. If you do not agree to these terms, please do not use the Portal.</p>

            <h2 class="section-title">2. Eligibility</h2>
            <p>The Portal is exclusively available to:</p>
            <ul>
                <li>Current staff members of Taraba State University with valid TSU email addresses (@tsuniversity.edu.ng)</li>
                <li>Authorized administrators and personnel designated by the University</li>
                <li>Individuals who have been granted specific access permissions by the University administration</li>
            </ul>
            <p>By registering, you confirm that you are eligible to use this service and that all information provided is accurate and truthful.</p>

            <h2 class="section-title">3. Account Registration and Security</h2>
            <h3>3.1 Registration Requirements</h3>
            <ul>
                <li>You must use your official TSU email address for registration</li>
                <li>You must provide accurate and complete information during registration</li>
                <li>You must verify your email address to activate your account</li>
                <li>You must complete your profile setup with valid staff information</li>
            </ul>

            <h3>3.2 Account Security</h3>
            <ul>
                <li>You are responsible for maintaining the confidentiality of your password</li>
                <li>You must notify the administration immediately of any unauthorized access</li>
                <li>You are responsible for all activities that occur under your account</li>
                <li>The University reserves the right to suspend or terminate accounts that violate these terms</li>
            </ul>

            <h2 class="section-title">4. User Responsibilities</h2>
            <h3>4.1 Profile Information</h3>
            <ul>
                <li>You must ensure all profile information is accurate, current, and complete</li>
                <li>You must update your profile promptly when information changes</li>
                <li>You must not impersonate another person or provide false information</li>
                <li>You are responsible for the accuracy of your academic credentials, publications, and professional information</li>
            </ul>

            <h3>4.2 Acceptable Use</h3>
            <p>You agree NOT to:</p>
            <ul>
                <li>Use the Portal for any unlawful purpose or in violation of University policies</li>
                <li>Upload or share content that is offensive, defamatory, or inappropriate</li>
                <li>Attempt to gain unauthorized access to other users' accounts or data</li>
                <li>Interfere with or disrupt the Portal's functionality or security</li>
                <li>Use automated systems (bots, scrapers) to access the Portal without permission</li>
                <li>Harvest or collect information about other users without their consent</li>
                <li>Share your account credentials with others</li>
            </ul>

            <h2 class="section-title">5. Content and Intellectual Property</h2>
            <h3>5.1 Your Content</h3>
            <ul>
                <li>You retain ownership of content you upload (photos, CVs, publications, etc.)</li>
                <li>By uploading content, you grant TSU a license to display, store, and use it within the Portal</li>
                <li>You represent that you have the right to upload all content you submit</li>
                <li>You are responsible for ensuring your content does not infringe on others' rights</li>
            </ul>

            <h3>5.2 University Content</h3>
            <ul>
                <li>The Portal's design, features, and functionality are owned by Taraba State University</li>
                <li>University logos, trademarks, and branding materials are protected intellectual property</li>
                <li>You may not reproduce, distribute, or create derivative works without permission</li>
            </ul>

            <h2 class="section-title">6. Privacy and Data Protection</h2>
            <p>Your use of the Portal is also governed by our <a href="<?= url('privacy-policy') ?>">Privacy Policy</a>, which explains how we collect, use, and protect your personal information. By using the Portal, you consent to our data practices as described in the Privacy Policy.</p>

            <h2 class="section-title">7. Profile Visibility and Access Control</h2>
            <ul>
                <li>You can control your profile visibility (Public, University Only, or Private)</li>
                <li>Public profiles may be visible to anyone accessing the Portal</li>
                <li>University-only profiles are visible to authenticated TSU staff members</li>
                <li>Private profiles display limited information to non-owners</li>
                <li>The University administration may access all profiles for legitimate purposes</li>
            </ul>

            <h2 class="section-title">8. ID Card Generation</h2>
            <ul>
                <li>ID cards generated through the Portal are official University documents</li>
                <li>You must not alter, duplicate, or misuse generated ID cards</li>
                <li>Lost or damaged ID cards must be reported to the administration</li>
                <li>The University reserves the right to revoke ID cards for policy violations</li>
            </ul>

            <h2 class="section-title">9. Termination and Suspension</h2>
            <h3>9.1 By You</h3>
            <p>You may request account deletion by contacting the University administration. Note that some information may be retained for legal or administrative purposes.</p>

            <h3>9.2 By the University</h3>
            <p>The University may suspend or terminate your account if:</p>
            <ul>
                <li>You violate these Terms and Conditions</li>
                <li>You are no longer employed by Taraba State University</li>
                <li>Your account shows suspicious or unauthorized activity</li>
                <li>Required by law or University policy</li>
            </ul>

            <h2 class="section-title">10. Disclaimers and Limitations of Liability</h2>
            <h3>10.1 Service Availability</h3>
            <ul>
                <li>The Portal is provided "as is" without warranties of any kind</li>
                <li>We do not guarantee uninterrupted or error-free service</li>
                <li>We may modify, suspend, or discontinue features at any time</li>
                <li>Scheduled maintenance may temporarily affect availability</li>
            </ul>

            <h3>10.2 Limitation of Liability</h3>
            <p>To the maximum extent permitted by law, Taraba State University shall not be liable for:</p>
            <ul>
                <li>Any indirect, incidental, or consequential damages</li>
                <li>Loss of data, profits, or business opportunities</li>
                <li>Damages resulting from unauthorized access to your account</li>
                <li>Content posted by other users</li>
            </ul>

            <h2 class="section-title">11. Changes to Terms</h2>
            <p>The University reserves the right to modify these Terms and Conditions at any time. Changes will be effective immediately upon posting. Your continued use of the Portal after changes constitutes acceptance of the modified terms. We encourage you to review these terms periodically.</p>

            <h2 class="section-title">12. Governing Law</h2>
            <p>These Terms and Conditions are governed by the laws of the Federal Republic of Nigeria and the regulations of Taraba State University. Any disputes shall be resolved in accordance with University policies and Nigerian law.</p>

            <h2 class="section-title">13. Contact Information</h2>
            <p>For questions, concerns, or reports regarding these Terms and Conditions, please contact:</p>
            <div class="alert alert-info">
                <strong>Taraba State University</strong><br>
                ICT Department / Staff Portal Administration<br>
                Email: <a href="mailto:ict@tsuniversity.edu.ng">ict@tsuniversity.edu.ng</a><br>
                Website: <a href="https://tsuniversity.edu.ng" target="_blank">www.tsuniversity.edu.ng</a>
            </div>

            <h2 class="section-title">14. Severability</h2>
            <p>If any provision of these Terms and Conditions is found to be invalid or unenforceable, the remaining provisions shall continue in full force and effect.</p>

            <h2 class="section-title">15. Entire Agreement</h2>
            <p>These Terms and Conditions, together with our Privacy Policy, constitute the entire agreement between you and Taraba State University regarding the use of the Portal.</p>

            <hr class="my-5">

            <div class="text-center">
                <p class="text-muted mb-3">By using the TSU Staff Profile Portal, you acknowledge that you have read, understood, and agree to be bound by these Terms and Conditions.</p>
                <a href="<?= url() ?>" class="btn btn-primary me-2">
                    <i class="fas fa-home me-2"></i>Return to Home
                </a>
                <a href="<?= url('privacy-policy') ?>" class="btn btn-outline-primary">
                    <i class="fas fa-shield-alt me-2"></i>Privacy Policy
                </a>
            </div>
        </div>
    </div>

    <footer class="bg-dark text-white py-4 mt-5">
        <div class="container text-center">
            <p class="mb-2">&copy; <?= date('Y') ?> Taraba State University. All rights reserved.</p>
            <p class="mb-0">
                <a href="<?= url('terms') ?>" class="text-white me-3">Terms & Conditions</a>
                <a href="<?= url('privacy-policy') ?>" class="text-white">Privacy Policy</a>
            </p>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
