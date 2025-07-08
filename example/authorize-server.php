<?php
// Jalankan ini: php -S localhost:8080 example/authorize-server.php

require __DIR__ . '/../vendor/autoload.php';

use Google_Client;

$credentialsPath = __DIR__ . '/../credentials.json';
$tokenPath = __DIR__ . '/../token.json';

$client = new Google_Client();
$client->setApplicationName("Google Contacts Manager");
$client->setScopes([Google_Service_PeopleService::CONTACTS]);
$client->setAuthConfig($credentialsPath);
$client->setAccessType('offline');
$client->setPrompt('consent');

$redirectUri = 'http://localhost:8080';
$client->setRedirectUri($redirectUri);

session_start();

if (!isset($_GET['code'])) {
    $authUrl = $client->createAuthUrl();
    echo "<a href='$authUrl'>Login ke Google</a>";
    exit;
} else {
    $token = $client->fetchAccessTokenWithAuthCode($_GET['code']);
    if (isset($token['error'])) {
        echo "<h3>Gagal tukar token:</h3><pre>" . htmlspecialchars(json_encode($token, JSON_PRETTY_PRINT)) . "</pre>";
        exit;
    }

    if (!is_dir(dirname($tokenPath))) {
        mkdir(dirname($tokenPath), 0777, true);
    }
    file_put_contents($tokenPath, json_encode($token));

    // $client->setAccessToken($token);
    // $oauth2 = new Google_Service_Oauth2($client);
    // $me = $oauth2->userinfo->get();

    echo "<h2>✅ Token berhasil disimpan!</h2>";
    echo "<p>Login sebagai: <b>{$me->email}</b></p>";
    echo "<p>Token tersimpan di: <code>$tokenPath</code></p>";
}
 
