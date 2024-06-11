<?php
include('config.php');

$admin_login = BOT_LOGIN;
$admin_api_token = API_MATRIX_USER;
$admin_shared_secret = SHARED_SECRET;
$matrix_server_url = MATRIX_SERVER_URL;

function getNonce() {
    global $matrix_server_url, $admin_api_token;

    $url = $matrix_server_url . '/_synapse/admin/v1/register';

    $headers = [
        'Authorization: Bearer ' . $admin_api_token,
        'Content-Type: application/json'
    ];

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

    $response = curl_exec($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($http_code == 200) {
        $data = json_decode($response, true);
        return $data['nonce'];
    } else {
        return false;
    }
}

function createUser($login, $password, $nonce) {
    global $matrix_server_url, $admin_shared_secret;

    $url = $matrix_server_url . '/_synapse/admin/v1/register';

    $mac = hash_hmac('sha1', $nonce . "\0" . $login . "\0" . $password . "\0" . 'notadmin', $admin_shared_secret);

    $data = [
        'nonce' => $nonce,
        'username' => $login,
        'password' => $password,
        'mac' => $mac
    ];

    $headers = [
        'Content-Type: application/json'
    ];

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

    $response = curl_exec($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    return [$http_code, $response];
}

function getRegistrationTokens() {
    global $matrix_server_url, $admin_api_token;

    $url = $matrix_server_url . '/_synapse/admin/v1/registration_tokens';

    $headers = [
        'Authorization: Bearer ' . $admin_api_token,
        'Content-Type: application/json'
    ];

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

    $response = curl_exec($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($http_code == 200) {
        $data = json_decode($response, true);
        return isset($data['registration_tokens']) ? $data['registration_tokens'] : [];
    } else {
        return false;
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $login = $_POST['login'];
    $password = $_POST['password'];
    $token = $_POST['token'];

    if (!empty($login) && !empty($password) && !empty($token)) {
        $tokens = getRegistrationTokens();
        $isValidToken = false;

        if ($tokens !== false) {
            foreach ($tokens as $regToken) {
                if ($regToken['token'] === $token) {
                    $isValidToken = true;
                    break;
                }
            }
        }

        if ($isValidToken) {
            $nonce = getNonce();
            if ($nonce) {
                list($status, $response) = createUser($login, $password, $nonce);

                if ($status == 200) {
                    echo '<p class="text-success">Пользователь успешно создан.</p>';
                } else {
                    echo '<p class="text-danger">Ошибка создания пользователя: ' . $response . '</p>';
                }
            } else {
                echo '<p class="text-danger">Ошибка получения nonce.</p>';
            }
        } else {
            echo '<p class="text-danger">Неверный токен регистрации.</p>';
        }
    } else {
        echo '<p class="text-danger">Пожалуйста, заполните все поля.</p>';
    }
}
?>
