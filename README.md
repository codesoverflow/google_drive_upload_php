# Create Service Account with Google API Console.

# Add key into that. and download credential json file

# Create config folder and config.php file inside it.

# Create following configs

<?php

$zipBackupsFrom = ["Path for the file where backup need to taken"];

$dbConfigs = [
        array(
        'user' => 'Database username',
        'host' => 'Database host',
        'password' => '***********',
        'dbName' => 'Db Name',
)
]
;


$fileUploadPath =  __DIR__ . "/upload/";
$downLoadPath = __DIR__."/download/";

$credentialsFile = __DIR__ . '/credentials.json';




# Open command line and run 

 php driveOprations.php 