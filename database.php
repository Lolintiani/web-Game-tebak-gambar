<?php
// config/database.php

$host = "localhost";
$username = "nebg9297_nebakgambar";
$password = "errylinggo2020";
$database = "nebg9297_nebak gambar";

$conn = new mysqli($host, $username, $password, $database);

if ($conn->connect_error) {
    die("Koneksi database gagal: " . $conn->connect_error);
}
?>