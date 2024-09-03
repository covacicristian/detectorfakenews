<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "my_new_db";

// Creare conexiune
$conn = new mysqli($servername, $username, $password, $dbname);

// Verificare conexiune
if ($conn->connect_error) {
    die("Conexiunea a eșuat: " . $conn->connect_error);
}
?>