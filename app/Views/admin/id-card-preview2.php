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
        
        /* Front of ID Card - New Design */
        .id-card-front {
            height: 100%;
            display: flex;
            flex-direction: column;
            background-image: url('data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 350 550"><defs><linearGradient id="bg" x1="0%" y1="0%" x2="0%" y2="100%"><stop offset="0%" style="stop-color:%23e8eef5;stop-opacity:1" /><stop offset="100%" style="stop-color:%23f5f8fc;stop-opacity:1" /></linearGradient></defs><rect fill="url(%23bg)" width="350" height="550"/></svg>');
            background-size: cover;
            position: relative;
        }
        
        /* Building background overlay */
        .id-card-front::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-image: url('<?= asset('assets/images/tsu-building.jpg') ?>');
            background-size: cover;
            background-position: center;
            opacity: 0.25;
            z-index: 0;
        }
        
        .id-card-front::after {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(to bottom, rgba(255,255,255,0.75) 0%, rgba(255,255,255,0.85) 40%, rgba(255,255,255,0.90) 100%);
            z-index: 1;
        }
        
        .id-card-front > * {
            position: relative;
            z-index: 2;
        }
        
        /* Vertical Staff ID Card Text */
        .vertical-text {
            position: absolute;
            left: 0;
            top: 320px;
            height: 170px;
            width: 50px;
            background: #1e3a8a;
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 3;
        }
        
        .vertical-text span {
            writing-mode: vertical-lr;
            transform: rotate(180deg);
            color: white;
            font-weight: bold;
            font-size: 13px;
            letter-spacing: 2px;
            text-transform: uppercase;
            white-space: nowrap;
        }
        
        /* Header with Logo */
        .id-card-header-new {
            padding: 20px 20px 15px;
            text-align: center;
            background: transparent;
        }
        
        .id-card-header-new img {
            width: 80px;
            height: 80px;
            margin-bottom: 10px;
        }
        
        .id-card-header-new h5 {
            font-size: 18px;
            font-weight: bold;
            color: #1e3a8a;
            margin: 0 0 3px 0;
            letter-spacing: 0.5px;
        }
        
        .id-card-header-new .subtitle {
            font-size: 14px;
            color: #1e3a8a;
            font-weight: 600;
            margin: 0;
            border-top: 2px solid #1e3a8a;
            border-bottom: 2px solid #1e3a8a;
            padding: 3px 0;
            display: inline-block;
        }
        
        /* Photo Section */
        .id-card-photo-new {
            text-align: center;
            padding: 15px 60px 15px 20px;
        }
        
        .id-card-photo-new img {
            width: 180px;
            height: 220px;
            object-fit: cover;
            border-radius: 8px;
            border: 4px solid #1e3a8a;
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        }
        
        .id-card-photo-new .placeholder {
            width: 180px;
            height: 220px;
            background: #1e3a8a;
            color: white;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 60px;
            font-weight: bold;
            border-radius: 8px;
            border: 4px solid #1e3a8a;
        }
        
        /* Name and Details */
        .id-card-name-section {
            padding: 10px 60px 10px 20px;
            text-align: center;
        }
        
        .id-card-name-section h4 {
            font-size: 20px;
            font-weight: bold;
            color: #1e3a8a;
            margin: 0 0 5px 0;
            line-height: 1.2;
        }
        
        .id-card-name-section .designation {
            font-size: 13px;
            color: #374151;
            margin-bottom: 15px;
            font-weight: 500;
        }
        
        /* Info Section */
        .id-card-info-new {
            padding: 0 60px 15px 20px;
            font-size: 13px;
        }
        
        .id-card-info-new .info-row {
            display: flex;
            margin-bottom: 8px;
            align-items: flex-start;
            background: rgba(255,255,255,0.85);
            padding: 8px 12px;
            border-radius: 4px;
            border-left: 3px solid #1e3a8a;
        }
        
        .id-card-info-new .info-label {
            font-weight: bold;
            color: #1e3a8a;
            min-width: 80px;
            flex-shrink: 0;
        }
        
        .id-card-info-new .info-value {
            color: #1f2937;
            flex-grow: 1;
            word-wrap: break-word;
            font-weight: 500;
        }
        
        /* Footer */
        .id-card-footer-new {
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            background: #1e3a8a;
            color: white;
            text-align: center;
            padding: 12px;
            font-size: 11px;
            font-weight: 500;
        }
        
        /* Back of ID Card with TSU Watermark */
        .id-card-back {
            height: 100%;
            display: flex;
            flex-direction: column;
            background: linear-gradient(135deg, #e8eef5 0%, #f5f8fc 100%);
            position: relative;
            overflow: hidden;
        }
        
        /* TSU Watermark */
        .tsu-watermark {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            font-size: 180px;
            font-weight: 900;
            color: rgba(30, 58, 138, 0.08);
            font-family: 'Arial Black', sans-serif;
            letter-spacing: -10px;
            z-index: 1;
            user-select: none;
            pointer-events: none;
        }
        
        .id-card-back > * {
            position: relative;
            z-index: 2;
        }
        
        .id-card-back-header {
            background: #1e3a8a;
            color: white;
            padding: 15px;
            text-align: center;
        }
        
        .id-card-back-header h6 {
            margin: 0;
            font-size: 14px;
            font-weight: bold;
            letter-spacing: 1px;
        }
        
        .id-card-back-content {
            flex-grow: 1;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: flex-start;
            padding: 25px 20px 20px;
        }
        
        .qr-code-section {
            text-align: center;
            flex-shrink: 0;
            margin-bottom: auto;
        }
        
        .qr-code-section img {
            width: 180px;
            height: 180px;
            border: 4px solid #1e3a8a;
            border-radius: 12px;
            background: white;
            padding: 10px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        }
        
        .qr-code-section p {
            margin-top: 12px;
            font-size: 11px;
            color: #374151;
            font-weight: 500;
        }
        
        .blood-group-section {
            text-align: center;
            margin-top: 15px;
            margin-bottom: 15px;
            padding: 12px 20px;
            background: rgba(255,255,255,0.8);
            border-radius: 8px;
            border: 2px solid #1e3a8a;
            width: 100%;
            max-width: 200px;
        }
        
        .blood-group-section .label {
            font-size: 10px;
            color: #6b7280;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .blood-group-section .value {
            font-size: 24px;
            color: #1e3a8a;
            font-weight: bold;
            margin-top: 3px;
        }
        
        .back-footer-info {
            text-align: center;
            font-size: 10px;
            color: #1f2937;
            padding: 15px;
            background: rgba(255,255,255,0.75);
            border-radius: 8px;
            width: 100%;
            max-width: 280px;
            margin-top: auto;
        }
        
        .back-footer-info p {
            margin: 4px 0;
            line-height: 1.4;
        }
        
        .back-footer-info strong {
            color: #1e3a8a;
            font-weight: 700;
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
                        <!-- Vertical Staff ID Card Text -->
                        <div class="vertical-text">
                            <span>STAFF ID CARD</span>
                        </div>
                        
                        <!-- Header with Logo -->
                        <div class="id-card-header-new">
                            <img src="<?= asset('assets/images/tsu-logo.png') ?>" alt="TSU Logo">
                            <h5>TARABA STATE UNIVERSITY</h5>
                            <div class="subtitle">JALINGO</div>
                        </div>
                        
                        <!-- Photo -->
                        <div class="id-card-photo-new">
                            <?php if (!empty($profile['profile_photo'])): ?>
                                <?php
                                $photoPath = $profile['profile_photo'];
                                if (strpos($photoPath, 'uploads/') === 0 || strpos($photoPath, '/uploads/') === 0) {
                                    $photoUrl = url($photoPath);
                                } else {
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
                        
                        <!-- Name and Designation -->
                        <div class="id-card-name-section">
                            <?php
                            $title = html_entity_decode($profile['title'] ?? '', ENT_QUOTES | ENT_HTML5, 'UTF-8');
                            $firstName = html_entity_decode($profile['first_name'] ?? '', ENT_QUOTES | ENT_HTML5, 'UTF-8');
                            $middleName = html_entity_decode($profile['middle_name'] ?? '', ENT_QUOTES | ENT_HTML5, 'UTF-8');
                            $lastName = html_entity_decode($profile['last_name'] ?? '', ENT_QUOTES | ENT_HTML5, 'UTF-8');
                            $designation = html_entity_decode($profile['designation'] ?? '', ENT_QUOTES | ENT_HTML5, 'UTF-8');
                            $faculty = html_entity_decode($profile['faculty'] ?? '', ENT_QUOTES | ENT_HTML5, 'UTF-8');
                            $department = html_entity_decode($profile['department'] ?? '', ENT_QUOTES | ENT_HTML5, 'UTF-8');
                            $staffNumber = html_entity_decode($profile['staff_number'] ?? '', ENT_QUOTES | ENT_HTML5, 'UTF-8');
                            
                            $fullName = $title . ' ' . $firstName;
                            if (!empty($middleName)) {
                                $fullName .= ' ' . $middleName;
                            }
                            $fullName .= ' ' . $lastName;
                            ?>
                            <h4><?= htmlspecialchars($fullName, ENT_QUOTES, 'UTF-8') ?></h4>
                            <div class="designation"><?= htmlspecialchars($designation, ENT_QUOTES, 'UTF-8') ?></div>
                        </div>
                        
                        <!-- Info Section -->
                        <div class="id-card-info-new">
                            <div class="info-row">
                                <div class="info-label">Staff ID:</div>
                                <div class="info-value"><?= !empty($staffNumber) ? htmlspecialchars($staffNumber, ENT_QUOTES, 'UTF-8') : 'TSU-' . str_pad($profile['id'], 5, '0', STR_PAD_LEFT) ?></div>
                            </div>
                            <div class="info-row">
                                <div class="info-label">Faculty:</div>
                                <div class="info-value"><?= htmlspecialchars($faculty, ENT_QUOTES, 'UTF-8') ?></div>
                            </div>
                            <div class="info-row">
                                <div class="info-label">Dept:</div>
                                <div class="info-value"><?= htmlspecialchars($department, ENT_QUOTES, 'UTF-8') ?></div>
                            </div>
                        </div>
                        
                        <!-- Footer -->
                        <div class="id-card-footer-new">
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
                        <!-- TSU Watermark -->
                        <div class="tsu-watermark">TSU</div>
                        
                        <!-- Header -->
                        <div class="id-card-back-header">
                            <h6>SCAN FOR PROFILE</h6>
                        </div>
                        
                        <!-- QR Code, Blood Group and Footer -->
                        <div class="id-card-back-content">
                            <div class="qr-code-section">
                                <?php if (!empty($qr_code_url)): ?>
                                    <img src="<?= $qr_code_url ?>" alt="QR Code">
                                    <p><strong>Scan to view profile</strong></p>
                                <?php else: ?>
                                    <div class="alert alert-warning">
                                        QR Code not generated yet
                                    </div>
                                <?php endif; ?>
                            </div>
                            
                            <?php if (!empty($profile['blood_group'])): ?>
                            <div class="blood-group-section">
                                <div class="label">Blood Group</div>
                                <div class="value"><?= htmlspecialchars($profile['blood_group'], ENT_QUOTES, 'UTF-8') ?></div>
                            </div>
                            <?php endif; ?>
                            
                            <div class="back-footer-info">
                                <p><strong>IMPORTANT</strong></p>
                                <p>This card is property of TSU</p>
                                <p>If found, please return to:</p>
                                <p><strong>Security Unit</strong></p>
                                <p>Taraba State University, Jalingo</p>
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
