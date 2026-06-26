<?php
$pageTitle = 'Navigation';
include 'header.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    foreach ($_POST['items'] as $id => $data) {
        $pdo->prepare("UPDATE cms_navigation SET label=?, href=?, sort_order=? WHERE id=?")
            ->execute([$data['label'], $data['href'], (int)$data['sort_order'], (int)$id]);
    }
    echo '<div class="alert alert-success m-3">Navigation saved! <a href="index.php">Back to CMS</a></div>';
}

$items = $pdo->query("SELECT * FROM cms_navigation ORDER BY sort_order")->fetchAll(PDO::FETCH_ASSOC);
?>
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4><i class="bi-list-ul"></i> Navigation Menu</h4>
        <a href="index.php" class="btn btn-outline-secondary btn-sm">&larr; CMS</a>
    </div>

    <div class="card bg-dark border-secondary">
        <div class="card-header border-secondary">Nav Items</div>
        <div class="card-body">
            <form method="post">
                <?php foreach ($items as $item): ?>
                    <div class="row g-2 mb-2 align-items-center">
                        <div class="col-md-4">
                            <input type="text" name="items[<?= $item['id'] ?>][label]" class="form-control" value="<?= htmlspecialchars($item['label']) ?>" required>
                        </div>
                        <div class="col-md-4">
                            <input type="text" name="items[<?= $item['id'] ?>][href]" class="form-control" value="<?= htmlspecialchars($item['href']) ?>" required>
                        </div>
                        <div class="col-md-2">
                            <input type="number" name="items[<?= $item['id'] ?>][sort_order]" class="form-control" value="<?= $item['sort_order'] ?>">
                        </div>
                        <div class="col-md-2">
                            <span class="badge bg-<?= $item['is_active'] ? 'success' : 'secondary' ?>"><?= $item['is_active'] ? 'Active' : 'Inactive' ?></span>
                        </div>
                    </div>
                <?php endforeach; ?>
                <button type="submit" class="btn btn-admin mt-3"><i class="bi-check-lg"></i> Save Navigation</button>
                <a href="index.php" class="btn btn-secondary mt-3">Cancel</a>
            </form>
        </div>
    </div>
</div>
<?php include 'footer.php'; ?>
