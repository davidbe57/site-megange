<div class="page-header">
    <div class="container">
        <h1>Contact</h1>
        <p>Comment nous joindre et nous trouver</p>
    </div>
</div>

<div class="content-page">
    <div class="container">
        <div class="contact-grid">
            <div>
                <h2>Nous écrire</h2>
                <p>Vous avez une question, une suggestion ou besoin d'information ? N'hésitez pas à nous contacter via ce formulaire.</p>

                <form method="POST" action="index.php?p=contact" class="contact-form">
                    <?php
                    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                        $name  = trim($_POST['name'] ?? '');
                        $email = trim($_POST['email'] ?? '');
                        $subj  = $_POST['subject'] ?? '';
                        $msg   = trim($_POST['message'] ?? '');
                        $subjects = [
                            'etat-civil'   => 'État civil',
                            'urbanisme'    => 'Urbanisme / Permis de construire',
                            'associations' => 'Vie associative',
                            'evenement'    => 'Proposer un événement',
                            'autre'        => 'Autre demande',
                        ];
                        $subjectLabel = $subjects[$subj] ?? 'Contact';
                        $headers = 'From: ' . $email . "\r\n" .
                                   'Reply-To: ' . $email . "\r\n" .
                                   'Content-Type: text/plain; charset=utf-8' . "\r\n" .
                                   'X-Mailer: PHP/' . phpversion();
                        $body = "Nom : $name\n"
                              . "Email : $email\n"
                              . "Sujet : $subjectLabel\n\n"
                              . "Message :\n$msg";
                        if (@mail($contact_email, '[Mégange] ' . $subjectLabel, $body, $headers)) {
                            echo '<div class="form-success" style="display: block;">Merci pour votre message ! Nous vous répondrons dans les plus brefs délais.</div>';
                        } else {
                            echo '<div class="form-success" style="display: block; background: #fde8e8; color: #c00;">Erreur lors de l\'envoi. Veuillez réessayer ou nous contacter par téléphone.</div>';
                        }
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
                            <option value="etat-civil">État civil</option>
                            <option value="urbanisme">Urbanisme / Permis de construire</option>
                            <option value="associations">Vie associative</option>
                            <option value="evenement">Proposer un événement</option>
                            <option value="autre">Autre demande</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="message">Message</label>
                        <textarea id="message" name="message" class="form-control" required></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary"><i class="fas fa-paper-plane"></i> Envoyer</button>
                </form>
            </div>

            <div>
                <h2>Coordonnées</h2>

                <div class="contact-info-item">
                    <div class="icon"><i class="fas fa-location-dot"></i></div>
                    <div>
                        <h4>Adresse</h4>
                        <p><?= $site_address ?></p>
                    </div>
                </div>

                <div class="contact-info-item">
                    <div class="icon"><i class="fas fa-phone"></i></div>
                    <div>
                        <h4>Téléphone</h4>
                        <p><a href="tel:<?= $site_phone ?>"><?= $site_phone ?></a></p>
                    </div>
                </div>

                <div class="contact-info-item">
                    <div class="icon"><i class="fas fa-envelope"></i></div>
                    <div>
                        <h4>Email</h4>
                        <p><a href="mailto:<?= $site_email ?>"><?= $site_email ?></a></p>
                    </div>
                </div>

                <div class="contact-info-item">
                    <div class="icon"><i class="fas fa-clock"></i></div>
                    <div>
                        <h4>Horaires d'ouverture</h4>
                        <ul class="hours-list">
                            <?php foreach ($mairie_hours as $day => $hours): ?>
                            <li><span class="day"><?= $day ?></span><span class="hours"><?= $hours ?></span></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                </div>

                <div class="map-container" style="margin-top: 2rem;">
                    <p style="color: var(--green-700);"><i class="fas fa-map"></i> Carte interactive bientôt disponible</p>
                </div>
            </div>
        </div>
    </div>
</div>
