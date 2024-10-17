<?php
// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

$mysqli = new mysqli("localhost", "new_user", "pass123!", "jobeet2");
// $mysqli->options(MYSQLI_INIT_COMMAND, "SET NAMES 'utf8'");

if ($mysqli->connect_error) {
    die("Connection failed: " . $mysqli->connect_error);
}

// Set the charset to utf8
// $mysqli->set_charset("utf8");

$jsonData = file_get_contents('categories.json');
$data = json_decode($jsonData, true);

foreach ($data['categories'] as $category) {
    $name = $mysqli->real_escape_string($category);
    $slug = strtolower(preg_replace('/[^a-z0-9]+/i', '-', trim($category)));

    $sql = "INSERT INTO categories (name, slug) VALUES ('$name', '$slug')";

    if ($mysqli->query($sql) === TRUE) {
        echo "Category '$name' inserted successfully.\n";
    } else {
        echo "Error inserting category '$name': " . $mysqli->error . "\n";
    }
}

$mysqli->close();
?>
