<?php

// Function to load .env file into PHP environment variables
function loadEnv($path = '.env') {
    if (!file_exists($path)) {
        die("Error: .env file not found!");
    }

    // Read the .env file line by line
    $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

    // Parse each line in the .env file
    foreach ($lines as $line) {
        if (strpos(trim($line), '#') === 0) {
            continue; // Ignore comments
        }

        // Split the line into key=value
        list($key, $value) = explode('=', $line, 2);
        $key = trim($key);
        $value = trim($value);

        // Set the environment variable
        if (!array_key_exists($key, $_SERVER) && !array_key_exists($key, $_ENV)) {
            putenv("$key=$value");
            $_ENV[$key] = $value;
            $_SERVER[$key] = $value;
        }
    }
}

// Load the .env variables
loadEnv();
