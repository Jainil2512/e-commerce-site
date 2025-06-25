<?php
$conn = mysqli_connect("localhost", "root", "", "file_upload");
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}
// session_start();
?>