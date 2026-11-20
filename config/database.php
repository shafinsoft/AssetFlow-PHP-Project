<?php
// XAMPP default: user root, empty password. Change if yours differs.
$conn = new mysqli('localhost', 'root', '', 'assetflow_db');
if ($conn->connect_error) die('Database connection failed. Did you import database/assetflow.sql?');
$conn->set_charset('utf8mb4');
