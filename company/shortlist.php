<?php

session_start();

if (!isset($_SESSION["user_id"]) || $_SESSION["role"] !== "company") {
    header("Location: ../login.php");
    exit;
}

require_once "../config.php";

$application_id = $_GET["id"] ?? 0;

$application_id = intval($application_id);

if ($application_id <= 0) {
    die("Invalid Application ID");
}

$sql = "UPDATE applications
        SET status = 'Shortlisted'
        WHERE id = ?";

$stmt = $conn->prepare($sql);

$stmt->bind_param("i", $application_id);

$stmt->execute();

$stmt->close();

header("Location: applicants.php");

exit;

?>