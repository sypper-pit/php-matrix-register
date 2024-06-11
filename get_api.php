<?php
// Включаем файл конфигурации
require 'config.php';

function checkApiKey($login, $apiKey) {
    $ch = curl_init();

    curl_setopt($ch, CURLOPT_URL, MATRIX_SERVER_URL . '/_matrix/client/r0/account/whoami');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Authorization: Bearer ' . $apiKey,
        'Content-Type: application/json'
    ]);

    $result = curl_exec($ch);

    if (curl_errno($ch)) {
        return false;
    }

    $response = json_decode($result, true);
    curl_close($ch);

    return isset($response['user_id']);
}

function getNewApiKey($login, $password) {
    $postData = [
        'type' => 'm.login.password',
        'identifier' => [
            'type' => 'm.id.user',
            'user' => $login
        ],
        'password' => $password
    ];

    $ch = curl_init();

    curl_setopt($ch, CURLOPT_URL, MATRIX_SERVER_URL . '/_matrix/client/r0/login');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($postData));
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);

    $result = curl_exec($ch);

    if (curl_errno($ch)) {
        return 'Error: ' . curl_error($ch);
    }

    curl_close($ch);

    $response = json_decode($result, true);
    if (isset($response['access_token'])) {
        return $response['access_token'];
    } else {
        return 'Error retrieving API key.';
    }
}

$apiKey = null;
$error = null;
$responseMessage = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action']) && $_POST['action'] === 'request_new_api_key') {
        // Обработка запроса нового API ключа
        $login = $_POST['login'] ?? '';
        $password = $_POST['password'] ?? '';

        if (!empty($login) && !empty($password)) {
            $apiKey = getNewApiKey($login, $password);
            if (strpos($apiKey, 'Error') === false) {
                $responseMessage = 'New API key generated successfully.';
            } else {
                $error = $apiKey;
            }
            echo '<script>console.log(' . json_encode($apiKey) . ');</script>';
        } else {
            $error = 'Login and password are required.';
        }
    } elseif (isset($_POST['action']) && $_POST['action'] === 'check_current_api_key') {
        // Обработка проверки текущего API ключа
        $checkLogin = $_POST['check_login'] ?? '';
        $currentApiKey = $_POST['current_api_key'] ?? '';

        if (!empty($checkLogin) && !empty($currentApiKey)) {
            if (!checkApiKey($checkLogin, $currentApiKey)) {
                $error = 'The current API key is inactive.';
            } else {
                $responseMessage = 'The current API key is active.';
            }
        } else {
            $error = 'Login and current API key are required.';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Get API Key</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" rel="stylesheet">
    <script>
        function copyToClipboard(elementId) {
            var copyText = document.getElementById(elementId);
            copyText.select();
            document.execCommand("copy");
        }

        function showModal(message) {
            var modalBody = document.getElementById('modalBody');
            modalBody.textContent = message;
            $('#responseModal').modal('show');
        }

        <?php if (isset($apiKey) || isset($error) || isset($responseMessage)): ?>
        window.onload = function() {
            var message = <?php echo json_encode($apiKey ?? $error ?? $responseMessage); ?>;
            showModal(message);
        };
        <?php endif; ?>
    </script>
</head>
<body>
    <div class="container">
        <h2 class="mt-5">Get API Key</h2>
        <form method="POST" action="get_api.php">
            <input type="hidden" name="action" value="request_new_api_key">
            <div class="form-group">
                <label for="login">Login</label>
                <input type="text" class="form-control" id="login" name="login" required>
            </div>
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" class="form-control" id="password" name="password" required>
            </div>
            <button type="submit" class="btn btn-primary">Request New API Key</button>
        </form>

        <h2 class="mt-5">Check Current API Key</h2>
        <form method="POST" action="get_api.php">
            <input type="hidden" name="action" value="check_current_api_key">
            <div class="form-group">
                <label for="check_login">Login</label>
                <input type="text" class="form-control" id="check_login" name="check_login" required>
            </div>
            <div class="form-group">
                <label for="current_api_key">Current API Key</label>
                <input type="text" class="form-control" id="current_api_key" name="current_api_key" required>
            </div>
            <button type="submit" class="btn btn-primary">Check API Key</button>
        </form>

        <!-- Modal -->
        <div class="modal fade" id="responseModal" tabindex="-1" role="dialog" aria-labelledby="responseModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="responseModalLabel">Response</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body" id="modalBody"></div>
                    <div class="modal-footer">
                        <?php if (isset($apiKey) && strpos($apiKey, 'Error') === false): ?>
                            <input type="text" class="form-control" id="apiKeyField" value="<?php echo htmlspecialchars($apiKey); ?>" readonly>
                            <button type="button" class="btn btn-primary" onclick="copyToClipboard('apiKeyField')">Copy to Clipboard</button>
                        <?php endif; ?>
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>
</body>
</html>
