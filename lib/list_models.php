<?php
$api_key = "AIzaSyCw7Z2x03zk-ubMIyI3LA3oLjdAwWlKy9E";
$url = "https://generativelanguage.googleapis.com/v1beta/models?key=" . $api_key;

$ch = curl_init($url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$response = curl_exec($ch);
curl_close($ch);

$data = json_decode($response, true);

if (isset($data['models'])) {
    foreach ($data['models'] as $model) {
        if (strpos($model['supportedGenerationMethods'][0], 'generateContent') !== false || in_array('generateContent', $model['supportedGenerationMethods'])) {
            echo $model['name'] . "\n";
        }
    }
} else {
    echo "Error: " . $response;
}
?>