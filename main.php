<?php

$input = "./easylist.txt";

// Read the contents of the file
$content = file_get_contents($input);

if (!$content) {
    die("Failed to read file.");
}

// Split the content into an array of lines
$content = explode(PHP_EOL, $content);

// Loop through each line and generate a JSON string
$jsonItems = [];
for ($i = 0; $i < count($content); $i++) {
    $line = trim($content[$i]);

    // Skip comments and empty lines
    if ($line === '' || strpos($line, '!') === 0 || strpos($line, '[') === 0) {
        continue;
    }

    // Process normal lines with parameters
    $parts = explode("$", $line);

    // Handle cases where there are no parameters
    if (count($parts) < 2) {
        $urlFilter = trim($parts[0]);
        $resourceTypes = [];
        $excludedDomains = [];
    } else {
        $urlFilter = trim($parts[0]);
        $params = trim($parts[1]);

        // Separate individual parameters
        $resourceParams = explode(',', $params);

        // Initialize arrays to store resource types and domains
        $resourceTypes = [];
        $excludedDomains = [];

        foreach ($resourceParams as $param) {
            // Check for valid resource types
            if (preg_match('/image|script|stylesheet|xmlhttprequest|object|popup/', $param, $matches)) {
                $resourceTypes[] = $matches[0];
            }

            // Extract domains
            if (preg_match('/domain=~([^|]+)/', $param, $matches)) {
                $excludedDomains[] = $matches[1];
            }
            if (preg_match('/domain=.*\|([^|]+)/', $param, $matches)) {
                $excludedDomains[] = $matches[1];
            }
        }
    }

    // Create the JSON item
    $jsonItem = [
        "id" => $i + 1, // Adjust ID to start from 1
        "priority" => 1,
        "action" => [
            "type" => "block"
        ],
        "condition" => [
            "urlFilter" => "*://*." . $urlFilter . "*",
        ]
    ];

    if (preg_match('/^-/', $content[0])) {
        $jsonItem = [
            "id" => $i + 1, // Adjust ID to start from 1
            "priority" => 1,
            "action" => [
                "type" => "block"
            ],
            "condition" => [
                "urlFilter" => "/*" . $urlFilter . "*"
            ]
        ];
    }

    // Add resourceTypes if not empty
    if (!empty($resourceTypes)) {
        $jsonItem["condition"]["resourceTypes"] = $resourceTypes;
    }

    // Add excludedDomains if not empty
    if (!empty($excludedDomains)) {
        $jsonItem["condition"]["excludedDomains"] = $excludedDomains;
    }

    $jsonItems[] = $jsonItem;
}

// Convert the array of JSON objects to a JSON string
$output = json_encode($jsonItems, JSON_PRETTY_PRINT);

// Output the JSON
echo $output;

// Optionally, save to a file
file_put_contents('rules.json', $output);
