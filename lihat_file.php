<?php

$folder = "uploads/";

if (!is_dir($folder)) {
    mkdir($folder, 0777, true);
}

$files = scandir($folder);
$imageExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
$allExtensions   = ['jpg', 'jpeg', 'png', 'gif', 'webp', 'pdf', 'txt', 'zip', 'mp4', 'docx', 'xlsx'];

// Collect all files (images + other)
$fileList = [];
foreach($files as $file) {
    if($file === '.' || $file === '..') continue;
    $ext = strtolower(pathinfo($file, PATHINFO_EXTENSION));
    if(in_array($ext, $allExtensions)) {
        $fileList[] = [
            'name' => $file,
            'ext'  => $ext,
            'path' => $folder . $file,
            'size' => filesize($folder . $file),
            'is_image' => in_array($ext, $imageExtensions),
        ];
    }
}

function formatBytes($bytes, $precision = 1) {
    $units = ['B', 'KB', 'MB', 'GB'];
    $bytes = max($bytes, 0);
    $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
    $pow = min($pow, count($units) - 1);
    return round($bytes / pow(1024, $pow), $precision) . ' ' . $units[$pow];
}

function fileIcon($ext) {
    $map = [
        'pdf'  => '📕', 'txt' => '📝', 'zip' => '🗜',
        'mp4'  => '🎬', 'docx'=> '📘', 'xlsx'=> '📗',
        'jpg'  => '🖼', 'jpeg'=> '🖼', 'png' => '🖼',
        'gif'  => '🖼', 'webp'=> '🖼',
    ];
    return $map[$ext] ?? '📄';
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Gallery Upload</title>
<link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700&family=DM+Sans:wght@400;500;600&display=swap" rel="stylesheet">
<style>

:root {
  --bg-1: #0a1628;
  --bg-2: #0d2137;
  --surface: #111e35;
  --emerald: #10b981;
  --emerald-dark: #059669;
  --gold: #f59e0b;
  --gold-light: #fcd34d;
  --glass: rgba(255,255,255,0.04);
  --glass-border: rgba(255,255,255,0.1);
  --text: #f1f5f9;
  --muted: #64748b;
  --muted-light: #94a3b8;
  --danger: #ef4444;
  --danger-dark: #dc2626;
}

* { margin:0; padding:0; box-sizing:border-box; }

body {
  background: var(--bg-1);
  color: var(--text);
  min-height: 100vh;
  font-family: 'DM Sans', sans-serif;
}

/* Ambient */
.ambient-1 {
  position: fixed;
  width: 700px; height: 700px;
  border-radius: 50%;
  background: radial-gradient(circle, rgba(16,185,129,0.12) 0%, transparent 70%);
  top: -200px; left: -200px;
  pointer-events: none;
  z-index: 0;
}
.ambient-2 {
  position: fixed;
  width: 600px; height: 600px;
  border-radius: 50%;
  background: radial-gradient(circle, rgba(245,158,11,0.08) 0%, transparent 70%);
  bottom: -150px; right: -150px;
  pointer-events: none;
  z-index: 0;
}

.wrapper {
  position: relative;
  z-index: 1;
  max-width: 1280px;
  margin: auto;
  padding: 48px 32px 80px;
}

/* Header */
.topbar {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 48px;
  flex-wrap: wrap;
  gap: 16px;
}

.brand {
  display: flex;
  flex-direction: column;
}

.badge {
  display: inline-block;
  background: linear-gradient(90deg, var(--emerald), var(--gold));
  color: #0a1628;
  font-size: 10px;
  font-weight: 700;
  letter-spacing: 0.12em;
  text-transform: uppercase;
  padding: 3px 10px;
  border-radius: 999px;
  margin-bottom: 8px;
  width: fit-content;
}

h1 {
  font-family: 'Playfair Display', serif;
  font-size: 36px;
  background: linear-gradient(90deg, var(--text), var(--gold-light));
  -webkit-background-clip: text;
  -webkit-text-fill-color: transparent;
  background-clip: text;
  line-height: 1.1;
}

.header-actions {
  display: flex;
  gap: 12px;
  align-items: center;
}

.stat-chip {
  background: var(--glass);
  border: 1px solid var(--glass-border);
  border-radius: 999px;
  padding: 8px 18px;
  font-size: 13px;
  color: var(--muted-light);
}

.stat-chip strong { color: var(--emerald); }

.btn-upload {
  display: inline-flex;
  align-items: center;
  gap: 8px;
  padding: 10px 20px;
  background: linear-gradient(135deg, var(--emerald), var(--emerald-dark));
  color: white;
  text-decoration: none;
  border-radius: 12px;
  font-size: 14px;
  font-weight: 600;
  transition: 0.2s;
  white-space: nowrap;
}

.btn-upload:hover {
  transform: translateY(-2px);
  box-shadow: 0 8px 24px rgba(16,185,129,0.3);
}

/* Grid */
.gallery {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
  gap: 22px;
}

/* Card */
.card {
  background: var(--glass);
  border: 1px solid var(--glass-border);
  border-radius: 20px;
  overflow: hidden;
  backdrop-filter: blur(12px);
  transition: transform 0.25s, box-shadow 0.25s, border-color 0.25s;
  animation: fadeUp 0.5s ease both;
}

.card:hover {
  transform: translateY(-4px);
  box-shadow: 0 20px 50px rgba(0,0,0,0.4);
  border-color: rgba(16,185,129,0.3);
}

@keyframes fadeUp {
  from { opacity:0; transform:translateY(16px); }
  to   { opacity:1; transform:translateY(0); }
}

/* Staggered delays */
.card:nth-child(1)  { animation-delay: 0.05s; }
.card:nth-child(2)  { animation-delay: 0.10s; }
.card:nth-child(3)  { animation-delay: 0.15s; }
.card:nth-child(4)  { animation-delay: 0.20s; }
.card:nth-child(5)  { animation-delay: 0.25s; }
.card:nth-child(6)  { animation-delay: 0.30s; }

.image-box {
  width: 100%;
  height: 200px;
  background: var(--surface);
  display: flex;
  justify-content: center;
  align-items: center;
  overflow: hidden;
  position: relative;
}

.image-box img {
  width: 100%;
  height: 100%;
  object-fit: cover;
  transition: transform 0.4s;
}

.card:hover .image-box img {
  transform: scale(1.05);
}

.file-icon-big {
  font-size: 56px;
  opacity: 0.8;
}

/* Ext badge on image */
.ext-tag {
  position: absolute;
  top: 10px; right: 10px;
  background: rgba(10,22,40,0.75);
  backdrop-filter: blur(8px);
  border: 1px solid var(--glass-border);
  color: var(--gold-light);
  font-size: 10px;
  font-weight: 700;
  letter-spacing: 0.08em;
  text-transform: uppercase;
  padding: 3px 8px;
  border-radius: 6px;
}

.content { padding: 18px 18px 20px; }

.filename {
  word-break: break-all;
  font-size: 14px;
  color: var(--text);
  font-weight: 500;
  margin-bottom: 4px;
  line-height: 1.4;
}

.filesize {
  font-size: 11px;
  color: var(--muted);
  margin-bottom: 16px;
}

.btn-group {
  display: flex;
  gap: 8px;
}

.btn {
  flex: 1;
  text-align: center;
  padding: 9px 6px;
  border-radius: 10px;
  text-decoration: none;
  font-size: 13px;
  font-weight: 600;
  transition: 0.2s;
  border: none;
  cursor: pointer;
  font-family: 'DM Sans', sans-serif;
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 5px;
}

.btn-view {
  background: rgba(37,99,235,0.15);
  color: #60a5fa;
  border: 1px solid rgba(37,99,235,0.3);
}

.btn-view:hover {
  background: rgba(37,99,235,0.3);
  color: white;
}

.btn-download {
  background: rgba(16,185,129,0.12);
  color: var(--emerald);
  border: 1px solid rgba(16,185,129,0.3);
}

.btn-download:hover {
  background: rgba(16,185,129,0.25);
  color: white;
}

.btn-delete {
  background: rgba(239,68,68,0.1);
  color: #f87171;
  border: 1px solid rgba(239,68,68,0.25);
  flex: 0 0 auto;
  padding: 9px 12px;
}

.btn-delete:hover {
  background: rgba(239,68,68,0.25);
  color: white;
}

/* Empty state */
.empty {
  grid-column: 1 / -1;
  text-align: center;
  padding: 80px 20px;
}

.empty-icon { font-size: 64px; margin-bottom: 16px; opacity: 0.5; }
.empty h2 { color: var(--muted-light); font-family: 'Playfair Display', serif; margin-bottom: 8px; }
.empty p { color: var(--muted); font-size: 14px; margin-bottom: 24px; }

/* Toast notification */
.toast {
  position: fixed;
  bottom: 28px; right: 28px;
  background: var(--surface);
  border: 1px solid rgba(239,68,68,0.4);
  border-radius: 14px;
  padding: 14px 20px;
  font-size: 14px;
  color: #fca5a5;
  box-shadow: 0 10px 30px rgba(0,0,0,0.4);
  z-index: 999;
  display: none;
  animation: slideIn 0.3s ease;
}

@keyframes slideIn {
  from { transform: translateX(60px); opacity:0; }
  to   { transform: translateX(0); opacity:1; }
}

/* Confirm overlay */
.overlay {
  display: none;
  position: fixed;
  inset: 0;
  background: rgba(0,0,0,0.65);
  backdrop-filter: blur(6px);
  z-index: 100;
  justify-content: center;
  align-items: center;
}

.overlay.active { display: flex; }

.dialog {
  background: #111e35;
  border: 1px solid rgba(239,68,68,0.3);
  border-radius: 20px;
  padding: 36px 32px;
  max-width: 380px;
  width: 90%;
  text-align: center;
  box-shadow: 0 30px 80px rgba(0,0,0,0.6);
  animation: popIn 0.3s ease;
}

@keyframes popIn {
  from { transform: scale(0.9); opacity:0; }
  to   { transform: scale(1); opacity:1; }
}

.dialog-icon { font-size: 44px; margin-bottom: 14px; }
.dialog h2 { color: var(--text); margin-bottom: 8px; font-size: 20px; }
.dialog p { color: var(--muted-light); font-size: 14px; margin-bottom: 24px; }
.dialog .fname { color: #f87171; font-weight: 600; word-break: break-all; }

.dialog-btns { display: flex; gap: 12px; }
.dialog-btn { flex:1; padding:12px; border-radius:12px; border:none; font-size:14px; font-weight:600; cursor:pointer; font-family:'DM Sans',sans-serif; transition:0.2s; }
.dialog-cancel { background:var(--glass); border:1px solid var(--glass-border); color:var(--muted-light); }
.dialog-cancel:hover { color:var(--text); border-color:var(--muted-light); }
.dialog-confirm { background:linear-gradient(135deg,var(--danger),var(--danger-dark)); color:white; }
.dialog-confirm:hover { transform:translateY(-1px); box-shadow:0 6px 20px rgba(239,68,68,0.35); }

</style>
</head>
<body>

<div class="ambient-1"></div>
<div class="ambient-2"></div>

<!-- Delete Confirm Dialog -->
<div class="overlay" id="deleteOverlay">
  <div class="dialog">
    <div class="dialog-icon">🗑</div>
    <h2>Hapus File?</h2>
    <p>File <span class="fname" id="deleteFileName"></span> akan dihapus secara permanen dan tidak dapat dikembalikan.</p>
    <div class="dialog-btns">
      <button class="dialog-btn dialog-cancel" onclick="closeDialog()">Batal</button>
      <button class="dialog-btn dialog-confirm" onclick="confirmDelete()">Ya, Hapus</button>
    </div>
  </div>
</div>

<!-- Toast -->
<div class="toast" id="toast"></div>

<div class="wrapper">

  <div class="topbar">
    <div class="brand">
      <span class="badge">📁 File Manager</span>
      <h1>Gallery Upload</h1>
    </div>
    <div class="header-actions">
      <div class="stat-chip">
        <strong><?= count($fileList) ?></strong> file tersimpan
      </div>
      <a href="index.html" class="btn-upload">⬆ Upload File</a>
    </div>
  </div>

  <div class="gallery">

    <?php if(empty($fileList)): ?>
      <div class="empty">
        <div class="empty-icon">🗂</div>
        <h2>Belum Ada File</h2>
        <p>Mulai unggah file pertama Anda ke gallery.</p>
        <a href="index.html" class="btn-upload" style="display:inline-flex">⬆ Upload Sekarang</a>
      </div>

    <?php else: ?>
      <?php foreach($fileList as $f): ?>

        <div class="card" id="card-<?= md5($f['name']) ?>">

          <div class="image-box">
            <?php if($f['is_image']): ?>
              <img src="<?= htmlspecialchars($f['path']) ?>" alt="<?= htmlspecialchars($f['name']) ?>" loading="lazy">
            <?php else: ?>
              <div class="file-icon-big"><?= fileIcon($f['ext']) ?></div>
            <?php endif; ?>
            <span class="ext-tag"><?= strtoupper($f['ext']) ?></span>
          </div>

          <div class="content">
            <div class="filename"><?= htmlspecialchars($f['name']) ?></div>
            <div class="filesize"><?= formatBytes($f['size']) ?></div>

            <div class="btn-group">
              <a href="<?= htmlspecialchars($f['path']) ?>" target="_blank" class="btn btn-view">
                👁 Lihat
              </a>
              <a href="<?= htmlspecialchars($f['path']) ?>" download class="btn btn-download">
                ⬇ Unduh
              </a>
              <button 
                class="btn btn-delete" 
                onclick="askDelete('<?= htmlspecialchars(addslashes($f['name'])) ?>', '<?= md5($f['name']) ?>')"
                title="Hapus file"
              >
                🗑
              </button>
            </div>
          </div>

        </div>

      <?php endforeach; ?>
    <?php endif; ?>

  </div>

</div>

<script>
let pendingFile = '';
let pendingHash = '';

function askDelete(filename, hash) {
  pendingFile = filename;
  pendingHash = hash;
  document.getElementById('deleteFileName').textContent = filename;
  document.getElementById('deleteOverlay').classList.add('active');
}

function closeDialog() {
  document.getElementById('deleteOverlay').classList.remove('active');
}

function confirmDelete() {
  closeDialog();

  fetch('hapus.php?file=' + encodeURIComponent(pendingFile), { method: 'GET' })
    .then(r => r.json())
    .then(data => {
      if(data.success) {
        const card = document.getElementById('card-' + pendingHash);
        if(card) {
          card.style.transition = 'opacity 0.4s, transform 0.4s';
          card.style.opacity = '0';
          card.style.transform = 'scale(0.9)';
          setTimeout(() => card.remove(), 400);
        }
        showToast('✅ File berhasil dihapus', 'success');
      } else {
        showToast('❌ Gagal menghapus file: ' + data.error, 'error');
      }
    })
    .catch(() => showToast('❌ Koneksi gagal', 'error'));
}

function showToast(msg, type) {
  const t = document.getElementById('toast');
  t.textContent = msg;
  t.style.display = 'block';
  t.style.borderColor = type === 'success' ? 'rgba(16,185,129,0.5)' : 'rgba(239,68,68,0.4)';
  t.style.color = type === 'success' ? '#6ee7b7' : '#fca5a5';
  setTimeout(() => { t.style.display = 'none'; }, 3500);
}

// Close dialog on overlay click
document.getElementById('deleteOverlay').addEventListener('click', function(e) {
  if(e.target === this) closeDialog();
});
</script>

</body>
</html>
