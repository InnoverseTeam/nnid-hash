<?php
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nnid = isset($_POST['nnid']) ? $_POST['nnid'] : null;

    if ($nnid) {
        function getNNIDHash($user_id) {
            $api_url = "https://nnidlt.murilo.eu.org/api.php?env=production&user_id=" . urlencode($user_id);

            $ch = curl_init();
            
            curl_setopt($ch, CURLOPT_URL, $api_url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            
            $response = curl_exec($ch);
            
            if (curl_errno($ch)) {
                echo 'Error cURL : ' . curl_error($ch);
                curl_close($ch);
                return null;
            }
            
            curl_close($ch);
            
            $data = json_decode($response, true);
            
            file_put_contents('debug.log', "API response for user_id $user_id : " . print_r($data, true) . "\n", FILE_APPEND);
            
            if (isset($data['hash'])) {
                return $data['hash'];
            } else {
                return null;
            }
        }

        $hash = getNNIDHash($nnid);

        if ($hash) {
            echo "The hash for user $nnid is: $hash<br>";
            echo "<img src='http://mii-images.account.nintendo.net/{$hash}_normal_face.png' alt='Mii Image'>";
        } else {
            echo "Unable to get hash for user $nnid.";
        }
    } else {
        echo "NNID required.";
    }
} else {
    echo '
    <!DOCTYPE html>
    <html>
    <head>
        <meta charset="UTF-8">
        <title>Search Hash NNID</title>
    </head>
    <body>
        <form action="" method="post">
            <label for="nnid">Nintendo Network ID:</label>
            <input type="text" id="nnid" name="nnid" required><br><br>
            <input type="submit" value="Submit">
        </form>
    </body>
    </html>
    ';
}
?>
