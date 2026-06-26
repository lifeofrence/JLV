<?php
$pageTitle = 'Pricing';
include 'header.php';

// Handle save
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_package'])) {
    $id = (int)($_POST['id'] ?? 0);
    $category = $_POST['category'] ?? 'wedding';
    $name = $_POST['package_name'] ?? '';
    $price = $_POST['price'] ?? '';
    $subtitle = $_POST['subtitle'] ?? '';
    $featured = isset($_POST['is_featured']) ? 1 : 0;
    $sort = (int)($_POST['sort_order'] ?? 0);

    if ($id) {
        $pdo->prepare("UPDATE cms_pricing SET category=?, package_name=?, price=?, subtitle=?, is_featured=?, sort_order=? WHERE id=?")
            ->execute([$category, $name, $price, $subtitle, $featured, $sort, $id]);
    } else {
        $pdo->prepare("INSERT INTO cms_pricing (category, package_name, price, subtitle, is_featured, sort_order) VALUES (?,?,?,?,?,?)")
            ->execute([$category, $name, $price, $subtitle, $featured, $sort]);
        $id = $pdo->lastInsertId();
    }

    // Save features
    $pdo->prepare("DELETE FROM cms_pricing_features WHERE pricing_id=?")->execute([$id]);
    if (!empty($_POST['features'])) {
        foreach ($_POST['features'] as $i => $f) {
            if (trim($f)) {
                $pdo->prepare("INSERT INTO cms_pricing_features (pricing_id, feature_text, sort_order) VALUES (?,?,?)")
                    ->execute([$id, trim($f), $i]);
            }
        }
    }
    echo '<div class="alert alert-success m-3">Package saved!</div>';
}

// Handle delete
if (isset($_GET['delete'])) {
    $pdo->prepare("DELETE FROM cms_pricing WHERE id=?")->execute([(int)$_GET['delete']]);
    echo '<div class="alert alert-info m-3">Package deleted.</div>';
}

// Get edit data
$editPackage = null;
$editFeatures = [];
if (isset($_GET['edit'])) {
    $stmt = $pdo->prepare("SELECT * FROM cms_pricing WHERE id=?");
    $stmt->execute([(int)$_GET['edit']]);
    $editPackage = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($editPackage) {
        $stmt = $pdo->prepare("SELECT * FROM cms_pricing_features WHERE pricing_id=? ORDER BY sort_order");
        $stmt->execute([$editPackage['id']]);
        $editFeatures = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}

$categories = ['wedding' => 'Wedding', 'lifestyle' => 'Lifestyle & Brand', 'event' => 'Event'];
$packages = $pdo->query("SELECT * FROM cms_pricing ORDER BY FIELD(category,'wedding','lifestyle','event'), sort_order")->fetchAll(PDO::FETCH_ASSOC);
?>
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4><i class="bi-currency-dollar"></i> Pricing Packages</h4>
        <div>
            <a href="pricing.php" class="btn btn-outline-secondary btn-sm">All Packages</a>
            <a href="index.php" class="btn btn-outline-secondary btn-sm">&larr; CMS</a>
        </div>
    </div>

    <!-- Edit / Add Form -->
    <div class="card bg-dark border-secondary mb-4">
        <div class="card-header border-secondary"><?= $editPackage ? 'Edit' : 'Add' ?> Package</div>
        <div class="card-body">
            <form method="post">
                <input type="hidden" name="id" value="<?= $editPackage['id'] ?? 0 ?>">
                <div class="row g-3">
                    <div class="col-md-3">
                        <label class="form-label">Category</label>
                        <select name="category" class="form-select">
                            <?php foreach ($categories as $k => $v): ?>
                                <option value="<?= $k ?>" <?= ($editPackage['category'] ?? '') === $k ? 'selected' : '' ?>><?= $v ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Package Name</label>
                        <input type="text" name="package_name" class="form-control" value="<?= htmlspecialchars($editPackage['package_name'] ?? '') ?>" required>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Price</label>
                        <input type="text" name="price" class="form-control" value="<?= htmlspecialchars($editPackage['price'] ?? '') ?>" required>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Subtitle</label>
                        <input type="text" name="subtitle" class="form-control" value="<?= htmlspecialchars($editPackage['subtitle'] ?? '') ?>">
                    </div>
                    <div class="col-md-1">
                        <label class="form-label">Sort</label>
                        <input type="number" name="sort_order" class="form-control" value="<?= $editPackage['sort_order'] ?? 0 ?>">
                    </div>
                    <div class="col-md-1 d-flex align-items-end">
                        <div class="form-check">
                            <input type="checkbox" name="is_featured" class="form-check-input" id="is_featured" <?= ($editPackage['is_featured'] ?? 0) ? 'checked' : '' ?>>
                            <label class="form-check-label" for="is_featured">Featured</label>
                        </div>
                    </div>
                </div>
                <div class="mt-3">
                    <label class="form-label">Features (one per line)</label>
                    <textarea name="features[]" class="form-control" rows="5"><?php
                        foreach ($editFeatures as $f) echo htmlspecialchars($f['feature_text']) . "\n";
                    ?></textarea>
                    <small class="text-secondary">Each line is one feature</small>
                </div>
                <button type="submit" name="save_package" class="btn btn-admin mt-3"><i class="bi-check-lg"></i> Save Package</button>
                <?php if ($editPackage): ?><a href="pricing.php" class="btn btn-secondary mt-3">Cancel Edit</a><?php endif; ?>
            </form>
        </div>
    </div>

    <!-- Package List -->
    <?php foreach ($categories as $ck => $cv): ?>
        <h5 class="text-secondary mt-4 text-uppercase" style="letter-spacing: 1px;">
            <i class="bi <?= $ck === 'wedding' ? 'bi-heart-fill' : ($ck === 'lifestyle' ? 'bi-brightness-high-fill' : 'bi-calendar-event-fill') ?> me-2"></i>
            <?= $cv ?>
        </h5>
        <div class="table-responsive">
            <table class="table table-dark table-hover align-middle">
                <thead><tr><th>#</th><th>Name</th><th>Price</th><th>Subtitle</th><th>Featured</th><th>Sort</th><th>Features</th><th></th></tr></thead>
                <tbody>
                    <?php foreach ($packages as $p): if ($p['category'] !== $ck) continue; ?>
                        <tr>
                            <td><?= $p['id'] ?></td>
                            <td><?= htmlspecialchars($p['package_name']) ?></td>
                            <td><?= htmlspecialchars($p['price']) ?></td>
                            <td><?= htmlspecialchars($p['subtitle']) ?></td>
                            <td><?= $p['is_featured'] ? '⭐' : '' ?></td>
                            <td><?= $p['sort_order'] ?></td>
                            <td>
                                <?php
                                $stmt = $pdo->prepare("SELECT feature_text FROM cms_pricing_features WHERE pricing_id=? ORDER BY sort_order");
                                $stmt->execute([$p['id']]);
                                $feats = $stmt->fetchAll(PDO::FETCH_COLUMN);
                                echo implode(', ', array_map('htmlspecialchars', $feats));
                                ?>
                            </td>
                            <td>
                                <a href="pricing.php?edit=<?= $p['id'] ?>" class="btn btn-sm btn-outline-info"><i class="bi-pencil"></i></a>
                                <a href="pricing.php?delete=<?= $p['id'] ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Delete this package?')"><i class="bi-trash"></i></a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endforeach; ?>
</div>
<?php include 'footer.php'; ?>
