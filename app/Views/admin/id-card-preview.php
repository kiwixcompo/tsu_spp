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
    <title>ID Card - <?= htmlspecialchars(trim(($profile['first_name'] ?? '') . ' ' . ($profile['middle_name'] ?? '') . ' ' . ($profile['last_name'] ?? ''))) ?></title>
    <link rel="icon" type="image/png" href="<?= asset('assets/images/tsu-logo.png') ?>">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        body { background: #f0f0f0; padding: 20px; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
        .id-card-container { max-width: 1000px; margin: 0 auto; }
        
        /* === CARD CONTAINER === */
        .id-card {
            width: 350px;
            height: 550px;
            background: white;
            border-radius: 12px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
            overflow: hidden;
            position: relative;
            margin: 20px auto;
        }
        
        /* === FRONT DESIGN === */
        .id-card-front {
            height: 100%;
            position: relative;
            background-color: #f8f9fa;
        }
        
        /* Background Image */
        .id-card-front::before {
            content: '';
            position: absolute;
            top: 0; left: 0; right: 0; bottom: 0;
            background-image: url('<?= asset('assets/images/tsu-building.jpg') ?>');
            background-size: cover;
            background-position: center;
            opacity: 0.15;
            z-index: 0;
        }
        
        /* Header Section - COMPACTED */
        .header-section {
            text-align: center;
            padding-top: 15px; /* Reduced from 25px */
            position: relative;
            z-index: 2;
        }
        
        .header-logo {
            width: 60px; /* Reduced from 70px */
            height: 60px;
            margin-bottom: 3px;
        }
        
        .uni-name {
            color: #1e40af;
            font-weight: 800;
            font-size: 15px; /* Slightly smaller */
            text-transform: uppercase;
            margin: 0;
            line-height: 1.1;
        }
        
        .uni-location {
            display: inline-block;
            color: #1e40af;
            font-weight: 600;
            font-size: 12px;
            text-transform: uppercase;
            border-top: 1px solid #1e40af;
            border-bottom: 1px solid #1e40af;
            padding: 1px 8px;
            margin-top: 2px;
        }
        
        /* Photo Section - COMPACTED */
        .photo-section {
            text-align: center;
            margin-top: 10px; /* Reduced margin */
            position: relative;
            z-index: 2;
            height: 170px; /* Reduced height container */
            display: flex;
            justify-content: center;
            align-items: center;
        }
        
        .profile-photo {
            width: 140px; /* Slightly smaller photo */
            height: 165px;
            object-fit: cover;
            border-radius: 8px;
            border: 3px solid #1e40af;
            box-shadow: 0 3px 6px rgba(0,0,0,0.15);
            background: #fff;
        }
        
        .photo-placeholder {
            width: 140px;
            height: 165px;
            background: #e2e8f0;
            color: #64748b;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 50px;
            font-weight: bold;
            border-radius: 8px;
            border: 3px solid #1e40af;
        }

        /* Vertical Blue Bar (Left) - Adjusted Position */
        .vertical-bar {
            position: absolute;
            left: 15px;
            bottom: 55px; /* Above footer */
            top: 200px; /* Adjusted to match new photo position */
            width: 35px; /* Slightly thinner */
            background-color: #1e40af;
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 4px 4px 0 0;
            z-index: 2;
            box-shadow: 2px 0 5px rgba(0,0,0,0.1);
        }
        
        .vertical-text {
            writing-mode: vertical-lr; /* Changed from vertical-rl to vertical-lr */
            transform: none; /* Removed rotation */
            font-weight: 700;
            letter-spacing: 2px;
            text-transform: uppercase;
            font-size: 13px;
            white-space: nowrap;
            padding: 10px 0;
            color: white; /* Ensure text is white for visibility */
        }
        
        /* Name Section */
        .name-section {
            text-align: center;
            margin-top: 10px;
            position: relative;
            z-index: 2;
            padding: 0 10px;
        }
        
        .full-name {
            color: #1e3a8a;
            font-weight: 800;
            font-size: 20px;
            margin: 0;
            line-height: 1.1;
        }
        
        .designation {
            color: #4b5563;
            font-size: 13px;
            font-weight: 600;
            margin-top: 3px;
        }
        
        /* Details Table - Optimized for space */
        .details-section {
            margin-top: 12px;
            margin-left: 65px; /* Clear blue bar */
            margin-right: 10px;
            margin-bottom: 15px;
            position: relative;
            z-index: 2;
            font-size: 13px;
        }
        
        .details-table {
            width: 100%;
            border-collapse: collapse;
        }
        
        .details-table td {
            vertical-align: top;
            padding-bottom: 6px;
        }
        
        .details-label {
            font-weight: 700;
            color: #1e40af;
            width: 60px;
            white-space: nowrap;
        }
        
        .details-value {
            color: #111;
            font-weight: 600;
            line-height: 1.3;
        }
        
        /* Footer */
        .card-footer {
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            height: 40px; /* Slightly shorter footer */
            background: #1e40af;
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 10px;
            font-weight: 500;
            z-index: 2;
        }
        
        /* === BACK DESIGN === */
        .id-card-back {
            height: 100%;
            position: relative;
            background: #fff;
            display: flex;
            flex-direction: column;
            overflow: hidden;
        }
        
        .tsu-watermark {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%) rotate(-30deg);
            font-size: 120px;
            font-weight: 900;
            color: rgba(30, 64, 175, 0.05);
            font-family: Arial, sans-serif;
            z-index: 0;
            pointer-events: none;
            user-select: none;
        }
        
        .back-content {
            position: relative;
            z-index: 2;
            flex: 1;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: flex-start; /* Changed from center to flex-start */
            padding: 30px 20px 20px; /* Increased top padding */
            text-align: center;
        }
        
        .qr-container {
            margin-top: 20px; /* Added top margin to push down */
            margin-bottom: 20px; /* Increased bottom margin */
            text-align: center;
        }
        
        .scan-instruction {
            font-size: 12px;
            color: #1e40af;
            font-weight: 700;
            margin-bottom: 8px; /* Increased spacing */
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .qr-code {
            width: 180px;
            height: 180px;
            border: 4px solid #1e3a8a;
            border-radius: 10px;
            padding: 4px;
            background: white;
            box-shadow: 0 4px 10px rgba(0,0,0,0.1);
        }
        
        .blood-group-box {
            border: 2px solid #dc2626;
            border-radius: 8px;
            padding: 4px 20px;
            margin-bottom: 20px; /* Increased spacing */
            background: rgba(255,255,255,0.95);
            min-width: 120px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.05);
        }
        
        .bg-label {
            font-size: 10px;
            text-transform: uppercase;
            color: #dc2626;
            font-weight: 700;
            letter-spacing: 1px;
        }
        
        .bg-value {
            font-size: 24px;
            font-weight: 900;
            color: #333;
            line-height: 1.1;
        }
        
        .return-info {
            font-size: 10px;
            color: #4b5563;
            line-height: 1.4;
            margin-top: auto;
            margin-bottom: 5px;
        }
        
        .return-info strong {
            color: #1e40af;
            display: block;
            font-size: 12px;
            margin: 2px 0;
        }
        
        /* Print Styles */
        @media print {
            body { background: white; padding: 0; }
            .no-print { display: none !important; }
            .id-card { box-shadow: none; margin: 0; page-break-after: always; }
        }
        
        .action-buttons { text-align: center; margin: 20px 0; }
        .action-buttons .btn { margin: 0 5px; }
    </style>
</head>
<body>
    <div class="id-card-container">
        <div class="action-buttons no-print">
            <button onclick="window.print()" class="btn btn-primary btn-lg">
                <i class="fas fa-print me-2"></i>Print ID Card
            </button>
            <a href="<?= url('admin/users') ?>" class="btn btn-secondary btn-lg">
                <i class="fas fa-arrow-left me-2"></i>Back to List
            </a>
            <button onclick="downloadIDCard()" class="btn btn-success btn-lg">
                <i class="fas fa-download me-2"></i>Download PDF
            </button>
        </div>

        <div class="row justify-content-center">
            <div class="col-md-6 d-flex justify-content-center">
                <div>
                    <h5 class="text-center mb-3 no-print">Front</h5>
                    <div class="id-card" id="id-card-front">
                        <div class="id-card-front">
                            <div class="vertical-bar">
                                <div class="vertical-text">STAFF ID CARD</div>
                            </div>

                            <div class="header-section">
                                <img src="<?= asset('assets/images/tsu-logo.png') ?>" alt="Logo" class="header-logo">
                                <h2 class="uni-name">TARABA STATE UNIVERSITY</h2>
                                <div class="uni-location">JALINGO</div>
                            </div>

                            <div class="photo-section">
                                <?php 
                                    // Image Path Logic for public/uploads/profiles/
                                    $photoUrl = null;
                                    if (!empty($profile['profile_photo'])) {
                                        $photoPath = $profile['profile_photo'];
                                        
                                        // Check if it's already a full URL
                                        if (filter_var($photoPath, FILTER_VALIDATE_URL)) {
                                            $photoUrl = $photoPath;
                                        } 
                                        // Check if path starts with uploads/ or /uploads/
                                        elseif (strpos($photoPath, 'uploads/') === 0 || strpos($photoPath, '/uploads/') === 0) {
                                            $photoUrl = url(ltrim($photoPath, '/'));
                                        }
                                        // Otherwise assume it's just the filename in uploads/profiles/
                                        else {
                                            $photoUrl = url('uploads/profiles/' . basename($photoPath));
                                        }
                                    }
                                ?>
                                
                                <?php if ($photoUrl): ?>
                                    <img src="<?= $photoUrl ?>" class="profile-photo" alt="Photo" onerror="this.style.display='none';this.nextElementSibling.style.display='flex';">
                                    <div class="photo-placeholder" style="display:none;">
                                        <?= strtoupper(substr($profile['first_name'], 0, 1) . substr($profile['last_name'], 0, 1)) ?>
                                    </div>
                                <?php else: ?>
                                    <div class="photo-placeholder">
                                        <?= strtoupper(substr($profile['first_name'], 0, 1) . substr($profile['last_name'], 0, 1)) ?>
                                    </div>
                                <?php endif; ?>
                            </div>

                            <div class="name-section">
                                <h3 class="full-name">
                                    <?php
                                    $nameParts = array_filter([
                                        $profile['title'] ?? '',
                                        $profile['first_name'] ?? '',
                                        $profile['middle_name'] ?? '',
                                        $profile['last_name'] ?? ''
                                    ]);
                                    $fullName = implode(' ', $nameParts);
                                    echo htmlspecialchars(trim($fullName));
                                    ?>
                                </h3>
                                <div class="designation"><?= htmlspecialchars($profile['designation'] ?? '') ?></div>
                            </div>

                            <div class="details-section">
                                <table class="details-table">
                                    <tr>
                                        <td class="details-label">Staff ID:</td>
                                        <td class="details-value"><?= htmlspecialchars($profile['staff_number'] ?? 'N/A') ?></td>
                                    </tr>
                                    <tr>
                                        <td class="details-label">Faculty:</td>
                                        <td class="details-value"><?= htmlspecialchars($profile['faculty'] ?? '') ?></td>
                                    </tr>
                                    <tr>
                                        <td class="details-label">Dept:</td>
                                        <td class="details-value"><?= htmlspecialchars($profile['department'] ?? '') ?></td>
                                    </tr>
                                </table>
                            </div>

                            <div class="card-footer">
                                Issued: <?= date('F Y') ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-md-6 d-flex justify-content-center">
                <div>
                    <h5 class="text-center mb-3 no-print">Back</h5>
                    <div class="id-card" id="id-card-back">
                        <div class="id-card-back">
                            <div class="tsu-watermark">TSU</div>
                            
                            <div class="back-content">
                                <div class="qr-container">
                                    <div class="scan-instruction">Scan to view online profile</div>
                                    <?php if (!empty($qr_code_url)): ?>
                                        <img src="<?= $qr_code_url ?>" class="qr-code" alt="QR Code">
                                    <?php else: ?>
                                        <div class="qr-code d-flex align-items-center justify-content-center text-muted small">
                                            QR Code
                                        </div>
                                    <?php endif; ?>
                                </div>

                                <div class="blood-group-box">
                                    <div class="bg-label">Blood Group</div>
                                    <div class="bg-value">
                                        <?= !empty($profile['blood_group']) ? htmlspecialchars($profile['blood_group']) : '<span style="color:#999;font-size:16px;">Not Added</span>' ?>
                                    </div>
                                </div>

                                <div class="return-info">
                                    <p style="margin: 0;">If found, please return to:</p>
                                    <strong>SECURITY UNIT</strong>
                                    <p style="margin: 0;">Taraba State University<br>Jalingo, Nigeria</p>
                                </div>
                            </div>
                            
                            <div class="card-footer">
                                Property of Taraba State University
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="alert alert-info no-print mt-4">
            <h6><i class="fas fa-info-circle me-2"></i>Printing Instructions:</h6>
            <ul class="mb-0">
                <li>Use standard ID card size (CR80 dimensions: 85.6mm x 54mm).</li>
                <li>Print in color for best results.</li>
                <li>Verify both sides align correctly before bulk printing.</li>
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
                unit: 'mm',
                format: [54, 85.6] // Standard card size
            });

            const captureCard = async (elementId) => {
                const element = document.getElementById(elementId);
                const canvas = await html2canvas(element, {
                    scale: 4, 
                    useCORS: true,
                    allowTaint: true,
                    backgroundColor: '#ffffff'
                });
                return canvas.toDataURL('image/jpeg', 1.0);
            };

            try {
                // Front
                const frontImg = await captureCard('id-card-front');
                pdf.addImage(frontImg, 'JPEG', 0, 0, 54, 85.6);
                
                // Back
                pdf.addPage();
                const backImg = await captureCard('id-card-back');
                pdf.addImage(backImg, 'JPEG', 0, 0, 54, 85.6);
                
                // Save
                const staffName = '<?= htmlspecialchars(trim(($profile['first_name'] ?? '') . '_' . ($profile['middle_name'] ?? '') . '_' . ($profile['last_name'] ?? ''))) ?>';
                pdf.save(`ID_Card_${staffName}.pdf`);
            } catch (error) {
                console.error('Error generating PDF:', error);
                alert('Error generating PDF. Please check console for details.');
            }
        }
    </script>
</body>
</html>