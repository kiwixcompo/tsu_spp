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
    <title>Privacy Policy - TSU Staff Profile Portal</title>
    <link rel="icon" type="image/png" href="<?= asset('assets/images/tsu-logo.png') ?>">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        body { background: #f8f9fa; }
        .legal-header { background: linear-gradient(135deg, #059669 0%, #10b981 100%); color: white; padding: 60px 0; }
        .legal-content { background: white; border-radius: 12px; padding: 40px; margin: -40px auto 40px; box-shadow: 0 4px 20px rgba(0,0,0,0.1); }
        .section-title { color: #059669; margin-top: 30px; margin-bottom: 15px; font-weight: 600; }
        .last-updated { color: #6b7280; font-size: 0.9rem; font-style: italic; }
        h1 { font-size: 2.5rem; font-weight: 700; }
        h2 { font-size: 1.75rem; }
        h3 { font-size: 1.25rem; }
        .data-table { background: #f8f9fa; border-radius: 8px; padding: 20px; margin: 20px 0; }
    </style>
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-success">
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
            <h1><i class="fas fa-shield-alt me-3"></i>Privacy Policy</h1>
            <p class="lead mb-0">Your privacy and data security are our priorities</p>
        </div>
    </section>

    <!-- Content -->
    <div class="container">
        <div class="legal-content">
            <p class="last-updated"><i class="fas fa-calendar-alt me-2"></i>Last Updated: February 24, 2026</p>

            <h2 class="section-title">1. Introduction</h2>
            <p>Taraba State University ("TSU", "we", "us", or "our") is committed to protecting the privacy and security of your personal information. This Privacy Policy explains how we collect, use, store, and protect your data when you use the TSU Staff Profile Portal ("the Portal").</p>
            <p>By using the Portal, you consent to the data practices described in this policy. Please read this policy carefully to understand how we handle your information.</p>

            <h2 class="section-title">2. Information We Collect</h2>
            
            <h3>2.1 Information You Provide Directly</h3>
            <div class="data-table">
                <strong>Account Information:</strong>
                <ul>
                    <li>Email address (TSU institutional email)</li>
                    <li>Password (encrypted and hashed)</li>
                    <li>Staff ID number</li>
                    <li>Email verification code</li>
                </ul>

                <strong>Profile Information:</strong>
                <ul>
                    <li>Personal details: Title, first name, middle name, last name, gender</li>
                    <li>Professional information: Designation, faculty, department, unit</li>
                    <li>Contact information: Office location, office phone</li>
                    <li>Biographical data: Professional summary, research interests, expertise keywords</li>
                    <li>Blood group (for ID card purposes)</li>
                </ul>

                <strong>Academic and Professional Records:</strong>
                <ul>
                    <li>Educational qualifications (degrees, institutions, years)</li>
                    <li>Work experience (positions, organizations, dates)</li>
                    <li>Publications (titles, authors, journals, DOIs)</li>
                    <li>Skills and proficiency levels</li>
                    <li>Certifications and awards</li>
                    <li>Professional memberships</li>
                </ul>

                <strong>Uploaded Files:</strong>
                <ul>
                    <li>Profile photographs</li>
                    <li>Curriculum Vitae (CV) documents</li>
                    <li>Supporting documents</li>
                </ul>
            </div>

            <h3>2.2 Information Collected Automatically</h3>
            <ul>
                <li><strong>Usage Data:</strong> Pages visited, features used, time spent on the Portal</li>
                <li><strong>Device Information:</strong> IP address, browser type, operating system</li>
                <li><strong>Activity Logs:</strong> Login times, profile updates, actions performed</li>
                <li><strong>Profile Views:</strong> Number of times your profile has been viewed</li>
            </ul>

            <h2 class="section-title">3. How We Use Your Information</h2>
            <p>We use your information for the following purposes:</p>

            <h3>3.1 Service Provision</h3>
            <ul>
                <li>Create and manage your staff profile</li>
                <li>Authenticate your identity and provide secure access</li>
                <li>Generate official staff ID cards</li>
                <li>Display your profile in the staff directory</li>
                <li>Enable communication between staff members</li>
            </ul>

            <h3>3.2 Administrative Purposes</h3>
            <ul>
                <li>Maintain accurate staff records</li>
                <li>Generate reports and statistics for university administration</li>
                <li>Verify staff credentials and employment status</li>
                <li>Manage access permissions and roles</li>
                <li>Compile nominal rolls and staff lists</li>
            </ul>

            <h3>3.3 Communication</h3>
            <ul>
                <li>Send account verification emails</li>
                <li>Notify you of important updates or changes</li>
                <li>Respond to your inquiries and support requests</li>
                <li>Send password reset instructions</li>
            </ul>

            <h3>3.4 Security and Compliance</h3>
            <ul>
                <li>Detect and prevent unauthorized access</li>
                <li>Monitor for suspicious activity</li>
                <li>Comply with legal obligations and university policies</li>
                <li>Maintain audit trails for accountability</li>
            </ul>

            <h3>3.5 Improvement and Analytics</h3>
            <ul>
                <li>Analyze usage patterns to improve the Portal</li>
                <li>Identify and fix technical issues</li>
                <li>Develop new features based on user needs</li>
            </ul>

            <h2 class="section-title">4. Information Sharing and Disclosure</h2>
            
            <h3>4.1 Within the University</h3>
            <p>Your information may be shared with:</p>
            <ul>
                <li><strong>University Administration:</strong> For personnel management and administrative purposes</li>
                <li><strong>Department Heads:</strong> To manage departmental staff information</li>
                <li><strong>HR Department:</strong> For employment records and staff management</li>
                <li><strong>ICT Department:</strong> For technical support and system maintenance</li>
                <li><strong>Authorized Personnel:</strong> ID card managers, nominal role users (for specific functions)</li>
            </ul>

            <h3>4.2 Public Visibility</h3>
            <p>Based on your privacy settings:</p>
            <ul>
                <li><strong>Public Profiles:</strong> Visible to anyone accessing the Portal</li>
                <li><strong>University-Only Profiles:</strong> Visible only to authenticated TSU staff</li>
                <li><strong>Private Profiles:</strong> Display limited information (name, designation, TSU affiliation only)</li>
            </ul>

            <h3>4.3 Third Parties</h3>
            <p>We do NOT sell, rent, or trade your personal information to third parties. We may share information only when:</p>
            <ul>
                <li>Required by law or legal process</li>
                <li>Necessary to protect the rights, property, or safety of TSU, staff, or others</li>
                <li>With service providers who assist in Portal operations (under strict confidentiality agreements)</li>
            </ul>

            <h2 class="section-title">5. Data Security</h2>
            <p>We implement robust security measures to protect your information:</p>

            <h3>5.1 Technical Safeguards</h3>
            <ul>
                <li>Password encryption using industry-standard hashing (bcrypt)</li>
                <li>Secure HTTPS connections for all data transmission</li>
                <li>Regular security updates and patches</li>
                <li>Firewall protection and intrusion detection</li>
                <li>Secure file storage with access controls</li>
            </ul>

            <h3>5.2 Administrative Safeguards</h3>
            <ul>
                <li>Role-based access control (users, admins, managers)</li>
                <li>Activity logging and audit trails</li>
                <li>Regular security audits and assessments</li>
                <li>Staff training on data protection</li>
            </ul>

            <h3>5.3 Your Responsibilities</h3>
            <ul>
                <li>Keep your password confidential and secure</li>
                <li>Log out after using shared computers</li>
                <li>Report suspicious activity immediately</li>
                <li>Use strong, unique passwords</li>
            </ul>

            <h2 class="section-title">6. Your Privacy Rights and Choices</h2>
            
            <h3>6.1 Access and Control</h3>
            <p>You have the right to:</p>
            <ul>
                <li><strong>Access:</strong> View all personal information we hold about you</li>
                <li><strong>Update:</strong> Correct or update your profile information at any time</li>
                <li><strong>Delete:</strong> Request deletion of your account (subject to retention requirements)</li>
                <li><strong>Export:</strong> Download your profile data in a portable format</li>
            </ul>

            <h3>6.2 Privacy Settings</h3>
            <p>You can control:</p>
            <ul>
                <li>Profile visibility (Public, University-Only, Private)</li>
                <li>Contact preferences</li>
                <li>Which information appears on your public profile</li>
                <li>Whether to display years for education entries</li>
            </ul>

            <h3>6.3 Communication Preferences</h3>
            <ul>
                <li>You can opt out of non-essential communications</li>
                <li>Essential communications (security alerts, policy changes) cannot be disabled</li>
            </ul>

            <h2 class="section-title">7. Data Retention</h2>
            <p>We retain your information for as long as:</p>
            <ul>
                <li>Your account is active</li>
                <li>Necessary to provide services to you</li>
                <li>Required by law or university policy</li>
                <li>Needed for legitimate business purposes (e.g., dispute resolution)</li>
            </ul>
            <p>When you leave TSU or request account deletion, we may retain certain information for:</p>
            <ul>
                <li>Legal compliance (e.g., employment records)</li>
                <li>Audit purposes</li>
                <li>Historical records and archives</li>
            </ul>

            <h2 class="section-title">8. Cookies and Tracking Technologies</h2>
            <p>The Portal uses cookies and similar technologies to:</p>
            <ul>
                <li>Maintain your login session</li>
                <li>Remember your preferences</li>
                <li>Enhance security (CSRF protection)</li>
                <li>Analyze usage patterns</li>
            </ul>
            <p>You can control cookies through your browser settings, but disabling them may affect Portal functionality.</p>

            <h2 class="section-title">9. Children's Privacy</h2>
            <p>The Portal is intended for TSU staff members only and is not designed for individuals under 18 years of age. We do not knowingly collect information from minors.</p>

            <h2 class="section-title">10. International Data Transfers</h2>
            <p>Your information is stored on servers located in Nigeria. If you access the Portal from outside Nigeria, your information will be transferred to and processed in Nigeria in accordance with this Privacy Policy and Nigerian data protection laws.</p>

            <h2 class="section-title">11. Changes to This Privacy Policy</h2>
            <p>We may update this Privacy Policy periodically to reflect changes in our practices or legal requirements. We will notify you of significant changes by:</p>
            <ul>
                <li>Posting the updated policy on the Portal</li>
                <li>Updating the "Last Updated" date</li>
                <li>Sending email notifications for material changes</li>
            </ul>
            <p>Your continued use of the Portal after changes constitutes acceptance of the updated policy.</p>

            <h2 class="section-title">12. Contact Us</h2>
            <p>If you have questions, concerns, or requests regarding this Privacy Policy or your personal information, please contact:</p>
            <div class="alert alert-success">
                <strong>Data Protection Officer</strong><br>
                Taraba State University<br>
                ICT Department / Staff Portal Administration<br>
                Email: <a href="mailto:privacy@tsuniversity.edu.ng">privacy@tsuniversity.edu.ng</a><br>
                Alternative: <a href="mailto:ict@tsuniversity.edu.ng">ict@tsuniversity.edu.ng</a><br>
                Website: <a href="https://tsuniversity.edu.ng" target="_blank">www.tsuniversity.edu.ng</a>
            </div>

            <h2 class="section-title">13. Your Consent</h2>
            <p>By using the TSU Staff Profile Portal, you acknowledge that you have read and understood this Privacy Policy and consent to the collection, use, and disclosure of your information as described herein.</p>

            <hr class="my-5">

            <div class="text-center">
                <p class="text-muted mb-3">We are committed to protecting your privacy and handling your data responsibly.</p>
                <a href="<?= url() ?>" class="btn btn-success me-2">
                    <i class="fas fa-home me-2"></i>Return to Home
                </a>
                <a href="<?= url('terms') ?>" class="btn btn-outline-success">
                    <i class="fas fa-file-contract me-2"></i>Terms & Conditions
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
