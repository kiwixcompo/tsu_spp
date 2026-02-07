<?php
// Load helpers
if (!function_exists('url')) {
    require_once __DIR__ . '/../../Helpers/UrlHelper.php';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ID Card - <?= htmlspecialchars($profile['first_name'] . ' ' . $profile['last_name']) ?></title>
    <link rel="icon" type="image/png" href="<?= asset('assets/images/tsu-logo.png') ?>">
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background: #f0f0f0;
            padding: 20px;
        }
        .id-card-container {
            max-width: 1000px;
            margin: 0 auto;
        }
        .id-card {
            width: 350px;
            height: 550px;
            background: white;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.3);
            overflow: hidden;
            position: relative;
            margin: 20px auto;
        }
        .id-card-front {
            height: 100%;
            display: flex;
            flex-direction: column;
        }
        .id-card-header {
            background: linear-gradient(135deg, #1e40af 0%, #3b82f6 100%);
            color: white;
            padding: 20px;
            text-align: center;
        }
        .id-card-header img {
            width: 60px;
            height: 60px;
            margin-bottom: 10px;
        }
        .id-card-header h5 {
            font-size: 16px;
            font-weight: bold;
            margin: 0;
        }
        .id-card-header p {
            font-size: 11px;
            margin: 5px 0 0 0;
            opacity: 0.9;
        }
        .id-card-photo {
            text-align: center;
            padding: 15px 20px;
            background: #f8f9fa;
            flex-shrink: 0;
        }
        .id-card-photo img {
            width: 150px;
            height: 150px;
            object-fit: cover;
            border-radius: 10px;
            border: 4px solid white;
            box-shadow: 0 4px 8px rgba(0,0,0,0.2);
        }
        .id-card-photo .placeholder {
            width: 150px;
            height: 150px;
            background: #1e40af;
            color: white;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 48px;
            font-weight: bold;
            border-radius: 10px;
            border: 4px solid white;
            box-shadow: 0 4px 8px rgba(0,0,0,0.2);
        }
        .id-card-details {
            padding: 15px 20px;
            flex-grow: 1;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }
        .id-card-details h4 {
            font-size: 18px;
            font-weight: bold;
            color: #1e40af;
            margin-bottom: 5px;
            text-align: center;
            line-height: 1.3;
            word-wrap: break-word;
            overflow-wrap: break-word;
        }
        .id-card-details .designation {
            text-align: center;
            color: #666;
            font-size: 12px;
            margin-bottom: 12px;
            font-weight: 500;
            line-height: 1.4;
            word-wrap: break-word;
            overflow-wrap: break-word;
        }
        .id-card-info {
            font-size: 11px;
            line-height: 1.6;
        }
        .id-card-info .info-row {
            display: flex;
            margin-bottom: 6px;
            align-items: flex-start;
        }
        .id-card-info .info-label {
            font-weight: bold;
            color: #666;
            width: 85px;
            flex-shrink: 0;
        }
        .id-card-info .info-value {
            color: #333;
            flex-grow: 1;
            word-wrap: break-word;
            overflow-wrap: break-word;
            line-height: 1.4;
        }
        .id-card-footer {
            background: #1e40af;
            color: white;
            text-align: center;
            padding: 10px;
            font-size: 10px;
        }
        
        /* Back of ID Card */
        .id-card-back {
            height: 100%;
            display: flex;
            flex-direction: column;
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
        }
        .id-card-back-header {
            background: #1e40af;
            color: white;
            padding: 15px;
            text-align: center;
        }
        .id-card-back-content {
            flex-grow: 1;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: flex-start;
            padding: 25px 20px;
            overflow: hidden;
        }
        .qr-code-section {
            text-align: center;
            margin-bottom: 15px;
            flex-shrink: 0;
        }
        .qr-code-section img {
            width: 180px;
            height: 180px;
            border: 3px solid #1e40af;
            border-radius: 10px;
            background: white;
            padding: 8px;
        }
        .qr-code-section p {
            margin-top: 12px;
            font-size: 11px;
            color: #666;
            line-height: 1.4;
            padding: 0 10px;
        }
        .profile-url {
            font-size: 10px;
            color: #1e40af;
            word-break: break-all;
            margin-top: 8px;
            padding: 0 10px;
            line-height: 1.3;
        }
        
        /* Print Styles */
        @media print {
            body {
                background: white;
                padding: 0;
            }
            .no-print {
                display: none !important;
            }
            .id-card {
                box-shadow: none;
                page-break-after: always;
            }
        }
        
        /* Action Buttons */
        .action-buttons {
            text-align: center;
            margin: 20px 0;
        }
        .action-buttons .btn {
            margin: 0 5px;
        }
    </style>
</head>
<body>
    <div class="id-card-container">
        <!-- Action Buttons -->
        <div class="action-buttons no-print">
            <button onclick="window.print()" class="btn btn-primary btn-lg">
                <i class="fas fa-print me-2"></i>Print ID Card
            </button>
            <a href="<?= url('admin/id-cards') ?>" class="btn btn-secondary btn-lg">
                <i class="fas fa-arrow-left me-2"></i>Back to List
            </a>
            <button onclick="downloadIDCard()" class="btn btn-success btn-lg">
                <i class="fas fa-download me-2"></i>Download
            </button>
        </div>

        <div class="row">
            <!-- Front of ID Card -->
            <div class="col-md-6">
                <h5 class="text-center mb-3 no-print">Front</h5>
                <div class="id-card" id="id-card-front">
                    <div class="id-card-front">
                        <!-- Header -->
                        <div class="id-card-header">
                            <img src="<?= asset('assets/images/tsu-logo.png') ?>" alt="TSU Logo">
                            <h5>TARABA STATE UNIVERSITY</h5>
                            <p>STAFF IDENTIFICATION CARD</p>
                        </div>
                        
                        <!-- Photo -->
                        <div class="id-card-photo">
                            <?php if (!empty($profile['profile_photo'])): ?>
                                <?php
                                // Try multiple possible paths for profile photo
                                $photoPath = $profile['profile_photo'];
                                if (strpos($photoPath, 'uploads/') === 0 || strpos($photoPath, '/uploads/') === 0) {
                                    // Full path already in database
                                    $photoUrl = url($photoPath);
                                } else {
                                    // Just filename, try common paths
                                    $photoUrl = asset('uploads/profiles/' . $photoPath);
                                }
                                ?>
                                <img src="<?= $photoUrl ?>" alt="Photo" onerror="this.parentElement.innerHTML='<div class=\'placeholder\'><?= strtoupper(substr($profile['first_name'], 0, 1) . substr($profile['last_name'], 0, 1)) ?></div>'">
                            <?php else: ?>
                                <div class="placeholder">
                                    <?= strtoupper(substr($profile['first_name'], 0, 1) . substr($profile['last_name'], 0, 1)) ?>
                                </div>
                            <?php endif; ?>
                        </div>
                        
                        <!-- Details -->
                        <div class="id-card-details">
                            <?php
                            // Decode HTML entities for proper display
                            $title = html_entity_decode($profile['title'] ?? '', ENT_QUOTES | ENT_HTML5, 'UTF-8');
                            $firstName = html_entity_decode($profile['first_name'] ?? '', ENT_QUOTES | ENT_HTML5, 'UTF-8');
                            $middleName = html_entity_decode($profile['middle_name'] ?? '', ENT_QUOTES | ENT_HTML5, 'UTF-8');
                            $lastName = html_entity_decode($profile['last_name'] ?? '', ENT_QUOTES | ENT_HTML5, 'UTF-8');
                            $designation = html_entity_decode($profile['designation'] ?? '', ENT_QUOTES | ENT_HTML5, 'UTF-8');
                            $faculty = html_entity_decode($profile['faculty'] ?? '', ENT_QUOTES | ENT_HTML5, 'UTF-8');
                            $department = html_entity_decode($profile['department'] ?? '', ENT_QUOTES | ENT_HTML5, 'UTF-8');
                            $staffNumber = html_entity_decode($profile['staff_number'] ?? '', ENT_QUOTES | ENT_HTML5, 'UTF-8');
                            $email = html_entity_decode($profile['email'] ?? '', ENT_QUOTES | ENT_HTML5, 'UTF-8');
                            
                            $fullName = $title . ' ' . $firstName;
                            if (!empty($middleName)) {
                                $fullName .= ' ' . $middleName;
                            }
                            $fullName .= ' ' . $lastName;
                            ?>
                            <h4><?= htmlspecialchars($fullName, ENT_QUOTES, 'UTF-8') ?></h4>
                            <div class="designation"><?= htmlspecialchars($designation, ENT_QUOTES, 'UTF-8') ?></div>
                            
                            <div class="id-card-info">
                                <div class="info-row">
                                    <div class="info-label">Staff ID:</div>
                                    <div class="info-value"><?= !empty($staffNumber) ? htmlspecialchars($staffNumber, ENT_QUOTES, 'UTF-8') : 'TSU-' . str_pad($profile['id'], 5, '0', STR_PAD_LEFT) ?></div>
                                </div>
                                <div class="info-row">
                                    <div class="info-label">Faculty:</div>
                                    <div class="info-value"><?= htmlspecialchars($faculty, ENT_QUOTES, 'UTF-8') ?></div>
                                </div>
                                <div class="info-row">
                                    <div class="info-label">Department:</div>
                                    <div class="info-value"><?= htmlspecialchars($department, ENT_QUOTES, 'UTF-8') ?></div>
                                </div>
                                <div class="info-row">
                                    <div class="info-label">Email:</div>
                                    <div class="info-value" style="font-size: 10px;"><?= htmlspecialchars($email, ENT_QUOTES, 'UTF-8') ?></div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Footer -->
                        <div class="id-card-footer">
                            Issued: <?= date('F Y') ?>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Back of ID Card -->
            <div class="col-md-6">
                <h5 class="text-center mb-3 no-print">Back</h5>
                <div class="id-card" id="id-card-back">
                    <div class="id-card-back">
                        <!-- Header -->
                        <div class="id-card-back-header">
                            <h6 class="mb-0">SCAN FOR PROFILE</h6>
                        </div>
                        
                        <!-- QR Code -->
                        <div class="id-card-back-content">
                            <div class="qr-code-section">
                                <?php if (!empty($qr_code_url)): ?>
                                    <img src="<?= $qr_code_url ?>" alt="QR Code">
                                    <p><strong>Scan this QR code</strong><br>to view real-time profile</p>
                                    <div class="profile-url">
                                        <?= url('profile/' . $profile['profile_slug']) ?>
                                    </div>
                                <?php else: ?>
                                    <div class="alert alert-warning">
                                        QR Code not generated yet
                                    </div>
                                <?php endif; ?>
                            </div>
                            
                            <div style="margin-top: auto; text-align: center; font-size: 10px; color: #666; padding-top: 15px;">
                                <p style="margin: 3px 0;"><strong>Important:</strong></p>
                                <p style="margin: 3px 0;">This card is property of TSU</p>
                                <p style="margin: 3px 0;">If found, please return to:</p>
                                <p style="margin: 3px 0;"><strong>Security Unit</strong></p>
                                <p style="margin: 3px 0;">Taraba State University, Jalingo</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Instructions -->
        <div class="alert alert-info no-print mt-4">
            <h6><i class="fas fa-info-circle me-2"></i>Printing Instructions:</h6>
            <ul class="mb-0">
                <li>Use high-quality card stock paper (300gsm recommended)</li>
                <li>Print in color for best results</li>
                <li>ID card size: 3.5" x 5.5" (standard ID card size)</li>
                <li>Consider laminating for durability</li>
                <li>Print front and back separately, then bind together</li>
            </ul>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
    <script>
        async function downloadIDCard() {
            const { jsPDF } = window.jspdf;
            const pdf = new jsPDF({
                orientation: 'portrait',
                unit: 'in',
                format: [3.5, 5.5]
            });

            try {
                // Capture front
                const frontCard = document.getElementById('id-card-front');
                const frontCanvas = await html2canvas(frontCard, {
                    scale: 3,
                    useCORS: true,
                    allowTaint: true,
                    backgroundColor: '#ffffff'
                });
                
                const frontImgData = frontCanvas.toDataURL('image/png');
                pdf.addImage(frontImgData, 'PNG', 0, 0, 3.5, 5.5);
                
                // Add new page for back
                pdf.addPage();
                
                // Capture back
                const backCard = document.getElementById('id-card-back');
                const backCanvas = await html2canvas(backCard, {
                    scale: 3,
                    useCORS: true,
                    allowTaint: true,
                    backgroundColor: '#f8f9fa'
                });
                
                const backImgData = backCanvas.toDataURL('image/png');
                pdf.addImage(backImgData, 'PNG', 0, 0, 3.5, 5.5);
                
                // Save PDF
                const staffName = '<?= htmlspecialchars($profile['first_name'] . '_' . $profile['last_name']) ?>';
                pdf.save(`ID_Card_${staffName}.pdf`);
            } catch (error) {
                console.error('Error generating PDF:', error);
                alert('Error generating PDF. Please use Print and save as PDF instead.');
            }
        }
    </script>
</body>
</html>
