<?php
$error = '';

if ($user_logged_in) {
    header('Location: index.php?p=mon-compte');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($email === '' || $password === '') {
        $error = 'Veuillez remplir tous les champs.';
    } else {
        $file = DATA_DIR . '/abonnes.json';
        $users = file_exists($file) ? (json_decode(file_get_contents($file), true) ?: []) : [];

        foreach ($users as $u) {
            if (strtolower($u['email'] ?? '') === strtolower($email) && !empty($u['password'])) {
                if (password_verify($password, $u['password'])) {
                    $_SESSION['user_id'] = $u['id'];
                    header('Location: index.php?p=mon-compte');
                    exit;
                }
            }
        }
        $error = 'Email ou mot de passe incorrect.';
    }
}
?>
<section class="page-section">
    <div class="container" style="max-width:450px;">
        <h1 style="margin-bottom:0.5rem;">Connexion</h1>
        <p style="color:var(--gray-400);margin-bottom:2rem;">Connectez-vous à votre espace personnel.</p>

        <?php if ($error): ?>
            <div style="background:#fef2f2;color:#b91c1c;padding:0.75rem 1rem;border-radius:var(--radius-sm);margin-bottom:1rem;"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <form method="POST" style="background:var(--bg-card);border:1px solid var(--border);border-radius:var(--radius);padding:1.5rem;">
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" class="form-control" value="<?= htmlspecialchars($_POST['email'] ?? '') ?>" required autofocus>
            </div>
            <div class="form-group">
                <label for="password">Mot de passe</label>
                <input type="password" id="password" name="password" class="form-control" required>
            </div>
            <button type="submit" class="btn btn-primary" style="width:100%;">Se connecter</button>
        </form>

        <p style="text-align:center;margin-top:1.5rem;">
            Pas encore de compte ? <a href="index.php?p=register">Inscrivez-vous</a>
        </p>

        <p style="text-align:center;margin-top:0.75rem;font-size:0.85rem;">
            <a href="index.php">Retour à l'accueil</a>
        </p>
    </div>
</section>
