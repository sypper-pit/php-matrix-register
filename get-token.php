<?php
// Включаем файл конфигурации
require 'config.php';

function getRegistrationTokens($apiKey) {
    $ch = curl_init();

    curl_setopt($ch, CURLOPT_URL, MATRIX_SERVER_URL . '/_synapse/admin/v1/registration_tokens');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Authorization: Bearer ' . $apiKey,
        'Content-Type: application/json'
    ]);

    $result = curl_exec($ch);

    if (curl_errno($ch)) {
        return 'Error: ' . curl_error($ch);
    }

    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

    curl_close($ch);

    $response = json_decode($result, true);
    
    if ($httpCode == 200 && isset($response['registration_tokens'])) {
        return $response['registration_tokens'];
    } elseif ($httpCode != 200) {
        return 'Error: HTTP ' . $httpCode . ' - ' . (isset($response['error']) ? $response['error'] : 'Unknown error');
    } else {
        return 'Error retrieving registration tokens.';
    }
}

$tokens = getRegistrationTokens(API_MATRIX_USER);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registration Tokens</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container">
        <h2 class="mt-5">Registration Tokens</h2>
        <?php if (is_array($tokens)): ?>
            <ul>
                <?php foreach ($tokens as $token): ?>
                    <li><?php echo htmlspecialchars($token['token']); ?></li>
                <?php endforeach; ?>
            </ul>
        <?php else: ?>
            <div class="alert alert-danger">
                <?php echo htmlspecialchars($tokens); ?>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>
