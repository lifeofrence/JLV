<?php
$pageTitle = 'Page Sections';
include 'header.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    foreach ($_POST['sections'] as $key => $val) {
        $imageUrl = $val['image_url'] ?? '';

        if (!empty($_FILES['sections_img']['name'][$key])) {
            $file = [
                'name' => $_FILES['sections_img']['name'][$key],
                'type' => $_FILES['sections_img']['type'][$key],
                'tmp_name' => $_FILES['sections_img']['tmp_name'][$key],
                'error' => $_FILES['sections_img']['error'][$key],
                'size' => $_FILES['sections_img']['size'][$key],
            ];
            $uploaded = uploadFileFromArray($file, 'images');
            if ($uploaded) $imageUrl = $uploaded;
        }

        $stmt = $pdo->prepare("UPDATE cms_sections SET title=?, subtitle=?, content=?, image_url=? WHERE section_key=?");
        $stmt->execute([
            $val['title'] ?? '',
            $val['subtitle'] ?? '',
            $val['content'] ?? '',
            $imageUrl,
            $key
        ]);
    }
    echo '<div class="alert alert-success">Sections updated! <a href="index.php">Back to CMS</a></div>';
}

$sections = $pdo->query("SELECT * FROM cms_sections ORDER BY id")->fetchAll(PDO::FETCH_ASSOC);
$grouped = [];
foreach ($sections as $s) {
    $prefix = explode('_', $s['section_key'])[0];
    $grouped[$prefix][] = $s;
}
?>
<form method="post" enctype="multipart/form-data">
    <?php foreach ($grouped as $group => $items): ?>
        <div class="card mb-4">
            <div class="card-header">
                <i class="bi bi-<?= $group === 'hero' ? 'camera' : ($group === 'about' ? 'person' : ($group === 'portfolio' ? 'play-btn' : ($group === 'contact' ? 'envelope' : 'building'))) ?> me-2"></i>
                <?= ucfirst($group) ?> Section
            </div>
            <div class="card-body">
                <?php foreach ($items as $section): ?>
                    <div class="mb-3 border-bottom border-secondary pb-3">
                        <strong class="text-secondary" style="font-size: 11px; text-transform: uppercase; letter-spacing: 0.5px;"><?= $section['section_key'] ?></strong>
                        <input type="hidden" name="sections[<?= $section['section_key'] ?>][id]" value="<?= $section['id'] ?>">

                        <?php if (in_array($section['section_key'], ['hero_image', 'about_heading', 'about_tagline', 'portfolio_heading', 'contact_heading', 'footer_site_name'])): ?>
                            <div class="mt-2">
                                <label class="form-label">Title / Alt Text</label>
                                <input type="text" class="form-control" name="sections[<?= $section['section_key'] ?>][title]" value="<?= htmlspecialchars($section['title'] ?? '') ?>">
                            </div>
                        <?php endif; ?>

                        <?php if ($section['section_key'] === 'portfolio_heading'): ?>
                            <div class="mt-2">
                                <label class="form-label">Subtitle</label>
                                <input type="text" class="form-control" name="sections[<?= $section['section_key'] ?>][subtitle]" value="<?= htmlspecialchars($section['subtitle'] ?? '') ?>">
                            </div>
                        <?php endif; ?>

                        <?php if (in_array($section['section_key'], ['about_text_1', 'about_text_2', 'about_text_3'])): ?>
                            <div class="mt-2">
                                <label class="form-label">Paragraph</label>
                                <textarea class="form-control" rows="4" name="sections[<?= $section['section_key'] ?>][content]"><?= htmlspecialchars($section['content'] ?? '') ?></textarea>
                            </div>
                        <?php endif; ?>

                        <?php if (in_array($section['section_key'], ['hero_image', 'about_heading'])): ?>
                            <div class="mt-2">
                                <label class="form-label">Image URL</label>
                                <input type="text" class="form-control" name="sections[<?= $section['section_key'] ?>][image_url]" value="<?= htmlspecialchars($section['image_url'] ?? '') ?>" placeholder="Or upload below">
                            </div>
                            <div class="mt-2">
                                <label class="form-label">Upload Image</label>
                                <input type="file" class="form-control" name="sections_img[<?= $section['section_key'] ?>]" accept="image/*">
                            </div>
                            <?php if ($section['image_url']): ?>
                                <img src="<?= assetUrl($section['image_url']) ?>" class="section-thumb" alt="Preview">
                            <?php endif; ?>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    <?php endforeach; ?>
    <button type="submit" class="btn btn-admin"><i class="bi-check-lg"></i> Save All Sections</button>
    <a href="index.php" class="btn btn-secondary">Cancel</a>
</form>
<?php include 'footer.php'; ?>
