<div class="page-header">
    <div class="container">
        <h1>Vie municipale</h1>
        <p>Le conseil municipal, les élus et la vie démocratique de la commune</p>
    </div>
</div>

<div class="content-page">
    <div class="container">
        <div class="content-grid">
            <div class="content-main">
                <h2>Le conseil municipal</h2>
                <p>Le conseil municipal de Mégange est composé d'élus dévoués au service de la commune et de ses habitants. Il se réunit régulièrement pour discuter et voter les décisions qui façonnent l'avenir du village.</p>
                <p>Les séances du conseil municipal sont publiques. Vous êtes invités à y assister pour suivre la vie démocratique de votre commune.</p>

                <h2>L'équipe municipale</h2>
                <div class="team-grid">
                    <?php foreach ($municipal_team as $member): ?>
                    <div class="team-card">
                        <div class="team-avatar"><i class="fas fa-user"></i></div>
                        <h3><?= $member['name'] ?></h3>
                        <p class="role"><?= $member['role'] ?></p>
                        <?php if (!empty($member['delegation'])): ?>
                        <p class="delegation"><?= $member['delegation'] ?></p>
                        <?php endif; ?>
                    </div>
                    <?php endforeach; ?>
                </div>

                <h2>Les comptes-rendus</h2>
                <p>Les comptes-rendus des conseils municipaux sont disponibles en mairie et peuvent être consultés sur demande. Ils retracent les délibérations et les décisions prises pour la gestion de la commune.</p>

                <h2>Budget communal</h2>
                <p>La transparence budgétaire est une priorité. Le budget de la commune est voté chaque année par le conseil municipal et peut être consulté par tout citoyen qui en fait la demande en mairie.</p>
            </div>

            <aside class="sidebar">
                <div class="sidebar-widget">
                    <h3>Prochain conseil</h3>
                    <p><i class="fas fa-calendar"></i> Prochaine séance : <strong>12 juillet 2026</strong></p>
                    <p><i class="fas fa-clock"></i> 20h00 - Salle du conseil</p>
                </div>

                <div class="sidebar-widget">
                    <h3>Vos élus</h3>
                    <p>Nombre de conseillers : <strong>11</strong></p>
                    <p>Majorité : <strong>Sortie</strong></p>
                    <p>Prochaine élection : <strong>2026</strong></p>
                </div>
            </aside>
        </div>
    </div>
</div>
