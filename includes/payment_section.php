<?php
// Dynamic Payment Information Section (QR Code & Bank Accounts)
if (!isset($pdo)) {
    $dbPath = __DIR__ . '/db.php';
    if (file_exists($dbPath)) {
        require_once $dbPath;
    }
}

// Fetch QR Code Settings
$qrEnabled = '1';
$qrImage = !empty($siteSettings['qr_code_image']) ? $siteSettings['qr_code_image'] : 'assets/images/siddhartha_qr.png';
$qrTitle = $siteSettings['qr_code_title'] ?? 'Fonepay / Mobile Banking QR';
$qrDesc = $siteSettings['qr_code_description'] ?? 'Scan with any mobile banking app, eSewa, Khalti, or Fonepay to pay directly.';

$qrImageUrl = (strpos($qrImage, 'http') === 0) ? $qrImage : ($basePath . ltrim($qrImage, '/'));
$localQrPath = __DIR__ . '/../' . ltrim($qrImage, '/');
if (file_exists($localQrPath)) {
    $qrImageUrl .= '?v=' . filemtime($localQrPath);
}

// Fetch Active Bank Accounts
$bankAccounts = [];
if (isset($pdo)) {
    try {
        $stmtBank = $pdo->query("SELECT * FROM bank_accounts WHERE is_active = 1 ORDER BY is_default DESC, display_order ASC, id ASC");
        $bankAccounts = $stmtBank->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        // Fallback
    }
}

// If no active QR and no active bank accounts, do not render section
if ($qrEnabled !== '1' && empty($bankAccounts)) {
    return;
}
?>

<section class="py-5 bg-light border-top border-bottom border-light-subtle position-relative overflow-hidden" id="payment-info-section">
    <div class="container py-4">
        <!-- Section Header -->
        <div class="text-center mb-5 max-w-700 mx-auto">
            <span class="badge bg-danger-subtle text-danger fw-bold mb-2 px-3 py-2 rounded-pill uppercase tracking-wider" style="font-size: 11.5px;">
                <i class="fa-solid fa-shield-halved me-1"></i> Official Payment Channels
            </span>
            <h2 class="section-title text-primary display-6 fw-extrabold mb-3">Payment & Fee Transfer Information</h2>
            <p class="text-muted lead fs-6">Make direct payments and tuition fee deposits securely using our official bank accounts or instant QR code scanner.</p>
        </div>

        <div class="row g-4 align-items-stretch justify-content-center">
            <?php if (!empty($bankAccounts)): ?>
                <!-- Left Side: Bank Accounts Cards -->
                <div class="<?= ($qrEnabled === '1') ? 'col-12 col-lg-7 col-xl-8' : 'col-12 col-lg-10' ?>">
                    <div class="d-flex flex-column gap-3 h-100">
                        <?php foreach ($bankAccounts as $index => $acc): ?>
                            <div class="card border border-light-subtle rounded-4 shadow-sm p-4 bg-white hover-lift transition-smooth position-relative overflow-hidden">
                                <?php if ($acc['is_default']): ?>
                                    <div class="position-absolute top-0 end-0 bg-danger text-white px-3 py-1 rounded-bl-4 fw-bold uppercase" style="font-size: 10px; letter-spacing: 0.5px; border-bottom-left-radius: 12px;">
                                        <i class="fa-solid fa-star me-1"></i> Primary Account
                                    </div>
                                <?php endif; ?>

                                <div class="row g-4 align-items-center">
                                    <div class="col-12 col-md-7 border-end-md border-light-subtle">
                                        <!-- Bank Name Header -->
                                        <div class="d-flex align-items-center gap-3 mb-3">
                                            <div class="bg-danger-subtle text-danger rounded-3 p-3 d-flex align-items-center justify-content-center flex-shrink-0" style="width: 48px; height: 48px;">
                                                <i class="fa-solid fa-building-columns fs-4"></i>
                                            </div>
                                            <div>
                                                <span class="text-muted uppercase fw-bold d-block" style="font-size: 11px; letter-spacing: 0.5px;">Bank Name</span>
                                                <h4 class="fw-bold text-dark mb-0 fs-5"><?= h($acc['bank_name']) ?></h4>
                                                <?php if (!empty($acc['branch'])): ?>
                                                    <span class="text-muted small" style="font-size: 12px;"><i class="fa-solid fa-location-dot me-1 text-danger opacity-75"></i><?= h($acc['branch']) ?></span>
                                                <?php endif; ?>
                                            </div>
                                        </div>

                                        <!-- Account Holder -->
                                        <div class="mb-3 ps-1">
                                            <span class="text-muted small d-block mb-1" style="font-size: 11.5px;"><i class="fa-solid fa-user me-1 text-primary"></i> Account Holder Name</span>
                                            <div class="fw-bold text-dark fs-6 font-monospace" style="letter-spacing: 0.2px;"><?= h($acc['account_name']) ?></div>
                                        </div>

                                        <!-- Remarks / Instructions -->
                                        <?php if (!empty($acc['instructions']) || !empty($acc['remarks'])): ?>
                                            <div class="bg-light p-2 px-3 rounded-3 border border-light-subtle mt-2 text-muted small" style="font-size: 11.5px; line-height: 1.4;">
                                                <i class="fa-solid fa-circle-info text-primary me-1"></i>
                                                <?= h($acc['instructions'] ?? $acc['remarks']) ?>
                                            </div>
                                        <?php endif; ?>
                                    </div>

                                    <div class="col-12 col-md-5">
                                        <!-- Account Number Block with Copy Action -->
                                        <div class="bg-light p-3 rounded-4 border border-light-subtle text-center text-md-start">
                                            <span class="text-muted small d-block mb-1 fw-semibold" style="font-size: 11.5px;">
                                                <i class="fa-solid fa-credit-card me-1 text-success"></i> Account Number
                                            </span>
                                            <div class="fw-extrabold text-primary fs-4 tracking-wide font-monospace text-break mb-3 select-all-text" id="acc-num-<?= $acc['id'] ?>">
                                                <?= h($acc['account_number']) ?>
                                            </div>

                                            <button class="btn btn-secondary w-100 rounded-pill py-2 btn-sm fw-bold btn-copy-acc d-flex align-items-center justify-content-center gap-2" 
                                                    data-clipboard-target="#acc-num-<?= $acc['id'] ?>" 
                                                    data-acc-num="<?= h($acc['account_number']) ?>"
                                                    type="button">
                                                <i class="fa-solid fa-copy"></i>
                                                <span>Copy Account Number</span>
                                            </button>
                                        </div>

                                        <!-- Additional Account Metadata Tags -->
                                        <div class="d-flex flex-wrap gap-2 mt-3 justify-content-center justify-content-md-start">
                                            <?php if (!empty($acc['account_type'])): ?>
                                                <span class="badge bg-white text-dark border border-light-subtle shadow-2xs" style="font-size: 11px;">Type: <?= h($acc['account_type']) ?></span>
                                            <?php endif; ?>
                                            <?php if (!empty($acc['swift_code'])): ?>
                                                <span class="badge bg-white text-dark border border-light-subtle shadow-2xs" style="font-size: 11px;">SWIFT: <?= h($acc['swift_code']) ?></span>
                                            <?php endif; ?>
                                            <?php if (!empty($acc['ifsc_code'])): ?>
                                                <span class="badge bg-white text-dark border border-light-subtle shadow-2xs" style="font-size: 11px;">IFSC: <?= h($acc['ifsc_code']) ?></span>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endif; ?>

            <?php if ($qrEnabled === '1'): ?>
                <!-- Right Side: QR Code Container -->
                <div class="<?= !empty($bankAccounts) ? 'col-12 col-lg-5 col-xl-4' : 'col-12 col-md-8 col-lg-6' ?>">
                    <div class="card border-0 rounded-4 shadow-sm h-100 p-4 text-center bg-white position-relative hover-lift transition-smooth">
                        <div class="d-inline-flex align-items-center justify-content-center bg-primary-subtle text-primary rounded-circle mb-3 mx-auto" style="width: 48px; height: 48px;">
                            <i class="fa-solid fa-qrcode fs-4"></i>
                        </div>
                        <h4 class="fw-bold text-dark mb-1" style="font-size: 1.25rem;"><?= h($qrTitle) ?></h4>
                        <p class="text-muted small mb-3" style="font-size: 12.5px;"><?= h($qrDesc) ?></p>

                        <!-- High Quality Framed QR Code -->
                        <div class="position-relative mx-auto bg-white p-3 rounded-4 border border-light-subtle shadow-sm my-2 cursor-pointer group-qr-hover" 
                             style="max-width: 280px;" 
                             data-bs-toggle="modal" 
                             data-bs-target="#qrCodeModal" 
                             title="Click to expand QR Code">
                            <img src="<?= h($qrImageUrl) ?>" 
                                 alt="<?= h($qrTitle) ?>" 
                                 class="img-fluid rounded-3 object-fit-contain transition-smooth qr-zoom-img" 
                                 loading="lazy" 
                                 style="max-height: 240px; width: 100%;">
                            <div class="position-absolute bottom-0 start-50 translate-middle-x mb-2 bg-dark bg-opacity-75 text-white px-3 py-1 rounded-pill small fw-semibold opacity-0 group-qr-hover-show transition-smooth" style="font-size: 11px;">
                                <i class="fa-solid fa-expand me-1"></i> Click to Enlarge
                            </div>
                        </div>

                        <!-- QR Direct Account Info Details Card -->
                        <div class="bg-light p-3 rounded-3 border border-light-subtle my-3 text-start small" style="font-size: 12px;">
                            <div class="d-flex align-items-center justify-content-between mb-1">
                                <span class="fw-bold text-primary"><i class="fa-solid fa-building-columns me-1"></i> Siddhartha Bank Limited</span>
                                <span class="badge bg-danger text-white" style="font-size: 9.5px;">Fonepay QR</span>
                            </div>
                            <div class="text-dark mb-1"><span class="text-muted">A/C Holder:</span> <strong>KRISHNA EDUCATION FOUNDATION PVT LTD</strong></div>
                            <div class="d-flex align-items-center justify-content-between">
                                <div class="text-dark"><span class="text-muted">Account No:</span> <code class="fw-extrabold text-primary font-monospace" style="font-size: 13.5px;">55509839020</code></div>
                                <button class="btn btn-sm btn-outline-secondary py-0 px-2 btn-copy-acc" data-acc-num="55509839020" style="font-size: 10.5px;" type="button">
                                    <i class="fa-regular fa-copy"></i> Copy
                                </button>
                            </div>
                        </div>

                        <!-- Scan Instructions & Mobile Banking Badges -->
                        <div class="mt-2 pt-2 border-top border-light">
                            <div class="d-flex align-items-center justify-content-center gap-2 text-muted small fw-semibold mb-2" style="font-size: 12px;">
                                <i class="fa-solid fa-mobile-screen-button text-danger"></i>
                                <span>Scan with any Mobile Banking App</span>
                            </div>
                            <div class="d-flex align-items-center justify-content-center flex-wrap gap-2 pt-1 opacity-85">
                                <span class="badge bg-light text-dark border border-light-subtle px-2 py-1" style="font-size: 10.5px;"><i class="fa-solid fa-building-columns me-1 text-primary"></i>Fonepay</span>
                                <span class="badge bg-light text-dark border border-light-subtle px-2 py-1" style="font-size: 10.5px;"><i class="fa-solid fa-wallet me-1 text-success"></i>eSewa</span>
                                <span class="badge bg-light text-dark border border-light-subtle px-2 py-1" style="font-size: 10.5px;"><i class="fa-solid fa-bolt me-1 text-warning"></i>Khalti</span>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</section>

<!-- Lightbox Modal for QR Code Zoom -->
<?php if ($qrEnabled === '1'): ?>
<div class="modal fade" id="qrCodeModal" tabindex="-1" aria-labelledby="qrCodeModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 rounded-4 shadow-lg overflow-hidden">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-bold text-dark" id="qrCodeModalLabel"><?= h($qrTitle) ?></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center p-4">
                <img src="<?= h($qrImageUrl) ?>" alt="<?= h($qrTitle) ?>" class="img-fluid rounded-3 max-w-100 shadow-sm" style="max-height: 380px;">
                <p class="text-muted small mt-3 mb-0"><?= h($qrDesc) ?></p>
            </div>
            <div class="modal-footer border-0 pt-0 justify-content-center">
                <a href="<?= h($qrImageUrl) ?>" download="Krishna_Education_Payment_QR.png" class="btn btn-primary rounded-pill px-4 btn-sm fw-bold">
                    <i class="fa-solid fa-download me-1"></i> Download QR Image
                </a>
                <button type="button" class="btn btn-light rounded-pill px-4 btn-sm fw-semibold" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
<?php endif; ?>

<!-- Copy Account Number Notification Toast Script -->
<script>
document.addEventListener("DOMContentLoaded", function() {
    $(document).on('click', '.btn-copy-acc', function() {
        var $btn = $(this);
        var accNum = $btn.attr('data-acc-num') || '';
        var originalText = $btn.html();
        
        if (navigator.clipboard && window.isSecureContext) {
            navigator.clipboard.writeText(accNum).then(function() {
                showCopySuccess($btn, originalText);
            }).catch(function() {
                fallbackCopyTextToClipboard(accNum, $btn, originalText);
            });
        } else {
            fallbackCopyTextToClipboard(accNum, $btn, originalText);
        }
    });

    function showCopySuccess($btn, originalText) {
        $btn.removeClass('btn-secondary').addClass('btn-success').html('<i class="fa-solid fa-check me-1"></i> Copied to Clipboard!');
        setTimeout(function() {
            $btn.removeClass('btn-success').addClass('btn-secondary').html(originalText);
        }, 2500);
    }

    function fallbackCopyTextToClipboard(text, $btn, originalText) {
        var textArea = document.createElement("textarea");
        textArea.value = text;
        textArea.style.top = "0";
        textArea.style.left = "0";
        textArea.style.position = "fixed";
        document.body.appendChild(textArea);
        textArea.focus();
        textArea.select();
        try {
            document.execCommand('copy');
            showCopySuccess($btn, originalText);
        } catch (err) {
            alert('Account number: ' + text);
        }
        document.body.removeChild(textArea);
    }
});
</script>
