<?php
if (!$user_logged_in) {
    header('Location: index.php?p=login');
    exit;
}

// Déconnexion
if (isset($_GET['logout'])) {
    unset($_SESSION['user_id']);
    header('Location: index.php');
    exit;
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nom = trim($_POST['nom'] ?? '');
    $prenom = trim($_POST['prenom'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $adresse = trim($_POST['adresse'] ?? '');
    $code_postal = trim($_POST['code_postal'] ?? '');
    $ville = trim($_POST['ville'] ?? '');
    $telephone = trim($_POST['telephone'] ?? '');
    $accept_bulletin = isset($_POST['accept_bulletin']);
    $newPassword = $_POST['new_password'] ?? '';

    if ($nom === '' || $prenom === '' || $email === '') {
        $error = 'Le nom, le prénom et l\'email sont obligatoires.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Email invalide.';
    } elseif ($newPassword !== '' && strlen($newPassword) < 6) {
        $error = 'Le mot de passe doit faire au moins 6 caractères.';
    } else {
        $file = DATA_DIR . '/abonnes.json';
        $users = file_exists($file) ? (json_decode(file_get_contents($file), true) ?: []) : [];

        // Vérifie que l'email n'est pas déjà pris par un autre utilisateur
        foreach ($users as $u) {
            if ($u['id'] !== $current_user['id'] && strtolower($u['email'] ?? '') === strtolower($email)) {
                $error = 'Cet email est déjà utilisé par un autre compte.';
                break;
            }
        }

        if (!$error) {
            foreach ($users as &$u) {
                if ($u['id'] === $current_user['id']) {
                    $u['nom'] = $nom;
                    $u['prenom'] = $prenom;
                    $u['email'] = $email;
                    $u['adresse'] = $adresse;
                    $u['code_postal'] = $code_postal;
                    $u['ville'] = $ville;
                    $u['telephone'] = $telephone;
                    $u['accept_bulletin'] = $accept_bulletin;
                    if ($newPassword !== '') {
                        $u['password'] = password_hash($newPassword, PASSWORD_DEFAULT);
                    }
                    $current_user = $u;
                    break;
                }
            }
            unset($u);
            file_put_contents($file, json_encode($users, JSON_PRETTY_PRINT));
            $success = 'Profil mis à jour.';
        }
    }
}
?>
<section class="page-section">
    <div class="container" style="max-width:550px;">
        <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:0.5rem;">
            <h1 style="margin:0;">Mon compte</h1>
            <a href="index.php?p=mon-compte&logout=1" style="color:var(--terracotta);"><i class="fas fa-sign-out-alt"></i> Déconnexion</a>
        </div>
        <p style="color:var(--gray-400);margin-bottom:2rem;">Bienvenue, <?= htmlspecialchars($current_user['prenom']) ?>.</p>

        <?php if ($error): ?>
            <div style="background:#fef2f2;color:#b91c1c;padding:0.75rem 1rem;border-radius:var(--radius-sm);margin-bottom:1rem;"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>
        <?php if ($success): ?>
            <div style="background:var(--green-100);color:var(--green-700);padding:0.75rem 1rem;border-radius:var(--radius-sm);margin-bottom:1rem;"><?= htmlspecialchars($success) ?></div>
        <?php endif; ?>

        <form method="POST" style="background:var(--bg-card);border:1px solid var(--border);border-radius:var(--radius);padding:1.5rem;">
            <div class="form-group">
                <label for="nom">Nom <span style="color:var(--terracotta);">*</span></label>
                <input type="text" id="nom" name="nom" class="form-control" value="<?= htmlspecialchars($current_user['nom']) ?>" required>
            </div>
            <div class="form-group">
                <label for="prenom">Prénom <span style="color:var(--terracotta);">*</span></label>
                <input type="text" id="prenom" name="prenom" class="form-control" value="<?= htmlspecialchars($current_user['prenom']) ?>" required>
            </div>
            <div class="form-group">
                <label for="email">Email <span style="color:var(--terracotta);">*</span></label>
                <input type="email" id="email" name="email" class="form-control" value="<?= htmlspecialchars($current_user['email']) ?>" required>
            </div>
            <div class="form-group">
                <label for="adresse">Adresse</label>
                <input type="text" id="adresse" name="adresse" class="form-control" value="<?= htmlspecialchars($current_user['adresse'] ?? '') ?>">
            </div>
            <div class="form-group">
                <label for="code_postal">Code postal</label>
                <input type="text" id="code_postal" name="code_postal" class="form-control" value="<?= htmlspecialchars($current_user['code_postal'] ?? '') ?>">
            </div>
            <div class="form-group">
                <label for="ville">Ville</label>
                <input type="text" id="ville" name="ville" class="form-control" value="<?= htmlspecialchars($current_user['ville'] ?? '') ?>">
            </div>
            <div class="form-group">
                <label for="telephone">Téléphone</label>
                <input type="tel" id="telephone" name="telephone" class="form-control" value="<?= htmlspecialchars($current_user['telephone'] ?? '') ?>">
            </div>
            <div class="form-group">
                <label for="new_password">Nouveau mot de passe (laisser vide pour conserver l'actuel)</label>
                <input type="password" id="new_password" name="new_password" class="form-control" minlength="6">
            </div>
            <div class="form-group" style="display:flex;align-items:center;gap:0.5rem;">
                <input type="checkbox" id="accept_bulletin" name="accept_bulletin" value="1" style="width:1.1rem;height:1.1rem;"<?= ($current_user['accept_bulletin'] ?? false) ? ' checked' : '' ?>>
                <label for="accept_bulletin" style="margin:0;font-weight:400;">J'accepte de recevoir le bulletin communal par email</label>
            </div>
            <button type="submit" class="btn btn-primary">Enregistrer</button>
        </form>
    </div>
</section>
