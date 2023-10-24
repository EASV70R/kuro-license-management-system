<?php
header("Content-Type: application/json; charset=UTF-8");

require_once '../kuro/require.php';
require_once '../kuro/controllers/api.php';

$api = new Api;

if ($_SERVER["REQUEST_METHOD"] == "GET")
{
    if (empty($_GET['key'] || empty($_GET['user']) || empty($_GET['pass'])))
    {
        $response = array('status' => 'failed', 'error' => 'Missing arguments');
    }

    $apiKey = $_GET["key"];
    $username = $_GET["user"];
    $password = $_GET["pass"];
    
    $response = $api->UserLogin($apiKey, $username, $password);

    if($response['status'] === 'success')
    {
        // add logging later
    }

    echo json_encode($response);
}