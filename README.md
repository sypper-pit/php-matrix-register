***Matrix User Registration System***

This repository contains a set of PHP scripts to handle user registration and API token validation for a Matrix server. Below is a description of the included files and their functionality.

**Files**

`config.php` - This file contains configuration constants required by the other scripts. It defines constants such as API keys, server names, and minimum password length.

`index.php` - This is the main entry point for the web interface. It includes the config.php file and provides a form for user registration. The form collects user login, password, and registration token.

`register.php` - This script processes the form data from index.php. It handles user registration by sending the collected data to the Matrix server using the provided API key.

`get-token.php` - This script is responsible for generating and validating registration tokens. It ensures that only valid tokens are used for the registration process.

`get_api.php` - This script checks the current API key status and provides an interface to validate the key.

*How to Use*
1) Install admin panel https://github.com/Awesome-Technologies/synapse-admin .
2) Add token
3) Add Admin user for register
4) Open `http://site.com/get_api.php` , after autorize copy API key
5) Open homserver.yaml and get `registration_shared_secret:`
6) Edit `config.php`
7) Open `http://site.com/get-token.php` for check token's
8) Try register user `http://site.com`
9) If all work - delete `get_api.php` and `get-token.php`

## If you want help me. Send donats:

Dogecoin (DOGE): `D6kb8jcVXYTi82nsoACAYKYhtA5EJ4D9Jg`

Litecoin (LTC): `LfMJCyxxg65sA3X9XEze157D16ztszndqk`

Bitcoin (BTC): `bc1qttzg9yww3nv5dg2d5ja95txmt0mrw9dltfqj57`

Monero (XMR): `8AyWrMwPCxrcbcmVDj3Y5RCfcSQtBBVE2JK9qJ4WqrPpaoa3uNvLReQXPXGj7D5zEsMjBKeWWdyDD4gerqzTtKKS36zSfnM`

Ethereum (ETH): `0xbdfec67586a78e5d3b58dfb70aa181823c8deafa`

TRC-20 USDT: `TTZGfnhurU62VRRGYUHMPJ8q6U8rn5xG5a`

ERC-20 USDT: `0xbdfec67586a78e5d3b58dfb70aa181823c8deafa`



*Security Considerations*

Ensure that config.php is not accessible directly from the web to prevent exposure of sensitive information.
Use HTTPS to encrypt data transmission between the client and the server.
For any questions or issues, please contact the repository maintainer.
