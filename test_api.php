<?php
$key = "YOUR_GEMINI_API_KEY";
$url = "https://generativelanguage.googleapis.com/v1beta/models/gemini-1.5-flash-latest:generateContent?key=$key";

$payload = json_encode(["contents" => [[ "parts" => [["text" => "Hello Gemini"]] ]]]);
$ch = curl_init($url);
curl_setopt_array($ch, [
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_POST => true,
    CURLOPT_HTTPHEADER => ["Content-Type: application/json"],
    CURLOPT_POSTFIELDS => $payload,
]);
echo curl_exec($ch);
curl_close($ch);
?>
