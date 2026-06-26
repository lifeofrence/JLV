<?php
$pageTitle = 'Portfolio Videos';
include 'header.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save'])) {
    $id = (int)($_POST['id'] ?? 0);
    $title = $_POST['title'] ?? '';
    $video_src = $_POST['video_src'] ?? '';
    $instagram_url = $_POST['instagram_url'] ?? '';
    $sort = (int)($_POST['sort_order'] ?? 0);

    if (!empty($_FILES['video_file']['name'])) {
        $uploaded = uploadFile('video_file', 'videos');
        if ($uploaded) $video_src = $uploaded;
    }

    if ($id) {
        $pdo->prepare("UPDATE cms_portfolio SET title=?, video_src=?, instagram_url=?, sort_order=? WHERE id=?")
            ->execute([$title, $video_src, $instagram_url, $sort, $id]);
    } else {
        $pdo->prepare("INSERT INTO cms_portfolio (title, video_src, instagram_url, sort_order) VALUES (?,?,?,?)")
            ->execute([$title, $video_src, $instagram_url, $sort]);
    }
    echo '<div class="alert alert-success">Portfolio item saved!</div>';
}

if (isset($_GET['delete'])) {
    $pdo->prepare("DELETE FROM cms_portfolio WHERE id=?")->execute([(int)$_GET['delete']]);
    echo '<div class="alert alert-info">Item deleted.</div>';
}

$editItem = null;
if (isset($_GET['edit'])) {
    $stmt = $pdo->prepare("SELECT * FROM cms_portfolio WHERE id=?");
    $stmt->execute([(int)$_GET['edit']]);
    $editItem = $stmt->fetch(PDO::FETCH_ASSOC);
}

$items = $pdo->query("SELECT * FROM cms_portfolio ORDER BY sort_order")->fetchAll(PDO::FETCH_ASSOC);
?>
<div class="card mb-4">
    <div class="card-header"><?= $editItem ? 'Edit' : 'Add' ?> Video Item</div>
    <div class="card-body">
        <form method="post" enctype="multipart/form-data">
            <input type="hidden" name="id" value="<?= $editItem['id'] ?? 0 ?>">
            <div class="row g-3">
                <div class="col-md-4">
                    <label class="form-label">Title</label>
                    <input type="text" name="title" class="form-control" value="<?= htmlspecialchars($editItem['title'] ?? '') ?>">
                </div>
                <div class="col-md-4">
                    <label class="form-label">Video Source (URL or upload)</label>
                    <input type="text" name="video_src" class="form-control" value="<?= htmlspecialchars($editItem['video_src'] ?? '') ?>" placeholder="video/filename.mp4">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Instagram URL</label>
                    <input type="text" name="instagram_url" class="form-control" value="<?= htmlspecialchars($editItem['instagram_url'] ?? '') ?>">
                </div>
                <div class="col-md-1">
                    <label class="form-label">Sort</label>
                    <input type="number" name="sort_order" class="form-control" value="<?= $editItem['sort_order'] ?? 0 ?>">
                </div>
            </div>
            <div class="mt-3">
                <label class="form-label">Upload Video File</label>
                <input type="file" name="video_file" class="form-control" accept="video/*">
                <small class="text-secondary">Uploads stored in /uploads/videos/</small>
            </div>
            <button type="submit" name="save" class="btn btn-admin mt-3"><i class="bi-check-lg"></i> Save</button>
            <?php if ($editItem): ?><a href="portfolio.php" class="btn btn-secondary mt-3">Cancel</a><?php endif; ?>
        </form>
    </div>
</div>

<div class="table-responsive">
    <table class="table table-dark table-hover align-middle">
        <thead><tr><th>#</th><th>Title</th><th>Video</th><th>Instagram</th><th>Sort</th><th></th></tr></thead>
        <tbody>
            <?php foreach ($items as $item): ?>
                <tr>
                    <td><?= $item['id'] ?></td>
                    <td><?= htmlspecialchars($item['title'] ?: '-') ?></td>
                    <td>
                        <?php if ($item['video_src']): ?>
                            <span class="text-info" title="<?= htmlspecialchars($item['video_src']) ?>">
                                <i class="bi-play-btn-fill"></i> <?= basename($item['video_src']) ?>
                            </span>
                        <?php else: ?>
                            -
                        <?php endif; ?>
                    </td>
                    <td><a href="<?= htmlspecialchars($item['instagram_url']) ?>" target="_blank" class="text-light">View <i class="bi-box-arrow-up-right"></i></a></td>
                    <td><?= $item['sort_order'] ?></td>
                    <td>
                        <a href="portfolio.php?edit=<?= $item['id'] ?>" class="btn btn-sm btn-outline-info"><i class="bi-pencil"></i></a>
                        <a href="portfolio.php?delete=<?= $item['id'] ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Delete?')"><i class="bi-trash"></i></a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
<?php include 'footer.php'; ?>
