<?php

if ($_SERVER['REQUEST_METHOD'] === 'GET') {

    if (file_exists(dirname($_SERVER['DOCUMENT_ROOT']) . DIRECTORY_SEPARATOR . '.env')) {
        die('No work to be done here');
    }
?>

<form id="env">
    <label for="DB_SSL">DB_SSL:</label>
    <select id="DB_SSL" name="DB_SSL">
        <option value="false">false</option>
        <option value="true">true</option>
    </select><br><br>
    <label for="DB_HOST">DB_HOST:</label>
    <input type="text" id="DB_HOST" name="DB_HOST" required placeholder="localhost" value="localhost"><br><br>

    <label for="DB_USER">DB_USER:</label>
    <input type="text" id="DB_USER" name="DB_USER" placeholder="root" value="root" required><br><br>

    <label for="DB_PASS">DB_PASS:</label>
    <input type="password" id="DB_PASS" name="DB_PASS" required><br><br>

    <label for="DB_NAME">DB_NAME:</label>
    <input type="text" id="DB_NAME" name="DB_NAME" placeholder="dashboard" value="dashboard" required><br><br>

    <label for="DB_DRIVER">DB_DRIVER:</label>
    <select id="DB_DRIVER" name="DB_DRIVER">
        <option value="mysql">MySQL</option>
        <option value="pgsql">PostgreSQL</option>
        <option value="sqlsrv">SQL Server</option>
        <option value="sqlite">SQLite</option>
    </select><br><br>

    <input type="checkbox" id="SENDGRID" name="SENDGRID">
    <label for="SENDGRID">SENDGRID</label><br><br>

    <input type="checkbox" id="Entra_ID_login" name="Entra_ID_login">
    <label for="Entra_ID_login">Entra ID Login (formerly Azure AD)</label><br><br>

    <!--<input type="checkbox" id="Microsoft_LIVE_login" name="Microsoft_LIVE_login">
    <label for="Microsoft_LIVE_login">Microsoft LIVE Login</label><br><br>-->

    <input type="checkbox" id="local_login" name="local_login">
    <label for="local_login">Local Login</label><br><br>

    <button type="submit">Submit</button>
</form>

<script src="/assets/js/create-env.js"></script>

<?php
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    //return var_dump($_POST);


    if (file_exists(dirname($_SERVER['DOCUMENT_ROOT']) . DIRECTORY_SEPARATOR . '.env')) {
        die('No work to be done here');
    }

    // Create the .env file
    $envContentArray = $_POST;
    
    if (isset($_POST['local_login'])) {
        if (!extension_loaded('openssl')) {
            die('Enable openssl extension');
        }

        // Generate private key
        $config = [
            "digest_alg" => "sha512",
            "private_key_bits" => 2048,
            "private_key_type" => OPENSSL_KEYTYPE_RSA,
        ];

        function encryptData($value){
            $key = $userKey;
            $text = $value;
            $iv_size = mcrypt_get_iv_size(MCRYPT_RIJNDAEL_256, MCRYPT_MODE_ECB);
            $iv = mcrypt_create_iv($iv_size, MCRYPT_RAND);
            $crypttext = mcrypt_encrypt(MCRYPT_RIJNDAEL_256, $key, $text, MCRYPT_MODE_ECB, $iv);
            return $crypttext;
         }
        
        // Create the keypair
        $res = openssl_pkey_new($config);

        // Get private key
        openssl_pkey_export($res, $privKey);

        // Get public key
        $pubKey = openssl_pkey_get_details($res);
        $pubKey = $pubKey["key"];
        
        // Base64 encode private and public keys
        $base64PrivateKey = base64_encode($privKey);
        $base64PublicKey = base64_encode($pubKey);

        $envContentArray['JWT_PUBLIC_KEY'] = $base64PublicKey;
        $envContentArray['JWT_PRIVATE_KEY'] = $base64PrivateKey;
    }

    unset($envContentArray['local_login']);
    unset($envContentArray['Entra_ID_login']);
    unset($envContentArray['SENDGRID']);

    $envContent = '';

    foreach ($envContentArray as $key => $value) {
        $updatedValue = '';
        if ($value === 'true' || $value === 'false') {
            $updatedValue = $value;
        } else {
            $updatedValue = '"' . $value . '"';
        }
        $envContent .= $key . '=' . $updatedValue . '' . PHP_EOL;
    }

    

    $envFilePath = dirname($_SERVER['DOCUMENT_ROOT']) . DIRECTORY_SEPARATOR . '.env';

    $fileHandle = fopen($envFilePath, 'w');

    // Write the content to the file
    if ($fileHandle) {
        fwrite($fileHandle, $envContent);
        fclose($fileHandle);
        echo "The .env file has been created successfully.";
    } else {
        echo "Unable to create the .env file.";
    }

}