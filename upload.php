<?php

$target_dir = __DIR__ . "/uploads/";

if (!is_dir($target_dir)) {
    mkdir($target_dir, 0777, true);
}

$message = "";
$filename = "";

if(isset($_FILES["fileToUpload"])){

    $target_file = $target_dir . basename($_FILES["fileToUpload"]["name"]);
    $uploadOk = 1;

    if ($_FILES["fileToUpload"]["size"] > 500000) {
        $message = "File terlalu besar! Maksimal 500KB.";
        $uploadOk = 0;
    }

    if ($uploadOk == 1) {
        if (move_uploaded_file($_FILES["fileToUpload"]["tmp_name"], $target_file)) {
            $filename = basename($_FILES["fileToUpload"]["name"]);
            $message = "Upload berhasil!";
        } else {
            $message = "Upload gagal! Coba lagi.";
        }
    }
}

$success = ($message === "Upload berhasil!");
?>

<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Hasil Upload</title>
<link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@600&family=DM+Sans:wght@400;500&display=swap" rel="stylesheet">
<style>
  :root {
    --bg-1: #0a1628;
    --emerald: #10b981;
    --emerald-dark: #059669;
    --gold: #f59e0b;
    --glass: rgba(255,255,255,0.05);
    --glass-border: rgba(255,255,255,0.12);
    --text: #f1f5f9;
    --muted: #94a3b8;
    --error: #ef4444;
  }

  * { margin:0; padding:0; box-sizing:border-box; }

  body {
    min-height: 100vh;
    display: flex;
    justify-content: center;
    align-items: center;
    background: var(--bg-1);
    font-family: 'DM Sans', sans-serif;
    overflow: hidden;
  }

  body::before {
    content: '';
    position: fixed;
    width: 600px; height: 600px;
    border-radius: 50%;
    background: radial-gradient(circle, rgba(16,185,129,0.18) 0%, transparent 70%);
    top: -150px; left: -150px;
    pointer-events: none;
  }

  body::after {
    content: '';
    position: fixed;
    width: 500px; height: 500px;
    border-radius: 50%;
    background: radial-gradient(circle, rgba(245,158,11,0.12) 0%, transparent 70%);
    bottom: -100px; right: -100px;
    pointer-events: none;
  }

  .card {
    position: relative;
    width: 460px;
    background: var(--glass);
    backdrop-filter: blur(24px);
    border: 1px solid var(--glass-border);
    padding: 44px 40px;
    border-radius: 24px;
    box-shadow: 0 25px 60px rgba(0,0,0,0.5), inset 0 1px 0 rgba(255,255,255,0.1);
    text-align: center;
    z-index: 1;
  }

  .icon-wrap {
    width: 80px; height: 80px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 38px;
    margin: 0 auto 22px;
  }

  .icon-wrap.ok { background: rgba(16,185,129,0.15); border: 2px solid rgba(16,185,129,0.4); }
  .icon-wrap.err { background: rgba(239,68,68,0.15); border: 2px solid rgba(239,68,68,0.4); }

  h1 {
    font-family: 'Playfair Display', serif;
    font-size: 28px;
    color: var(--text);
    margin-bottom: 10px;
  }

  .status-msg {
    font-size: 15px;
    margin-bottom: 28px;
  }

  .status-msg.ok { color: var(--emerald); }
  .status-msg.err { color: var(--error); }

  .file-info {
    background: rgba(255,255,255,0.04);
    border: 1px solid var(--glass-border);
    border-radius: 12px;
    padding: 14px 18px;
    margin-bottom: 24px;
    text-align: left;
    display: flex;
    align-items: center;
    gap: 12px;
  }

  .file-info span { font-size: 24px; }

  .file-info .info-text small { color: var(--muted); font-size: 11px; display: block; }
  .file-info .info-text strong { color: var(--text); font-size: 13px; word-break: break-all; }

  .btn-group { display: flex; gap: 12px; }

  .btn {
    flex: 1;
    display: inline-block;
    padding: 13px;
    border-radius: 12px;
    font-size: 14px;
    font-weight: 600;
    cursor: pointer;
    border: none;
    text-decoration: none;
    text-align: center;
    font-family: 'DM Sans', sans-serif;
    transition: 0.2s;
  }

  .btn-primary {
    background: linear-gradient(135deg, var(--emerald), var(--emerald-dark));
    color: white;
  }

  .btn-primary:hover { transform: translateY(-1px); box-shadow: 0 6px 20px rgba(16,185,129,0.3); }

  .btn-outline {
    background: transparent;
    color: var(--muted);
    border: 1px solid var(--glass-border);
  }

  .btn-outline:hover { border-color: var(--emerald); color: var(--emerald); }
</style>
</head>
<body>

<div class="card">
  <?php if($success): ?>
    <div class="icon-wrap ok">✅</div>
    <h1>Upload Berhasil!</h1>
    <p class="status-msg ok">File Anda telah berhasil diunggah ke server.</p>

    <div class="file-info">
      <span>📄</span>
      <div class="info-text">
        <small>Nama File</small>
        <strong><?= htmlspecialchars($filename) ?></strong>
      </div>
    </div>

    <div class="btn-group">
      <a href="lihat_file.php" class="btn btn-primary">🖼 Lihat Gallery</a>
      <a href="index.html" class="btn btn-outline">⬆ Upload Lagi</a>
    </div>

  <?php else: ?>
    <div class="icon-wrap err">❌</div>
    <h1>Upload Gagal</h1>
    <p class="status-msg err"><?= htmlspecialchars($message) ?></p>

    <a href="index.html" class="btn btn-primary" style="display:block;">⬅ Coba Lagi</a>
  <?php endif; ?>
</div>

</body>
</html>
