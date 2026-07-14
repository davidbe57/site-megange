<?php
session_start();
require_once __DIR__ . '/../config.php';

// Logout
if (isset($_GET['logout'])) {
    session_destroy();
    header('Location: index.php');
    exit;
}

// Login
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['password'])) {
    if ($_POST['password'] === $admin_password) {
        $_SESSION['admin'] = true;
    } else {
        $error = 'Mot de passe incorrect';
    }
}

$logged_in = !empty($_SESSION['admin']);

// Load articles
$articles = [];
$art_file = DATA_DIR . '/articles.json';
if (file_exists($art_file)) {
    $articles = json_decode(file_get_contents($art_file), true) ?: [];
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Administration | Mégange</title>
    <link rel="stylesheet" href="../assets/fonts/fontawesome.min.css">
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        .admin-bar { background: var(--green-900); color: white; padding: 0.75rem 0; font-size: 0.9rem; }
        .admin-bar a { color: var(--gold); }
        .admin-table { width: 100%; border-collapse: collapse; background: var(--bg-card); border-radius: var(--radius); overflow: hidden; }
        .admin-table th, .admin-table td { padding: 0.75rem 1rem; text-align: left; border-bottom: 1px solid var(--border); }
        .admin-table th { background: var(--green-100); font-weight: 600; }
        .admin-table tr:hover { background: var(--gray-100); }
        .admin-actions { display: flex; gap: 0.5rem; }
        .admin-actions a { padding: 0.4rem 0.75rem; border-radius: var(--radius-sm); font-size: 0.85rem; text-decoration: none; }
        .login-box { max-width: 400px; margin: 4rem auto; }
        .empty-state { text-align: center; padding: 3rem; color: var(--gray-400); }
        .toast { display: none; background: var(--green-100); color: var(--green-700); padding: 1rem 1.5rem; border-radius: var(--radius-sm); margin-bottom: 1.5rem; font-weight: 600; }
        .toast.show { display: block; }
    </style>
</head>
<body>
    <div class="admin-bar">
        <div class="container" style="display:flex;justify-content:space-between;align-items:center;">
            <span><i class="fas fa-shield-alt"></i> Administration du site de Mégange</span>
            <div>
                <?php if ($logged_in): ?>
                <a href="index.php?logout=1"><i class="fas fa-sign-out-alt"></i> Déconnexion</a>
                <?php endif; ?>
                <a href="../index.php" style="margin-left:1rem;"><i class="fas fa-arrow-left"></i> Retour au site</a>
            </div>
        </div>
    </div>

    <main style="padding: 2rem 0;">
        <div class="container">
            <?php if (!$logged_in): ?>
                <div class="login-box">
                    <h2 style="margin-bottom:1.5rem;">Connexion</h2>
                    <?php if ($error): ?>
                        <div style="background:#fef2f2;color:#b91c1c;padding:0.75rem 1rem;border-radius:var(--radius-sm);margin-bottom:1rem;"><?= $error ?></div>
                    <?php endif; ?>
                    <form method="POST">
                        <div class="form-group">
                            <label for="password">Mot de passe</label>
                            <input type="password" id="password" name="password" class="form-control" required autofocus>
                        </div>
                        <button type="submit" class="btn btn-primary"><i class="fas fa-lock"></i> Se connecter</button>
                    </form>
                </div>
            <?php else: ?>
                <?php if (isset($_GET['created'])): ?>
                    <div class="toast show">Article créé avec succès</div>
                <?php endif; ?>
                <?php if (isset($_GET['updated'])): ?>
                    <div class="toast show">Article modifié avec succès</div>
                <?php endif; ?>
                <?php if (isset($_GET['deleted'])): ?>
                    <div class="toast show">Article supprimé</div>
                <?php endif; ?>

                <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:2rem;">
                    <h2>Blog - Vie locale</h2>
                    <a href="create.php" class="btn btn-primary"><i class="fas fa-plus"></i> Nouvel article</a>
                </div>

                <?php if (empty($articles)): ?>
                    <div class="empty-state">
                        <i class="fas fa-newspaper" style="font-size:3rem;margin-bottom:1rem;"></i>
                        <p>Aucun article pour le moment.</p>
                        <a href="create.php" class="btn btn-primary" style="margin-top:1rem;">Créer le premier article</a>
                    </div>
                <?php else: ?>
                    <table class="admin-table">
                        <thead>
                            <tr>
                                <th>Titre</th>
                                <th>Auteur</th>
                                <th>Date</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach (array_reverse($articles) as $art): ?>
                            <tr>
                                <td><strong><?= htmlspecialchars($art['title']) ?></strong></td>
                                <td><?= htmlspecialchars($art['author']) ?></td>
                                <td><?= date('d/m/Y', strtotime($art['date'])) ?></td>
                                <td>
                                    <div class="admin-actions">
                                        <a href="edit.php?id=<?= $art['id'] ?>" style="background:var(--green-100);color:var(--green-700);">
                                            <i class="fas fa-edit"></i> Modifier
                                        </a>
                                        <a href="delete.php?id=<?= $art['id'] ?>" style="background:#fef2f2;color:#b91c1c;" onclick="return confirm('Supprimer cet article ?')">
                                            <i class="fas fa-trash"></i> Supprimer
                                        </a>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php endif; ?>

                <div style="margin-top:3rem;padding-top:2rem;border-top:2px solid var(--border);">
                    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:1.5rem;">
                        <h2>Comptes-rendus</h2>
                        <a href="cr_create.php" class="btn btn-primary"><i class="fas fa-plus"></i> Ajouter</a>
                    </div>
                    <p style="color:var(--gray-400);"><a href="comptes_rendus.php">Gérer les comptes-rendus du conseil municipal</a></p>
                </div>

                <div style="margin-top:2rem;padding-top:2rem;border-top:2px solid var(--border);">
                    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:1.5rem;">
                        <h2>Bulletins communaux</h2>
                        <a href="bulletin_create.php" class="btn btn-primary"><i class="fas fa-plus"></i> Ajouter</a>
                    </div>
                    <p style="color:var(--gray-400);"><a href="bulletins.php">Gérer les bulletins municipaux d'information</a></p>
                </div>

                <div style="margin-top:2rem;padding-top:2rem;border-top:2px solid var(--border);">
                    <h2 style="margin-bottom:1rem;">Configuration</h2>
                    <div style="display:flex;gap:1rem;flex-wrap:wrap;">
                        <a href="carousel.php" class="btn"><i class="fas fa-images"></i> Carousel accueil</a>
                        <a href="galerie.php" class="btn"><i class="fas fa-camera"></i> Galerie photos</a>
                        <a href="horaires.php" class="btn"><i class="fas fa-clock"></i> Horaires d'ouverture</a>
                        <a href="config-site.php" class="btn"><i class="fas fa-gear"></i> Coordonnées mairie</a>
                    </div>
                </div>

                <div style="margin-top:2rem;padding-top:2rem;border-top:2px solid var(--border);">
                    <h2 style="margin-bottom:1rem;">Vie municipale</h2>
                    <div style="display:flex;gap:1rem;flex-wrap:wrap;">
                        <a href="conseil.php" class="btn"><i class="fas fa-calendar"></i> Conseil & sidebar</a>
                        <a href="elus.php" class="btn"><i class="fas fa-users"></i> Équipe municipale</a>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </main>
</body>
</html>
