<?php

#!!! AI generated code

// Check if the number of rows argument is provided
if ($argc < 2) {
    echo "Usage: php script_name.php number_of_rows\n";
    exit(1);
}

// Replace with your database connection details
$host = 'mysql-old';
$username = 'root';
$password = 'password';
$database = 'users';

// Create a connection to the database
$mysqli = new mysqli($host, $username, $password, $database);

// Check connection
if ($mysqli->connect_error) {
    die("Connection failed: " . $mysqli->connect_error);
}

// Function to generate a random date within a specific range
function randomDate($start_date, $end_date) {
    $min = strtotime($start_date);
    $max = strtotime($end_date);
    $random_date = mt_rand($min, $max);
    return date('Y-m-d', $random_date);
}

// Function to generate a random string of specified length
function randomString($length) {
    $characters = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
    $string = '';
    for ($i = 0; $i < $length; $i++) {
        $string .= $characters[mt_rand(0, strlen($characters) - 1)];
    }
    return $string;
}

// Number of random records to insert (from command-line argument)
$numberOfRecords = intval($argv[1]);

// Define date range for birth_date randomization
$start_date = '1900-01-01';
$end_date = '2023-12-31';

// Batch size for chunking
$batchSize = 1000;

// Calculate the number of chunks
$numChunks = ceil($numberOfRecords / $batchSize);

// Loop through the chunks and insert data
for ($chunk = 0; $chunk < $numChunks; $chunk++) {
    // Prepare the batch insert query
    $insertQuery = "INSERT INTO users (username, email, birth_date) VALUES ";

    // Generate batch insert values
    $startRecord = $chunk * $batchSize;
    $endRecord = min(($chunk + 1) * $batchSize, $numberOfRecords);

    for ($i = $startRecord; $i < $endRecord; $i++) {
        $username = randomString(8);
        $email = $username . '@example.com';
        $birth_date = randomDate($start_date, $end_date);

        // Add values to the batch insert query
        $insertQuery .= "('$username', '$email', '$birth_date')";

        // Add a comma after each set of values except the last one
        if ($i < $endRecord - 1) {
            $insertQuery .= ", ";
        }
    }

    // Execute the batch insert query
    if ($mysqli->query($insertQuery) === TRUE) {
        echo "Chunk " . ($chunk + 1) . " of $numChunks inserted successfully.\n";
    } else {
        echo "Error inserting chunk " . ($chunk + 1) . ": " . $mysqli->error . "\n";
    }
}

// Close the database connection
$mysqli->close();