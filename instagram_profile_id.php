<?php
header('Content-Type: application/json; charset=utf-8');

// Mode debug
$debug = ($_GET['debug'] ?? $_POST['debug'] ?? null) === '1';

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

if ($debug) {
    echo "<pre>HTML partiel:\n";
    echo substr($html, 0, 1000); // Affiche les 1000 premiers caractères
    echo "\n</pre>";
}

// Découpage du HTML par balise </script>
$parts = explode('</script>', $html);
var_dump($parts) ;
// Index supposé contenant le JSON
$index = 37;

if (!isset($parts[$index])) {
    echo json_encode([
        'success' => false,
        'error' => "La partie du script index $index n'a pas été trouvée dans le contenu."
    ]);
    if ($debug) {
        echo "<pre>Nombre total de scripts trouvés : " . count($parts) . "</pre>";
    }
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
    if ($debug) {
        echo "<pre>Contenu à l'index $index:\n" . htmlspecialchars($content) . "</pre>";
    }
    exit;
}

$startPos = $pos + strlen($needle);
$jsonContent = trim(substr($content, $startPos));

if ($debug) {
    echo "<pre>JSON brut:\n" . htmlspecialchars(substr($jsonContent, 0, 1000)) . "...</pre>";
}

// Décodage JSON
$data = json_decode($jsonContent, true);
var_dump($data) ;
if (json_last_error() !== JSON_ERROR_NONE) {
    echo json_encode([
        'success' => false,
        'error' => "Erreur de décodage JSON : " . json_last_error_msg()
    ]);
    if ($debug) {
        echo "<pre>JSON mal formé:\n" . htmlspecialchars($jsonContent) . "</pre>";
    }
    exit;
}

// Chemin vers profile_id
$path = ['require', 0, 3, 0, '__bbox', 'require', 8, 3, 0, 'initialRouteInfo', 'route', 'rootView', 'props', 'page_logging', 'params', 'profile_id'];

$profile_id = $data;
foreach ($path as $key) {
    if (isset($profile_id[$key])) {
        $profile_id = $profile_id[$key];
    } else {
        echo json_encode([
            'success' => false,
            'error' => "La clé '$key' n'existe pas dans le tableau à ce niveau."
        ]);
        if ($debug) {
            echo "<pre>État actuel du tableau à l'échec:\n";
            var_dump($profile_id);
            echo "</pre>";
        }
        exit;
    }
}

// Réponse finale
echo json_encode([
    'success' => true,
    'username' => $username,
    'profile_id' => $profile_id
], JSON_PRETTY_PRINT);
