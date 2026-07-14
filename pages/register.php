<?php
$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nom = trim($_POST['nom'] ?? '');
    $prenom = trim($_POST['prenom'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $adresse = trim($_POST['adresse'] ?? '');
    $code_postal = trim($_POST['code_postal'] ?? '');
    $ville = trim($_POST['ville'] ?? '');
    $telephone = trim($_POST['telephone'] ?? '');
    $accept_bulletin = isset($_POST['accept_bulletin']);

    if ($nom === '' || $prenom === '' || $email === '' || $password === '') {
        $error = 'Le nom, le prénom, l\'email et le mot de passe sont obligatoires.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Email invalide.';
    } elseif (strlen($password) < 6) {
        $error = 'Le mot de passe doit faire au moins 6 caractères.';
    } else {
        $file = DATA_DIR . '/abonnes.json';
        $users = file_exists($file) ? (json_decode(file_get_contents($file), true) ?: []) : [];

        foreach ($users as $u) {
            if (strtolower($u['email'] ?? '') === strtolower($email)) {
                $error = 'Cet email est déjà utilisé.';
                break;
            }
        }

        if (!$error) {
            $users[] = [
                'id' => count($users) ? max(array_column($users, 'id')) + 1 : 1,
                'nom' => $nom,
                'prenom' => $prenom,
                'email' => $email,
                'password' => password_hash($password, PASSWORD_DEFAULT),
                'adresse' => $adresse,
                'code_postal' => $code_postal,
                'ville' => $ville,
                'telephone' => $telephone,
                'accept_bulletin' => $accept_bulletin,
            ];
            file_put_contents($file, json_encode($users, JSON_PRETTY_PRINT));
            $success = 'Inscription réussie ! Vous pouvez maintenant vous connecter.';
        }
    }
}
?>
<section class="page-section">
    <div class="container" style="max-width:500px;">
        <h1 style="margin-bottom:0.5rem;">Créer un compte</h1>
        <p style="color:var(--gray-400);margin-bottom:2rem;">Créez votre compte pour suivre l'actualité de la commune.</p>

        <?php if ($error): ?>
            <div style="background:#fef2f2;color:#b91c1c;padding:0.75rem 1rem;border-radius:var(--radius-sm);margin-bottom:1rem;"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>
        <?php if ($success): ?>
            <div style="background:var(--green-100);color:var(--green-700);padding:1rem;border-radius:var(--radius-sm);margin-bottom:1rem;"><?= htmlspecialchars($success) ?></div>
        <?php endif; ?>

        <form method="POST" style="background:var(--bg-card);border:1px solid var(--border);border-radius:var(--radius);padding:1.5rem;">
            <div class="form-group">
                <label for="nom">Nom <span style="color:var(--terracotta);">*</span></label>
                <input type="text" id="nom" name="nom" class="form-control" value="<?= htmlspecialchars($_POST['nom'] ?? '') ?>" required>
            </div>
            <div class="form-group">
                <label for="prenom">Prénom <span style="color:var(--terracotta);">*</span></label>
                <input type="text" id="prenom" name="prenom" class="form-control" value="<?= htmlspecialchars($_POST['prenom'] ?? '') ?>" required>
            </div>
            <div class="form-group">
                <label for="email">Email <span style="color:var(--terracotta);">*</span></label>
                <input type="email" id="email" name="email" class="form-control" value="<?= htmlspecialchars($_POST['email'] ?? '') ?>" required>
            </div>
            <div class="form-group">
                <label for="password">Mot de passe <span style="color:var(--terracotta);">*</span></label>
                <input type="password" id="password" name="password" class="form-control" required minlength="6">
            </div>
            <div class="form-group">
                <label for="adresse">Adresse</label>
                <input type="text" id="adresse" name="adresse" class="form-control" value="<?= htmlspecialchars($_POST['adresse'] ?? '') ?>">
            </div>
            <div class="form-group">
                <label for="code_postal">Code postal</label>
                <input type="text" id="code_postal" name="code_postal" class="form-control" value="<?= htmlspecialchars($_POST['code_postal'] ?? '') ?>">
            </div>
            <div class="form-group">
                <label for="ville">Ville</label>
                <input type="text" id="ville" name="ville" class="form-control" value="<?= htmlspecialchars($_POST['ville'] ?? '') ?>">
            </div>
            <div class="form-group">
                <label for="telephone">Téléphone</label>
                <input type="tel" id="telephone" name="telephone" class="form-control" value="<?= htmlspecialchars($_POST['telephone'] ?? '') ?>">
            </div>
            <div class="form-group" style="display:flex;align-items:center;gap:0.5rem;">
                <input type="checkbox" id="accept_bulletin" name="accept_bulletin" value="1" style="width:1.1rem;height:1.1rem;"<?= !empty($_POST['accept_bulletin']) ? ' checked' : '' ?>>
                <label for="accept_bulletin" style="margin:0;font-weight:400;">J'accepte de recevoir le bulletin communal par email</label>
            </div>
            <button type="submit" class="btn btn-primary" style="width:100%;">Créer mon compte</button>
        </form>

        <p style="text-align:center;margin-top:1.5rem;">
            Déjà un compte ? <a href="index.php?p=login">Connectez-vous</a>
        </p>
    </div>
</section>
