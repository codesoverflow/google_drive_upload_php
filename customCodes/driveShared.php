<?php

function getClient() {

    global $credentialsFile;

    $client = new Google_Client();

    if (!file_exists($credentialsFile)) {
        throw new RuntimeException('Service account credentials Not Found!');
    }

    $client->setAuthConfig($credentialsFile);
    $client->setApplicationName("drive-upload");
    $client->setScopes(Google_Service_Drive::DRIVE);

    
    return $client;
}

function getService($client = null) {
    $client = $client === null ? getClient() : $client;
    $service = new Google_Service_Drive($client);

    return $service;
}


function printOnSameLine($percent) {
    echo $percent . "\r";
}

function convertToMB($amount) {

    return formatSizeUnits($amount);
}

function formatSizeUnits($bytes) {
    if ($bytes >= 1073741824)
    {
        $bytes = number_format($bytes / 1073741824, 2) . ' GB';
    }
    elseif ($bytes >= 1048576)
    {
        $bytes = number_format($bytes / 1048576, 2) . ' MB';
    }
    elseif ($bytes >= 1024)
    {
        $bytes = number_format($bytes / 1024, 2) . ' KB';
    }
    elseif ($bytes > 1)
    {
        $bytes = $bytes . ' bytes';
    }
    elseif ($bytes == 1)
    {
        $bytes = $bytes . 'byte';
    }
    else
    {
        $bytes = '0 bytes';
    }

    return $bytes.'                                               ';
}

function printMessage($message, $enterNextLines = 1) {
    echo $message;
    for($i = 1; $i <= $enterNextLines; $i++) {
        echo "\n";
    }
}


// our function to read from the command line
function readStdin()
{
        $fr=fopen("php://stdin","r");   // open our file pointer to read from stdin
        $input = fgets($fr,128);        // read a maximum of 128 characters
        $input = rtrim($input);         // trim any trailing spaces.
        fclose ($fr);                   // close the file handle
        return $input;                  // return the text entered
}

function setNowDate() {
        global $nowDT;
        $nowDT = date(getNowDtFormat());
}

function getNowDtFormat() {
    return "Y-m-d-H:i:s";
}

function getNowDate() {
    global $nowDT;

    return $nowDT;
}

function runCommand($cmd) {
        error_reporting( E_ALL );
        exec( $cmd, $out, $return_var);
	
        // print_r($out);
        // print_r($return_var);
}


function getFileList($dir, $onlyFileName = false) {
    $files = scandir($dir);
    $results = [];
    foreach ($files as $key => $value) {
		$path = realpath($dir . DIRECTORY_SEPARATOR . $value);
		$keyToAdd = $onlyFileName ?  $path : $key;
        if (!is_dir($path)) {
            $results[$keyToAdd] = $onlyFileName ? $value : $path;
        } 
    }
    
    return $results;
}