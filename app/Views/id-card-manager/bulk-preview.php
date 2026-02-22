<?php
// Load URL helper
if (!function_exists('url')) {
    require_once __DIR__ . '/../../Helpers/UrlHelper.php';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bulk ID Card Preview - TSU Staff Portal</title>
    <link rel="icon" type="image/png" href="<?= asset('assets/images/tsu-logo.png') ?>">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        body { overflow-x: hidden; background: #f0f0f0; padding: 20px; }
        .action-buttons { text-align: center; margin: 20px 0; position: sticky; top: 0; background: white; padding: 15px; z-index: 1000; box-shadow: 0 2px 10px rgba(0,0,0,0.1); border-radius: 8px; }
        .action-buttons .btn { margin: 0 5px; }
        .cards-container { display: flex; flex-wrap: wrap; gap: 30px; justify-content: center; margin-top: 20px; }
        .card-pair { display: flex; gap: 20px; }
        @media print {
            body { background: white; padding: 0; }
            .no-print { display: none !important; }
            .card-pair { page-break-after: always; margin: 0; }
        }
    </style>
</head>
<body>
    <div class="action-buttons no-print">
        <h4 class="mb-3"><i class="fas fa-id-card me-2"></i>Bulk ID Card Preview (<?= count($profiles) ?> Cards)</h4>
        <button onclick="window.print()" class="btn btn-primary btn-lg">
            <i class="fas fa-print me-2"></i>Print All Cards
        </button>
        <button onclick="downloadAllPDFs()" class="btn btn-success btn-lg">
            <i class="fas fa-download me-2"></i>Download All as ZIP
        </button>
        <a href="<?= url('id-card-manager/browse') ?>" class="btn btn-secondary btn-lg">
            <i class="fas fa-arrow-left me-2"></i>Back to Browse
        </a>
    </div>

    <div class="cards-container">
        <?php foreach ($profiles as $profile): ?>
        <div class="card-pair">
            <!-- Front -->
            <div class="id-card" style="width: 350px; height: 550px; background: white; border-radius: 12px; box-shadow: 0 10px 30px rgba(0,0,0,0.2); overflow: hidden; position: relative;">
                <div style="height: 100%; position: relative; background-color: #f8f9fa;">
                    <div style="position: absolute; top:0; left:0; width:100%; height:100%; background-image: url('<?= asset('assets/images/tsu-building.jpg') ?>'); background-size: cover; background-position: center; opacity: 0.15; z-index: 0;"></div>
                    
                    <div style="position: absolute; left: 20px; bottom: 40px; height: 180px; width: 40px; background: #1e40af; color: white; display: flex; align-items: center; justify-content: center; border-radius: 8px 8px 0 0; z-index: 3; box-shadow: 2px -2px 5px rgba(0,0,0,0.1);">
                        <div style="transform: rotate(-90deg); white-space: nowrap; font-weight: 800; letter-spacing: 2px; text-transform: uppercase; font-size: 13px;">STAFF ID CARD</div>
                    </div>

                    <div style="text-align: center; padding-top: 20px; position: relative; z-index: 2;">
                        <img src="<?= asset('assets/images/tsu-logo.png') ?>" style="width: 65px; height: 65px; margin-bottom: 3px;">
                        <h2 style="color: #1e40af; font-weight: 800; font-size: 15px; text-transform: uppercase; margin: 0; line-height: 1.1;">TARABA STATE UNIVERSITY</h2>
                        <div style="display: inline-block; color: #1e40af; font-weight: 600; font-size: 12px; text-transform: uppercase; border-top: 1px solid #1e40af; border-bottom: 1px solid #1e40af; padding: 1px 8px; margin-top: 2px;">JALINGO</div>
                    </div>

                    <div style="text-align: center; margin-top: 15px; position: relative; z-index: 2; height: 170px; display: flex; justify-content: center; align-items: center;">
                        <?php 
                            $photoUrl = null;
                            if (!empty($profile['profile_photo'])) {
                                if (filter_var($profile['profile_photo'], FILTER_VALIDATE_URL)) {
                                    $photoUrl = $profile['profile_photo'];
                                } elseif (strpos($profile['profile_photo'], 'uploads/') === 0 || strpos($profile['profile_photo'], '/uploads/') === 0) {
                                    $photoUrl = url(ltrim($profile['profile_photo'], '/'));
                                } else {
                                    $photoUrl = url('uploads/profiles/' . basename($profile['profile_photo']));
                                }
                            }
                        ?>
                        <?php if ($photoUrl): ?>
                            <img src="<?= $photoUrl ?>" style="width: 140px; height: 165px; object-fit: cover; border-radius: 8px; border: 3px solid #1e40af; box-shadow: 0 3px 6px rgba(0,0,0,0.15);" onerror="this.style.display='none';this.nextElementSibling.style.display='flex';">
                            <div style="width: 140px; height: 165px; background: #e2e8f0; color: #64748b; display: none; align-items: center; justify-content: center; font-size: 50px; border-radius: 8px; border: 3px solid #1e40af;"><?= strtoupper(substr($profile['first_name'], 0, 1)) ?></div>
                        <?php else: ?>
                            <div style="width: 140px; height: 165px; background: #e2e8f0; color: #64748b; display: flex; align-items: center; justify-content: center; font-size: 50px; border-radius: 8px; border: 3px solid #1e40af;"><?= strtoupper(substr($profile['first_name'], 0, 1)) ?></div>
                        <?php endif; ?>
                    </div>

                    <div style="text-align: center; margin-top: 10px; position: relative; z-index: 2; padding: 0 10px;">
                        <?php
                        $nameParts = array_filter([
                            $profile['title'] ?? '',
                            $profile['first_name'] ?? '',
                            $profile['middle_name'] ?? '',
                            $profile['last_name'] ?? ''
                        ]);
                        $fullName = trim(implode(' ', $nameParts));
                        $nameLength = strlen($fullName);
                        
                        $fontSize = '19px';
                        if ($nameLength > 30) {
                            $fontSize = '14px';
                        } elseif ($nameLength > 25) {
                            $fontSize = '15px';
                        } elseif ($nameLength > 20) {
                            $fontSize = '16px';
                        }
                        ?>
                        <h3 style="color: #1e3a8a; font-weight: 800; font-size: <?= $fontSize ?>; margin: 0; line-height: 1.1; word-wrap: break-word; word-break: break-word; hyphens: auto;">
                            <?= htmlspecialchars($fullName) ?>
                        </h3>
                        <div style="color: #4b5563; font-size: 13px; font-weight: 600; margin-top: 3px;"><?= htmlspecialchars($profile['designation'] ?? '') ?></div>
                    </div>

                    <div style="margin-top: 15px; margin-left: 70px; margin-right: 15px; position: relative; z-index: 2; font-size: 12px;">
                        <?php 
                        $hasUnit = !empty($profile['unit']) && trim($profile['unit']) !== '';
                        $hasFaculty = !empty($profile['faculty']) && trim($profile['faculty']) !== '';
                        $hasDepartment = !empty($profile['department']) && trim($profile['department']) !== '';
                        
                        if ($hasUnit && !$hasFaculty && !$hasDepartment): 
                        ?>
                        <table style="width: 100%; border-collapse: collapse;">
                            <tr><td style="font-weight: 700; color: #1e40af; width: 55px; vertical-align: top; padding-bottom: 5px;">Staff ID:</td><td style="color: #111; font-weight: 600; vertical-align: top;"><?= htmlspecialchars($profile['staff_number'] ?? 'N/A') ?></td></tr>
                            <tr><td style="font-weight: 700; color: #1e40af; width: 55px; vertical-align: top; padding-bottom: 5px;">Unit:</td><td style="color: #1e3a8a; font-weight: 800; font-size: 14px; vertical-align: top; line-height: 1.3;"><?= htmlspecialchars($profile['unit']) ?></td></tr>
                        </table>
                        <?php else: ?>
                        <table style="width: 100%; border-collapse: collapse;">
                            <tr><td style="font-weight: 700; color: #1e40af; width: 55px; vertical-align: top; padding-bottom: 5px;">Staff ID:</td><td style="color: #111; font-weight: 600; vertical-align: top;"><?= htmlspecialchars($profile['staff_number'] ?? 'N/A') ?></td></tr>
                            <?php if ($hasFaculty): ?>
                            <tr><td style="font-weight: 700; color: #1e40af; width: 55px; vertical-align: top; padding-bottom: 5px;">Faculty:</td><td style="color: #111; font-weight: 600; vertical-align: top;"><?= htmlspecialchars($profile['faculty']) ?></td></tr>
                            <?php endif; ?>
                            <?php if ($hasDepartment): ?>
                            <tr><td style="font-weight: 700; color: #1e40af; width: 55px; vertical-align: top; padding-bottom: 5px;">Dept:</td><td style="color: #111; font-weight: 600; vertical-align: top;"><?= htmlspecialchars($profile['department']) ?></td></tr>
                            <?php endif; ?>
                        </table>
                        <?php endif; ?>
                    </div>

                    <div style="position: absolute; bottom: 0; left: 0; right: 0; height: 40px; background: #1e40af; color: white; display: flex; align-items: center; justify-content: center; font-size: 11px; font-weight: 500; z-index: 2;">
                        Issued: <?= date('F Y') ?>
                    </div>
                </div>
            </div>

            <!-- Back -->
            <div class="id-card" style="width: 350px; height: 550px; background: white; border-radius: 12px; box-shadow: 0 10px 30px rgba(0,0,0,0.2); overflow: hidden; position: relative;">
                <div style="height: 100%; position: relative; background: #fff; display: flex; flex-direction: column;">
                    <div style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%) rotate(-30deg); font-size: 120px; font-weight: 900; color: rgba(30, 64, 175, 0.05); z-index: 0; pointer-events: none;">TSU</div>
                    
                    <div style="position: relative; z-index: 2; flex: 1; display: flex; flex-direction: column; align-items: center; justify-content: center; padding: 20px; text-align: center;">
                        <div style="margin-bottom: 20px;">
                            <div style="font-size: 14px; color: #1e40af; font-weight: 800; margin-bottom: 10px; text-transform: uppercase; letter-spacing: 0.5px;">SCAN THIS TO VERIFY</div>
                            <?php if (!empty($profile['qr_code_url'])): ?>
                                <img src="<?= $profile['qr_code_url'] ?>" style="width: 220px; height: 220px; border: 4px solid #1e3a8a; border-radius: 12px; padding: 5px; background: white; box-shadow: 0 4px 12px rgba(0,0,0,0.15);">
                            <?php endif; ?>
                        </div>

                        <?php if (!empty($profile['blood_group'])): ?>
                        <div style="border: 3px solid #dc2626; border-radius: 10px; padding: 8px 30px; margin-bottom: 20px; background: rgba(255, 255, 255, 0.95); min-width: 140px; box-shadow: 0 2px 5px rgba(0,0,0,0.05);">
                            <div style="font-size: 12px; text-transform: uppercase; color: #dc2626; font-weight: 800; letter-spacing: 1px;">Blood Group</div>
                            <div style="font-size: 32px; font-weight: 900; color: #333; line-height: 1.1;"><?= htmlspecialchars($profile['blood_group']) ?></div>
                        </div>
                        <?php endif; ?>

                        <div style="font-size: 11px; color: #4b5563; line-height: 1.4; margin-top: auto; margin-bottom: 5px;">
                            <p style="margin:0;">If found, please return to:</p>
                            <strong style="color: #1e40af; display: block; font-size: 13px; margin: 2px 0;">SECURITY UNIT</strong>
                            <p style="margin:0;">Taraba State University<br>Jalingo, Nigeria</p>
                        </div>
                    </div>

                    <div style="height: 40px; background: #1e40af; color: white; display: flex; align-items: center; justify-content: center; font-size: 11px; font-weight: 500; z-index: 2;">
                        Property of Taraba State University
                    </div>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/FileSaver.js/2.0.5/FileSaver.min.js"></script>
    
    <script>
        async function downloadAllPDFs() {
            const { jsPDF } = window.jspdf;
            const zip = new JSZip();
            const btn = event.target;
            const originalText = btn.innerHTML;
            btn.disabled = true;
            btn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Processing...';

            const cardPairs = document.querySelectorAll('.card-pair');
            
            for (let i = 0; i < cardPairs.length; i++) {
                const pair = cardPairs[i];
                const cards = pair.querySelectorAll('.id-card');
                const pdf = new jsPDF({ orientation: 'portrait', unit: 'mm', format: [54, 85.6] });

                // Front
                const frontCanvas = await html2canvas(cards[0], { scale: 2, useCORS: true });
                pdf.addImage(frontCanvas.toDataURL('image/jpeg'), 'JPEG', 0, 0, 54, 85.6);
                
                // Back
                pdf.addPage();
                const backCanvas = await html2canvas(cards[1], { scale: 2, useCORS: true });
                pdf.addImage(backCanvas.toDataURL('image/jpeg'), 'JPEG', 0, 0, 54, 85.6);

                zip.file(`ID_Card_${i + 1}.pdf`, pdf.output('blob'));
            }

            const content = await zip.generateAsync({type:"blob"});
            saveAs(content, "TSU_Bulk_IDs_<?= date('Y-m-d') ?>.zip");
            
            btn.disabled = false;
            btn.innerHTML = originalText;
        }
    </script>
</body>
</html>
