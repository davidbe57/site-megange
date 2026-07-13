<?php
session_start();
require_once __DIR__ . '/../config.php';

if (empty($_SESSION['admin'])) {
    header('Location: index.php');
    exit;
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title   = trim($_POST['title'] ?? '');
    $author  = trim($_POST['author'] ?? '');
    $date    = trim($_POST['date'] ?? '');
    $content = trim($_POST['content'] ?? '');
    $image   = '';

    if (empty($title) || empty($author) || empty($date) || empty($content)) {
        $error = 'Tous les champs sont obligatoires.';
    } else {
        // Handle image upload
        if (!empty($_FILES['image']['name']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            $ext = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
            $allowed = ['jpg', 'jpeg', 'png', 'webp'];
            if (in_array($ext, $allowed)) {
                $filename = 'blog_' . time() . '_' . bin2hex(random_bytes(4)) . '.' . $ext;
                $dest = UPLOADS_DIR . '/blog/' . $filename;
                move_uploaded_file($_FILES['image']['tmp_name'], $dest);
                $image = 'serve.php?f=blog/' . $filename;
            } else {
                $error = 'Format d\'image non accepté (jpg, png, webp uniquement).';
            }
        }

        if (empty($error)) {
            $articles = [];
            $art_file = DATA_DIR . '/articles.json';
            if (file_exists($art_file)) {
                $articles = json_decode(file_get_contents($art_file), true) ?: [];
            }

            $max_id = 0;
            foreach ($articles as $a) {
                if ($a['id'] > $max_id) $max_id = $a['id'];
            }

            $plain = strip_tags($content);
            $excerpt = mb_strlen($plain) > 200 ? mb_substr($plain, 0, 200) . '...' : $plain;

            $articles[] = [
                'id'      => $max_id + 1,
                'title'   => $title,
                'date'    => $date,
                'author'  => $author,
                'image'   => $image,
                'content' => $content,
                'excerpt' => $excerpt,
            ];

            file_put_contents($art_file, json_encode($articles, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
            header('Location: index.php?created=1');
            exit;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nouvel article | Administration</title>
    <link rel="stylesheet" href="../assets/fonts/fontawesome.min.css">
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <div class="admin-bar" style="background:var(--green-900);color:white;padding:0.75rem 0;font-size:0.9rem;">
        <div class="container" style="display:flex;justify-content:space-between;align-items:center;">
            <span><i class="fas fa-plus"></i> Nouvel article</span>
            <a href="index.php" style="color:var(--gold);"><i class="fas fa-arrow-left"></i> Retour</a>
        </div>
    </div>

    <main style="padding: 2rem 0;">
        <div class="container" style="max-width: 800px;">
            <h2 style="margin-bottom: 1.5rem;">Créer un article</h2>

            <?php if ($error): ?>
                <div style="background:#fef2f2;color:#b91c1c;padding:0.75rem 1rem;border-radius:var(--radius-sm);margin-bottom:1rem;"><?= $error ?></div>
            <?php endif; ?>

            <form method="POST" enctype="multipart/form-data">
                <div class="form-group">
                    <label for="title">Titre de l'article</label>
                    <input type="text" id="title" name="title" class="form-control" required value="<?= htmlspecialchars($_POST['title'] ?? '') ?>">
                </div>

                <div style="display:grid;grid-template-columns:1fr 1fr;gap:1rem;">
                    <div class="form-group">
                        <label for="author">Auteur</label>
                        <input type="text" id="author" name="author" class="form-control" required value="<?= htmlspecialchars($_POST['author'] ?? '') ?>">
                    </div>
                    <div class="form-group">
                        <label for="date">Date</label>
                        <input type="date" id="date" name="date" class="form-control" required value="<?= htmlspecialchars($_POST['date'] ?? date('Y-m-d')) ?>">
                    </div>
                </div>

                <div class="form-group">
                    <label for="image">Photo (optionnelle, jpg/png/webp)</label>
                    <input type="file" id="image" name="image" class="form-control" accept=".jpg,.jpeg,.png,.webp">
                </div>

                <div class="form-group">
                    <label>Texte de l'article</label>
                    <div class="editor-toolbar">
                        <button type="button" onclick="wrapTag('b')" title="Gras"><i class="fas fa-bold"></i></button>
                        <button type="button" onclick="wrapTag('i')" title="Italique"><i class="fas fa-italic"></i></button>
                        <button type="button" onclick="wrapTag('u')" title="Souligné"><i class="fas fa-underline"></i></button>
                        <button type="button" onclick="insertLink()" title="Lien"><i class="fas fa-link"></i></button>
                    </div>
                    <textarea id="content" name="content" class="form-control" required style="min-height:250px;"><?= htmlspecialchars($_POST['content'] ?? '') ?></textarea>
                    <p style="font-size:0.8rem;color:var(--gray-400);margin-top:0.3rem;">Vous pouvez utiliser le HTML directement : <code>&lt;b&gt;</code>, <code>&lt;i&gt;</code>, <code>&lt;a href="..."&gt;</code></p>
                </div>

                <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Publier l'article</button>
                <a href="index.php" class="btn btn-outline" style="margin-left:0.5rem;">Annuler</a>
                <script>
                function wrapTag(tag) {
                    const ta = document.getElementById('content');
                    const start = ta.selectionStart, end = ta.selectionEnd;
                    const sel = ta.value.substring(start, end);
                    const def = tag === 'a' ? '' : 'texte';
                    const txt = sel || def;
                    const replacement = tag === 'a' ? '<a href="">' + txt + '</a>' : '<' + tag + '>' + txt + '</' + tag + '>';
                    ta.value = ta.value.substring(0, start) + replacement + ta.value.substring(end);
                    ta.focus();
                    if (!sel) ta.setSelectionRange(start + ('<' + tag + '>').length, start + ('<' + tag + '>').length);
                }
                function insertLink() {
                    const ta = document.getElementById('content');
                    const start = ta.selectionStart, end = ta.selectionEnd;
                    const sel = ta.value.substring(start, end);
                    const url = prompt('URL du lien :', 'https://');
                    if (!url) return;
                    const text = sel || 'lien';
                    const replacement = '<a href="' + url.replace(/"/g,'&quot;') + '">' + text + '</a>';
                    ta.value = ta.value.substring(0, start) + replacement + ta.value.substring(end);
                    ta.focus();
                }
                </script>
            </form>
        </div>
    </main>
</body>
</html>
