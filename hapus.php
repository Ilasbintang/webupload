<?php

header('Content-Type: application/json');

$folder = __DIR__ . "/uploads/";

if(!isset($_GET['file']) || empty($_GET['file'])) {
    echo json_encode(['success' => false, 'error' => 'Nama file tidak diberikan.']);
    exit;
}

// Sanitize: strip any directory traversal
$filename = basename($_GET['file']);

if(empty($filename)) {
    echo json_encode(['success' => false, 'error' => 'Nama file tidak valid.']);
    exit;
}

$filepath = $folder . $filename;

// Ensure path is still inside uploads folder
if(strpos(realpath($filepath), realpath($folder)) !== 0) {
    echo json_encode(['success' => false, 'error' => 'Akses ditolak.']);
    exit;
}

if(!file_exists($filepath)) {
    echo json_encode(['success' => false, 'error' => 'File tidak ditemukan.']);
    exit;
}

if(unlink($filepath)) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'error' => 'Gagal menghapus file.']);
}
