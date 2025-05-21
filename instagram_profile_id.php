<?php
header('Content-Type: application/json; charset=utf-8');

// Récupération du username depuis GET ou POST
$username = $_GET['username'] ?? $_POST['username'] ?? null;

if (!$username) {
    echo json_encode([
        'success' => false,
        'error' => 'Paramètre "username" manquant.'
    ]);
    exit;
}

// URL du profil Instagram
$url = "https://www.instagram.com/" . urlencode($username) . "/";

// Initialisation cURL
$ch = curl_init($url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows NT 10.0; Win64; x64)");
$html = curl_exec($ch);
curl_close($ch);

if (!$html) {
    echo json_encode([
        'success' => false,
        'error' => "Impossible de récupérer la page Instagram pour l'utilisateur $username."
    ]);
    exit;
}

// Découpage du HTML par balise fermante </script>
$parts = explode('</script>', $html);

// À ajuster si l'index change selon la page, ici on garde 37
$index = 37;

if (!isset($parts[$index])) {
    echo json_encode([
        'success' => false,
        'error' => "La partie du script index $index n'a pas été trouvée dans le contenu."
    ]);
    exit;
}

$content = $parts[$index];
$needle = 'data-sjs>';
$pos = strpos($content, $needle);

if ($pos === false) {
    echo json_encode([
        'success' => false,
        'error' => "La chaîne 'data-sjs>' n'a pas été trouvée dans la partie index $index."
    ]);
    exit;
}

$startPos = $pos + strlen($needle);
$jsonContent = trim(substr($content, $startPos));

// Décodage JSON
$data = json_decode($jsonContent, true);

if (json_last_error() !== JSON_ERROR_NONE) {
    echo json_encode([
        'success' => false,
        'error' => "Erreur de décodage JSON : " . json_last_error_msg()
    ]);
    exit;
}

// Extraction du profile_id (chemin donné)
$path = ['require', 0, 3, 0, '__bbox', 'require', 8, 3, 0, 'initialRouteInfo', 'route', 'rootView', 'props', 'page_logging', 'params', 'profile_id'];

$profile_id = $data;
foreach ($path as $key) {
    if (isset($profile_id[$key])) {
        $profile_id = $profile_id[$key];
    } else {
        echo json_encode([
            'success' => false,
            'error' => "La clé demandée n'existe pas dans le tableau."
        ]);
        exit;
    }
}

// Tout OK, on renvoie le résultat
echo json_encode([
    'success' => true,
    'username' => $username,
    'profile_id' => $profile_id
]);
