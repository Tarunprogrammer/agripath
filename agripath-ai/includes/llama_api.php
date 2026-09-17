<?php
$ch = curl_init("http://localhost:11434/api/tags");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$response = curl_exec($ch);

if (curl_errno($ch)) {
    echo "CURL ERROR: " . curl_error($ch);
} else {
    echo "SUCCESS: Connection established. Response: " . $response;
}
curl_close($ch);
?>
