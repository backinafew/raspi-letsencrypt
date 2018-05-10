#!/usr/bin/php

<?php
// Utility to create configuration json file in root folder of project.
// Change to match your Google account info...
$config = ['credentials_path' => '/path/to/your/google-credentials.json',
           'managedZone' => 'YourGoogleManagedZoneName',
           'project' => 'YourGoogleProjectName',
           'runlevel' => ''];      // '' = production // 'DEBUG' = test mode

//convert / validate json...
$config = json_encode($config,  JSON_PRETTY_PRINT);

if ($config != false) {
    file_put_contents('./config.json', $config);
    $cfgSettings = json_decode(file_get_contents('./config.json'));
    print_r($cfgSettings);
} //($config =! false)
else {
    echo("Error creating config.json file... \n");
    die(json_last_error_msg() . "\n");
} //else
?>