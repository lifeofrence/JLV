<?php
$pageTitle = 'Settings';
include 'header.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    foreach ($_POST['settings'] as $key => $val) {
        $stmt = $pdo->prepare("UPDATE cms_settings SET setting_value=? WHERE setting_key=?");
        $stmt->execute([trim($val), $key]);
    }
    echo '<div class="alert alert-success m-3">Settings saved! <a href="index.php">Back to CMS</a></div>';
}

$settings = $pdo->query("SELECT * FROM cms_settings ORDER BY setting_key")->fetchAll(PDO::FETCH_ASSOC);
$grouped = [];
foreach ($settings as $s) {
    if (str_starts_with($s['setting_key'], 'meta_') || str_starts_with($s['setting_key'], 'og_') || str_starts_with($s['setting_key'], 'twitter_') || str_starts_with($s['setting_key'], 'json_ld')) {
        $grouped['seo'][] = $s;
    } else {
        $grouped['general'][] = $s;
    }
}
?>
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4><i class="bi-gear-wide-connected"></i> Site Settings & SEO</h4>
        <a href="index.php" class="btn btn-outline-secondary btn-sm">&larr; CMS</a>
    </div>

    <form method="post">
        <!-- General -->
        <div class="card bg-dark border-secondary mb-4">
            <div class="card-header border-secondary">General Settings</div>
            <div class="card-body">
                <div class="row g-3">
                    <?php foreach ($grouped['general'] as $s): ?>
                        <div class="col-md-6">
                            <label class="form-label text-secondary text-uppercase" style="font-size: 12px;"><?= str_replace('_', ' ', $s['setting_key']) ?></label>
                            <input type="text" class="form-control" name="settings[<?= $s['setting_key'] ?>]" value="<?= htmlspecialchars($s['setting_value'] ?? '') ?>">
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>

        <!-- SEO -->
        <div class="card bg-dark border-secondary mb-4">
            <div class="card-header border-secondary">SEO & Meta Tags</div>
            <div class="card-body">
                <div class="row g-3">
                    <?php foreach ($grouped['seo'] as $s): ?>
                        <div class="col-md-6">
                            <label class="form-label text-secondary text-uppercase" style="font-size: 12px;"><?= str_replace('_', ' ', $s['setting_key']) ?></label>
                            <?php if (in_array($s['setting_key'], ['meta_keywords', 'json_ld_service_types'])): ?>
                                <textarea class="form-control" rows="2" name="settings[<?= $s['setting_key'] ?>]"><?= htmlspecialchars($s['setting_value'] ?? '') ?></textarea>
                            <?php else: ?>
                                <input type="text" class="form-control" name="settings[<?= $s['setting_key'] ?>]" value="<?= htmlspecialchars($s['setting_value'] ?? '') ?>">
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>

        <button type="submit" class="btn btn-admin"><i class="bi-check-lg"></i> Save All Settings</button>
        <a href="index.php" class="btn btn-secondary">Cancel</a>
    </form>
</div>
<?php include 'footer.php'; ?>
