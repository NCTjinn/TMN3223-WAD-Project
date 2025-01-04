<?php
$conn = new mysqli("localhost", "root", "", "PuffLab");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>