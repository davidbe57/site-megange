<?php
function escapePdf($s) {
    return str_replace(['(', ')', '\\'], ['\\(', '\\)', '\\\\'], $s);
}

function createDummyPdf($title, $date, $content) {
    $pdfDir = __DIR__ . '/../assets/pdf/';
    $name = 'cr_dummy_' . bin2hex(random_bytes(4)) . '.pdf';
    $path = $pdfDir . $name;

    $lines = explode("\n", wordwrap(wordwrap($content, 80, "\n", true), 80, "\n", true));
    $stream = '';
    $y = 700;
    $stream .= "BT /F1 24 Tf 100 $y Td (" . escapePdf($title) . ") Tj ET\n";
    $y -= 40;
    $stream .= "BT /F2 12 Tf 100 $y Td (" . escapePdf($date) . ") Tj ET\n";
    $y -= 30;
    foreach ($lines as $line) {
        $stream .= "BT /F2 11 Tf 100 $y Td (" . escapePdf($line) . ") Tj ET\n";
        $y -= 18;
        if ($y < 50) break;
    }
    $streamLen = strlen($stream);

    $pdf = "%PDF-1.4\n"
        . "1 0 obj\n<</Type/Catalog/Pages 2 0 R>>\nendobj\n"
        . "2 0 obj\n<</Type/Pages/Kids[3 0 R]/Count 1>>\nendobj\n"
        . "3 0 obj\n<</Type/Page/Parent 2 0 R/MediaBox[0 0 612 792]/Contents 4 0 R/Resources<</Font<</F1 5 0 R/F2 6 0 R>>>>>>\nendobj\n"
        . "4 0 obj\n<</Length $streamLen>>\nstream\n$stream\nendstream\nendobj\n"
        . "5 0 obj\n<</Type/Font/Subtype/Type1/BaseFont/Helvetica>>\nendobj\n"
        . "6 0 obj\n<</Type/Font/Subtype/Type1/BaseFont/Helvetica>>\nendobj\n"
        . "xref\n0 7\n0000000000 65535 f \n0000000009 00000 n \n0000000058 00000 n \n0000000115 00000 n \n0000000266 00000 n \n0000000350 00000 n \n0000000440 00000 n \n"
        . "trailer\n<</Size 7/Root 1 0 R>>\nstartxref\n520\n%%EOF";

    file_put_contents($path, $pdf);
    return 'assets/pdf/' . $name;
}

$items = [];
$data = [
    ['Conseil municipal du 05 juin 2026', '2026-06-05', "Ordre du jour :\n- Approbation du procès-verbal de la séance précédente\n- Vote du budget communal 2026\n- Attribution des subventions aux associations\n- Point sur les travaux de voirie\n- Questions diverses"],
    ['Conseil municipal du 24 avril 2026', '2026-04-24', "Ordre du jour :\n- Acquisition d'un terrain communal\n- Travaux de rénovation de la salle polyvalente\n- Modification des horaires d'ouverture de la mairie\n- Tarifs du repas des anciens"],
    ['Conseil municipal du 29 mars 2026', '2026-03-29', "Ordre du jour :\n- Élection du maire et des adjoints\n- Installation du nouveau conseil municipal\n- Répartition des délégations\n- Calendrier des manifestations 2026"],
];
$maxId = 0;
$months = ['','janvier','février','mars','avril','mai','juin','juillet','août','septembre','octobre','novembre','décembre'];

foreach ($data as $i => $d) {
    $pdfPath = createDummyPdf($d[0], $d[1], $d[2]);
    $ts = strtotime($d[1]);
    $title = 'Séance du ' . date('d', $ts) . ' ' . $months[(int)date('m', $ts)] . ' ' . date('Y', $ts);
    $items[] = [
        'id' => $i + 1,
        'title' => $title,
        'date' => $d[1],
        'file' => $pdfPath,
        'thumbnail' => '',
    ];
    $maxId = $i + 1;
}

$file = __DIR__ . '/../data/comptes_rendus.json';
file_put_contents($file, json_encode($items, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
echo "OK - " . count($items) . " comptes-rendus factices créés.\n";
