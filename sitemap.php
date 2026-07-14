<?php
require_once __DIR__ . '/config.php';
header('Content-Type: application/xml; charset=utf-8');
?>
<?xml version="1.0" encoding="UTF-8"?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
  <url>
    <loc><?= $site_url ?>/</loc>
    <priority>1.0</priority>
    <changefreq>weekly</changefreq>
  </url>
  <url>
    <loc><?= $site_url ?>/index.php?p=la-commune</loc>
    <priority>0.8</priority>
    <changefreq>monthly</changefreq>
  </url>
  <url>
    <loc><?= $site_url ?>/index.php?p=vie-municipale</loc>
    <priority>0.8</priority>
    <changefreq>monthly</changefreq>
  </url>
  <url>
    <loc><?= $site_url ?>/index.php?p=services</loc>
    <priority>0.7</priority>
    <changefreq>monthly</changefreq>
  </url>
  <url>
    <loc><?= $site_url ?>/index.php?p=vie-locale</loc>
    <priority>0.8</priority>
    <changefreq>weekly</changefreq>
  </url>
  <url>
    <loc><?= $site_url ?>/index.php?p=contact</loc>
    <priority>0.6</priority>
    <changefreq>yearly</changefreq>
  </url>
  <url>
    <loc><?= $site_url ?>/index.php?p=galerie</loc>
    <priority>0.5</priority>
    <changefreq>monthly</changefreq>
  </url>
</urlset>
