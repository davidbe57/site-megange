<?php
session_start();
require_once __DIR__ . '/../config.php';
if (empty($_SESSION['admin'])) { header('Location: index.php'); exit; }

$file = DATA_DIR . '/comptes_rendus.json';
$items = file_exists($file) ? (json_decode(file_get_contents($file), true) ?: []) : [];
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Comptes-rendus | Administration</title>
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
        .empty-state { text-align: center; padding: 3rem; color: var(--gray-400); }
        .toast { display: none; background: var(--green-100); color: var(--green-700); padding: 1rem 1.5rem; border-radius: var(--radius-sm); margin-bottom: 1.5rem; font-weight: 600; }
        .toast.show { display: block; }
        .thumb-preview { width: 60px; height: 80px; object-fit: cover; border-radius: 4px; background: var(--gray-100); }
    </style>
</head>
<body>
    <div class="admin-bar">
        <div class="container" style="display:flex;justify-content:space-between;align-items:center;">
            <span><i class="fas fa-shield-alt"></i> Administration — Comptes-rendus</span>
            <div>
                <a href="index.php"><i class="fas fa-arrow-left"></i> Tableau de bord</a>
                <a href="../index.php" style="margin-left:1rem;"><i class="fas fa-arrow-left"></i> Retour au site</a>
            </div>
        </div>
    </div>

    <main style="padding: 2rem 0;">
        <div class="container">
            <?php if (isset($_GET['created'])): ?><div class="toast show">Compte-rendu ajouté</div><?php endif; ?>
            <?php if (isset($_GET['updated'])): ?><div class="toast show">Compte-rendu modifié</div><?php endif; ?>
            <?php if (isset($_GET['deleted'])): ?><div class="toast show">Compte-rendu supprimé</div><?php endif; ?>

            <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:2rem;">
                <h2>Comptes-rendus</h2>
                <a href="cr_create.php" class="btn btn-primary"><i class="fas fa-plus"></i> Ajouter</a>
            </div>

            <?php if (empty($items)): ?>
                <div class="empty-state">
                    <i class="fas fa-file-pdf" style="font-size:3rem;margin-bottom:1rem;"></i>
                    <p>Aucun compte-rendu.</p>
                    <a href="cr_create.php" class="btn btn-primary" style="margin-top:1rem;">Ajouter le premier</a>
                </div>
            <?php else: ?>
                <table class="admin-table">
                    <thead>
                        <tr><th>Titre</th><th>Date</th><th>Fichier</th><th>Actions</th></tr>
                    </thead>
                    <tbody>
                        <?php foreach (array_reverse($items) as $cr): ?>
                        <tr>
                            <td><strong><?= htmlspecialchars($cr['title']) ?></strong></td>
                            <td><?= date('d/m/Y', strtotime($cr['date'])) ?></td>
                            <td><a href="../<?= htmlspecialchars($cr['file']) ?>" target="_blank"><i class="fas fa-file-pdf"></i> PDF</a></td>
                            <td>
                                <div class="admin-actions">
                                    <a href="cr_edit.php?id=<?= $cr['id'] ?>" style="background:var(--green-100);color:var(--green-700);"><i class="fas fa-edit"></i> Modifier</a>
                                    <a href="cr_delete.php?id=<?= $cr['id'] ?>" style="background:#fef2f2;color:#b91c1c;" onclick="return confirm('Supprimer ce compte-rendu ?')"><i class="fas fa-trash"></i> Supprimer</a>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>
    </main>
</body>
</html>
