<?php
header("Content-Type: application/json; charset=UTF-8");

require_once '../kuro/require.php';
require_once '../kuro/controllers/api.php';
require_once '../kuro/controllers/logs.php';

$api = new Api;
$log = new Logs;

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
        $log->AddLog($response['data']->userId, $response['data']->orgId, 1);
    } else {
        $userId = $api->GetUserIdByUsername($_GET['user']); // Assuming 'username' is the name of the input field
        $userId = $userId ? $userId : 0; // Set to 0 if not found
        $log->AddLog($userId, 0, 0);
    }

    echo json_encode($response);
}