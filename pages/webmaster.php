<div class="page-header">
    <div class="container">
        <h1>Contacter le webmaster</h1>
        <p>Signaler un problème ou suggérer une amélioration</p>
    </div>
</div>

<div class="content-page">
    <div class="container">
        <div class="content-grid">
            <div class="content-main">
                <form method="POST" action="index.php?p=webmaster" class="contact-form">
                    <?php
                    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                        $name  = trim($_POST['name'] ?? '');
                        $email = trim($_POST['email'] ?? '');
                        $subj  = $_POST['subject'] ?? '';
                        $msg   = trim($_POST['message'] ?? '');
                        $subjects = [
                            'bug'        => 'Problème technique / Bug',
                            'amelioration' => 'Suggestion d\'amélioration',
                            'contenu'    => 'Mise à jour de contenu',
                            'signalement' => 'Signalement (photo, information erronée)',
                            'autre'      => 'Autre',
                        ];
                        $subjectLabel = $subjects[$subj] ?? 'Contact webmaster';
                        $entry = [
                            'date'    => date('Y-m-d H:i:s'),
                            'name'    => $name,
                            'email'   => $email,
                            'subject' => $subjectLabel,
                            'message' => $msg,
                        ];
                        $file = DATA_DIR . '/webmaster_messages.json';
                        $msgs = file_exists($file) ? (json_decode(file_get_contents($file), true) ?: []) : [];
                        array_unshift($msgs, $entry);
                        file_put_contents($file, json_encode($msgs, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
                        $body = "Nom : $name\n"
                              . "Email : $email\n"
                              . "Sujet : $subjectLabel\n\n"
                              . "Message :\n$msg";
                        sendMail('david.better@gmail.com', '[Webmaster Mégange] ' . $subjectLabel, $body, $email);
                        echo '<div class="form-success" style="display: block;">Merci pour votre message ! Le webmaster vous répondra dans les plus brefs délais.</div>';
                    }
                    ?>
                    <div class="form-group">
                        <label for="name">Nom et prénom</label>
                        <input type="text" id="name" name="name" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label for="email">Email</label>
                        <input type="email" id="email" name="email" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label for="subject">Sujet</label>
                        <select id="subject" name="subject" class="form-control" required>
                            <option value="">Choisissez un sujet</option>
                            <option value="bug">Problème technique / Bug</option>
                            <option value="amelioration">Suggestion d'amélioration</option>
                            <option value="contenu">Mise à jour de contenu</option>
                            <option value="signalement">Signalement (photo, information erronée)</option>
                            <option value="autre">Autre</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="message">Message</label>
                        <textarea id="message" name="message" class="form-control" required></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary"><i class="fas fa-paper-plane"></i> Envoyer</button>
                </form>
            </div>

            <aside class="sidebar">
                <div class="sidebar-widget">
                    <h3>À propos</h3>
                    <p>Utilisez ce formulaire pour signaler un problème technique, suggérer une amélioration ou demander une mise à jour du contenu du site.</p>
                </div>
            </aside>
        </div>
    </div>
</div>
