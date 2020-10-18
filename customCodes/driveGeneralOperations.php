<?php


function listFiles($service, $print = false) {
    

    $files = $service->files->listFiles();

    if(!$print) {
        return $files;
    }
    
    foreach ($files as $file) {
        // print_r($file);
        printMessage($file['name'], 2);
    }
}

