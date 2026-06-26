<?php
$pageTitle = 'Sections';
include 'header.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    foreach ($_POST['sections'] as $key => $val) {
        $stmt = $pdo->prepare("UPDATE cms_sections SET title=?, subtitle=?, content=?, image_url=? WHERE section_key=?");
        $stmt->execute([
            $val['title'] ?? '',
            $val['subtitle'] ?? '',
            $val['content'] ?? '',
            $val['image_url'] ?? '',
            $key
        ]);
    }
    echo '<div class="alert alert-success m-3">Sections updated! <a href="index.php">Back to CMS</a></div>';
}

$sections = $pdo->query("SELECT * FROM cms_sections ORDER BY id")->fetchAll(PDO::FETCH_ASSOC);
$grouped = [];
foreach ($sections as $s) {
    $prefix = explode('_', $s['section_key'])[0];
    $grouped[$prefix][] = $s;
}
?>
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4><i class="bi-layers-fill"></i> Page Sections</h4>
        <a href="index.php" class="btn btn-outline-secondary btn-sm">&larr; CMS Home</a>
    </div>

    <form method="post">
        <?php foreach ($grouped as $group => $items): ?>
            <div class="card bg-dark border-secondary mb-4">
                <div class="card-header border-secondary text-uppercase" style="letter-spacing: 1px; font-size: 14px;">
                    <i class="bi bi-<?= $group === 'hero' ? 'camera' : ($group === 'about' ? 'person' : ($group === 'portfolio' ? 'play-btn' : ($group === 'contact' ? 'envelope' : 'building'))) ?> me-2"></i>
                    <?= ucfirst($group) ?> Section
                </div>
                <div class="card-body">
                    <?php foreach ($items as $section): ?>
                        <div class="mb-3 border-bottom border-secondary pb-3">
                            <strong class="text-secondary" style="font-size: 12px; text-transform: uppercase;"><?= $section['section_key'] ?></strong>
                            <input type="hidden" name="sections[<?= $section['section_key'] ?>][id]" value="<?= $section['id'] ?>">

                            <?php if (in_array($section['section_key'], ['hero_image', 'about_heading', 'about_tagline', 'portfolio_heading', 'contact_heading', 'footer_site_name'])): ?>
                                <div class="mt-2">
                                    <label class="form-label text-light">Title / Alt Text</label>
                                    <input type="text" class="form-control" name="sections[<?= $section['section_key'] ?>][title]" value="<?= htmlspecialchars($section['title'] ?? '') ?>">
                                </div>
                            <?php endif; ?>

                            <?php if ($section['section_key'] === 'portfolio_heading'): ?>
                                <div class="mt-2">
                                    <label class="form-label text-light">Subtitle</label>
                                    <input type="text" class="form-control" name="sections[<?= $section['section_key'] ?>][subtitle]" value="<?= htmlspecialchars($section['subtitle'] ?? '') ?>">
                                </div>
                            <?php endif; ?>

                            <?php if (in_array($section['section_key'], ['about_text_1', 'about_text_2', 'about_text_3'])): ?>
                                <div class="mt-2">
                                    <label class="form-label text-light">Paragraph</label>
                                    <textarea class="form-control" rows="4" name="sections[<?= $section['section_key'] ?>][content]"><?= htmlspecialchars($section['content'] ?? '') ?></textarea>
                                </div>
                            <?php endif; ?>

                            <?php if (in_array($section['section_key'], ['hero_image', 'about_heading'])): ?>
                                <div class="mt-2">
                                    <label class="form-label text-light">Image URL</label>
                                    <input type="text" class="form-control" name="sections[<?= $section['section_key'] ?>][image_url]" value="<?= htmlspecialchars($section['image_url'] ?? '') ?>">
                                    <?php if ($section['image_url']): ?>
                                        <img src="/<?= ltrim($section['image_url'], '/') ?>" style="height: 60px; margin-top: 8px; border-radius: 8px;">
                                    <?php endif; ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endforeach; ?>
        <button type="submit" class="btn btn-admin"><i class="bi-check-lg"></i> Save All Sections</button>
        <a href="index.php" class="btn btn-secondary">Cancel</a>
    </form>
</div>
<?php include 'footer.php'; ?>
