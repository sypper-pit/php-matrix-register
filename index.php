<?php
include('config.php');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Create new user</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        body {
            background-color: #343a40;
            color: #ffffff;
        }
        .container {
            margin-top: 50px;
        }
        .form-group label {
            color: #ffffff;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1 class="text-center">Create new user</h1>
        <div id="message" class="text-center"></div>
        <form id="registerForm">
            <div class="form-group">
                <label for="login">Login:</label>
                <input type="text" class="form-control" id="login" name="login" required>
            </div>
            <div class="form-group">
                <label for="password">Password (minimum <?php echo MIN_PASS; ?> simbol):</label>
                <input type="password" class="form-control" id="password" name="password" required>
            </div>
            <div class="form-group">
                <label for="confirmPassword">Password confirm:</label>
                <input type="password" class="form-control" id="confirmPassword" name="confirmPassword" required>
            </div>
            <div class="form-group">
                <label for="token">Token for register:</label>
                <input type="text" class="form-control" id="token" name="token" required>
            </div>
            <button type="submit" class="btn btn-primary btn-block" id="submitBtn" disabled>Create user</button>
        </form>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script>
        $(document).ready(function() {
            function validatePasswords() {
                let password = $('#password').val();
                let confirmPassword = $('#confirmPassword').val();
                let minPassLength = <?php echo MIN_PASS; ?>;

                if (password.length >= minPassLength && password === confirmPassword) {
                    $('#submitBtn').prop('disabled', false);
                    $('#message').html('');
                } else {
                    $('#submitBtn').prop('disabled', true);
                    if (password.length < minPassLength) {
                        $('#message').html('<p class="text-danger">Password need ' + minPassLength + ' simbols</p>');
                    } else if (password !== confirmPassword) {
                        $('#message').html('<p class="text-danger">Password mismatch</p>');
                    }
                }
            }

            $('#password, #confirmPassword').on('keyup', validatePasswords);

            $('#registerForm').on('submit', function(event) {
                event.preventDefault();
                let login = $('#login').val();
                let password = $('#password').val();
                let confirmPassword = $('#confirmPassword').val();
                let token = $('#token').val();

                if (password.length >= <?php echo MIN_PASS; ?> && password === confirmPassword) {
                    $.ajax({
                        url: 'register.php',
                        type: 'POST',
                        data: {
                            login: login,
                            password: password,
                            token: token
                        },
                        success: function(response) {
                            $('#message').html(response);
                        },
                        error: function() {
                            $('#message').html('<p class="text-danger">Error create user</p>');
                        }
                    });
                }
            });
        });
    </script>
</body>
</html>
