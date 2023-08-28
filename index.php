<?php
declare(strict_types=1);

require_once "ResultsManager.php";

if (isset($_GET['save-results']) && $_SERVER['REQUEST_METHOD'] === "POST") {
    $data = json_decode(file_get_contents("php://input")); // get JSON body data

    if (ResultsManager::saveResultToFile($data))
        header("HTTP/1.1 201 Created");
    else
        header("HTTP/1.1 400 Bad Request");

} else {
    header("HTTP/1.1 404 Not Found");
}