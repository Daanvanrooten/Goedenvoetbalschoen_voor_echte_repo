<?php
// automatisch detecteren of we lokaal of online zijn
$is_local = (strpos($_SERVER['HTTP_HOST'], 'localhost') !== false ||
    strpos($_SERVER['HTTP_HOST'], '127.0.0.1') !== false ||
    strpos($_SERVER['HTTP_HOST'], 'webroot.local') !== false);

if ($is_local) {
    // lokale urls
    $base_url = '/goudenvoetbalschoen/Goedenvoetbalschoen_voor_echte_repo/gdvoetbalschoen';
} else {
    // online server urls
    $base_url = '';
}
