<?php

function rand_number() {
    return rand(1, 999);
}


function rand_string($length) {
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }
    return $randomString;
}


function warp_unlimited($id_code) {
    $url = "https://api.cloudflareclient.com/v0a" . rand_number() . "/reg";
    $inst = rand_string(22);
    $body = array(
        "key" => rand_string(43) . "=",
        "install_id" => $inst,
        "fcm_token" => $inst . ":APA91b" . rand_string(134),
        "referrer" => $id_code,
        "warp_enabled" => false,
        "locale" => "es_US"
    );
    $data = json_encode($body);
    $headers = array(
        'Content-Type: application/json; charset=UTF-8',
        'User-Agent' => 'okhttp/3.12.1'
    );

    $options = array(
        'http' => array(
            'header' => implode("\r\n", $headers),
            'method' => 'POST',
            'content' => $data,
        )
    );

    $context = stream_context_create($options);
    try {
        $response = file_get_contents($url, false, $context);
        if ($response === FALSE) {
            throw new Exception("HTTP Request Failed");
        }
        return true;
    } catch (Exception $e) {
        return false;
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $code_id = $_POST['client_id'];
    $interval = 10; 
    $total_GB = 2;  

    $total_gb = 0;
    $response_message = "";
    $success_count = 0;

    while ($total_gb < $total_GB) {
        $success = warp_unlimited($code_id);
        if ($success) {
            $total_gb += 1;
            $success_count += 1;
            $response_message = "Success! You got $success_count GB Warp+.";
        } else {
            $response_message = "An error occurred. Please try again later.";
            break;
        }

        for ($i = $interval; $i > 0; $i--) {
            sleep(1);
            if ($total_gb == $total_GB) {
                break;
            }
        }
    }

    echo $response_message;
} else {
    echo 'Invalid request method.';
}
?>
