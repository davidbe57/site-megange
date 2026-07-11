<?php
define('PP_CACHE', DATA_DIR . '/panneaupocket_cache.json');
define('PP_TTL', 600);
define('PP_URL', 'https://app.panneaupocket.com/ville/250252113-megange-57220');

function get_panneaupocket_alerts() {
    if (file_exists(PP_CACHE)) {
        $c = json_decode(file_get_contents(PP_CACHE), true);
        if ($c && time() - $c['time'] < PP_TTL) return $c['data'];
    }

    $ctx = stream_context_create(['http' => [
        'method' => 'GET',
        'header' => "User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36\r\nAccept: text/html\r\n",
        'timeout' => 10,
    ]]);

    $html = @file_get_contents(PP_URL, false, $ctx);
    if (!$html) return false;

    $alerts = [];
    libxml_use_internal_errors(true);
    $doc = new DOMDocument();
    $doc->loadHTML('<?xml encoding="utf-8" ?>' . $html);
    $xpath = new DOMXPath($doc);

    $items = $xpath->query("//div[contains(@class, 'sign-carousel--item')]");
    if (!$items || $items->length === 0) {
        // Try alternate selector
        $items = $xpath->query("//div[contains(@class, 'sign-preview')]");
    }

    foreach ($items as $item) {
        $a = [];

        $t = $xpath->query(".//div[contains(@class, 'sign-preview__content')]/div[contains(@class, 'title')]", $item);
        if ($t && $t->length > 0) $a['title'] = trim($t->item(0)->textContent);

        $b = $xpath->query(".//div[contains(@class, 'sign-preview__content')]/div[contains(@class, 'content')]", $item);
        if ($b && $b->length > 0) {
            $inner = '';
            foreach ($b->item(0)->childNodes as $child) {
                $inner .= $doc->saveHTML($child);
            }
            // Remove img tags and empty a tags (image wrappers)
            $inner = preg_replace('/<img[^>]*>/i', '', $inner);
            $inner = preg_replace('/<a[^>]*>\s*<\/a>/i', '', $inner);
            $a['body'] = trim(strip_tags($inner, '<br><p><strong><em>'));
        }

        $d = $xpath->query(".//span[contains(@class, 'date')]", $item);
        if ($d && $d->length > 0) $a['date'] = trim($d->item(0)->textContent);

        $c = $xpath->query(".//p[contains(@class, 'city')]", $item);
        if ($c && $c->length > 0) $a['author'] = trim($c->item(0)->textContent);

        $i = $xpath->query(".//div[contains(@class, 'sign-preview__content')]//img", $item);
        if ($i && $i->length > 0) $a['image'] = $i->item(0)->getAttribute('src');

        if (!empty($a)) $alerts[] = $a;
    }
    libxml_clear_errors();

    if (empty($alerts)) return false;

    file_put_contents(PP_CACHE, json_encode(['time' => time(), 'data' => $alerts]));
    return $alerts;
}
