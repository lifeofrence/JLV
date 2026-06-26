<?php
$pageTitle = 'Social Links';
include 'header.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save'])) {
    foreach ($_POST['links'] as $id => $data) {
        $pdo->prepare("UPDATE cms_social_links SET platform=?, url=?, sort_order=? WHERE id=?")
            ->execute([$data['platform'], $data['url'], (int)$data['sort_order'], (int)$id]);
    }
    if (!empty($_POST['new_platform'])) {
        $pdo->prepare("INSERT INTO cms_social_links (platform, url, sort_order) VALUES (?,?,?)")
            ->execute([$_POST['new_platform'], $_POST['new_url'], 99]);
    }
    echo '<div class="alert alert-success m-3">Social links updated! <a href="index.php">Back to CMS</a></div>';
}

$links = $pdo->query("SELECT * FROM cms_social_links ORDER BY sort_order")->fetchAll(PDO::FETCH_ASSOC);
?>
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4><i class="bi-share"></i> Social Media Links</h4>
        <a href="index.php" class="btn btn-outline-secondary btn-sm">&larr; CMS</a>
    </div>

    <div class="card bg-dark border-secondary mb-4">
        <div class="card-header border-secondary">Existing Links</div>
        <div class="card-body">
            <form method="post">
                <?php foreach ($links as $link): ?>
                    <div class="row g-2 mb-2 align-items-center">
                        <input type="hidden" name="links[<?= $link['id'] ?>][id]" value="<?= $link['id'] ?>">
                        <div class="col-md-3">
                            <input type="text" name="links[<?= $link['id'] ?>][platform]" class="form-control" value="<?= htmlspecialchars($link['platform']) ?>">
                        </div>
                        <div class="col-md-6">
                            <input type="text" name="links[<?= $link['id'] ?>][url]" class="form-control" value="<?= htmlspecialchars($link['url']) ?>">
                        </div>
                        <div class="col-md-1">
                            <input type="number" name="links[<?= $link['id'] ?>][sort_order]" class="form-control" value="<?= $link['sort_order'] ?>">
                        </div>
                        <div class="col-md-2">
                            <span class="badge bg-secondary"><i class="<?= $link['icon'] ?>"></i> <?= $link['icon'] ?></span>
                        </div>
                    </div>
                <?php endforeach; ?>
                <hr class="border-secondary">
                <strong class="text-secondary">Add New</strong>
                <div class="row g-2 mt-2">
                    <div class="col-md-3">
                        <input type="text" name="new_platform" class="form-control" placeholder="Platform name">
                    </div>
                    <div class="col-md-7">
                        <input type="text" name="new_url" class="form-control" placeholder="Full URL">
                    </div>
                </div>
                <button type="submit" name="save" class="btn btn-admin mt-3"><i class="bi-check-lg"></i> Save</button>
            </form>
        </div>
    </div>
</div>
<?php include 'footer.php'; ?>
