<div class="page-header">
    <div class="container">
        <h1>La commune</h1>
        <p>Découvrez l'histoire, la géographie et les particularités de Mégange</p>
    </div>
</div>

<div class="content-page">
    <div class="container">
        <div class="content-grid">
            <div class="content-main">
                <h2 id="histoire">Histoire</h2>
                <p>Mégange est un village au riche passé historique, typique des villages lorrains. Son nom évoque l'époque gallo-romaine, témoignant d'une occupation ancienne du territoire. Au fil des siècles, le village s'est développé autour de l'agriculture et de la vigne, conservant son authenticité et son caractère rural.</p>
                <p>L'église Saint-Martin, joyau du patrimoine local, domine le village et rappelle l'importance de la tradition dans notre commune.</p>

                <h2 id="geographie">Géographie</h2>
                <p>Situé à quelques kilomètres au nord-est de Metz, Mégange bénéficie d'une situation privilégiée entre ville et campagne. Le village est entouré de champs, de bois et de prairies, offrant un cadre de vie verdoyant et paisible.</p>
                <p>La commune fait partie du Pays Messin et de la Communauté de Communes du Sud Messin, ce qui permet aux habitants de bénéficier de nombreux services et infrastructures à proximité.</p>

                <h2 id="chiffres">Chiffres clés</h2>
                <ul>
                    <li><strong>Superficie :</strong> environ 5 km²</li>
                    <li><strong>Population :</strong> environ 300 habitants (Mégangeois et Mégangeoises)</li>
                    <li><strong>Altitude :</strong> ~250 mètres</li>
                    <li><strong>Communes limitrophes :</strong> Ancy-sur-Moselle, Dornot, Corny-sur-Moselle, Rezonville, Vionville</li>
                    <li><strong>Accès :</strong> à 15 minutes de Metz par l'A31</li>
                </ul>

                <h2 id="cadre">Cadre de vie</h2>
                <p>À Mégange, la qualité de vie est notre priorité. Le village dispose d'espaces verts entretenus, d'une salle polyvalente pour les animations locales, et de nombreux chemins propices aux balades. La proximité de la Moselle et du canal offre également de belles promenades le long de l'eau.</p>
            </div>

            <aside class="sidebar">
                <div class="sidebar-widget">
                    <h3>Mairie</h3>
                    <p><strong><?= $site_address ?></strong></p>
                    <p><i class="fas fa-phone"></i> <?= $site_phone ?></p>
                    <p><i class="fas fa-envelope"></i> <?= $site_email ?></p>
                </div>

                <div class="sidebar-widget">
                    <h3>Horaires d'ouverture</h3>
                    <ul class="hours-list">
                        <?php foreach ($mairie_hours as $day => $hours): ?>
                        <li><span class="day"><?= $day ?></span><span class="hours"><?= $hours ?></span></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            </aside>
        </div>
    </div>
</div>
