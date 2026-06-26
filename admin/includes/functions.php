<?php
function uploadFile($inputName, $subdir = 'images') {
    if (empty($_FILES[$inputName]) || $_FILES[$inputName]['error'] !== UPLOAD_ERR_OK) {
        return null;
    }

    $file = $_FILES[$inputName];
    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    $allowedImages = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
    $allowedVideos = ['mp4', 'mov', 'avi', 'webm'];
    $allowed = array_merge($allowedImages, $allowedVideos);

    if (!in_array($ext, $allowed)) return null;

    $projectRoot = dirname(__DIR__, 2);
    $uploadDir = $projectRoot . '/uploads/' . $subdir . '/';

    if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);

    $filename = uniqid('jlv_') . '.' . $ext;
    $destPath = $uploadDir . $filename;

    if (move_uploaded_file($file['tmp_name'], $destPath)) {
        return 'uploads/' . $subdir . '/' . $filename;
    }
    return null;
}

function uploadFileFromArray($file, $subdir = 'images') {
    if (!$file || $file['error'] !== UPLOAD_ERR_OK) return null;

    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    $allowed = ['jpg', 'jpeg', 'png', 'gif', 'webp', 'mp4', 'mov', 'avi', 'webm'];
    if (!in_array($ext, $allowed)) return null;

    $projectRoot = dirname(__DIR__, 2);
    $uploadDir = $projectRoot . '/uploads/' . $subdir . '/';
    if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);

    $filename = uniqid('jlv_') . '.' . $ext;
    if (move_uploaded_file($file['tmp_name'], $uploadDir . $filename)) {
        return 'uploads/' . $subdir . '/' . $filename;
    }
    return null;
}

function isActive($paths) {
    $script = $_SERVER['SCRIPT_NAME'];
    foreach ((array)$paths as $p) {
        if ($script === $p) return 'active';
    }
    return '';
}

function assetUrl($path) {
    $script = $_SERVER['SCRIPT_NAME'];
    $pos = strpos($script, '/admin');
    $base = $pos !== false ? substr($script, 0, $pos) : '';
    return rtrim($base, '/') . '/' . ltrim($path, '/');
}
