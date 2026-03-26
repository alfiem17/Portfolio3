<?php
$host = "localhost";
$username = "REDACTED";
$db_password = "REDACTED";
$dbName = "REDACTED";

try {
    $conn = new PDO("mysql:host=$host;dbname=$dbName", $username, $db_password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $ex) {
    echo "Connection failed: " . $ex->getMessage();
    echo "<p>Sorry, a database error occurred. Please try again.</p>";
}
?>