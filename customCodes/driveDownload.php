<?php

function downloadFile($file, $downLoadPath, $service) {

    $fileId = $file['id'];
    $fileName = $file['name'];
    
    $content = $service->files->get($fileId, array("alt" => "media"));

    printMessage("Downloading file = $fileName : ", 3);

    // Open file handle for output.

    $outHandle = fopen($downLoadPath, "w+");

    // Until we have reached the EOF, read 1024 bytes at a time and write to the output file handle.

    $downloadedFileAmount = 0;
    $amountToDownload = 1024;

    while (!$content->getBody()->eof()) {
        
        fwrite($outHandle, $content->getBody()->read($amountToDownload));
        $downloadedFileAmount = $amountToDownload + $downloadedFileAmount;
        $downloadedSizeMb = convertToMB($downloadedFileAmount);
        printOnSameLine($downloadedSizeMb);
    }

    // Close output file handle.

    fclose($outHandle);


    printMessage("Downloading complete of file = $fileName : ", 3);

}


function fileToDownload($fileName) {

    global $downLoadPath;
    $service = getService();

    $files = listFiles($service);

    foreach($files as $file) {
        $fileId = $file['id'];
        $name = $file['name'];

        if($fileName == $name) {
            $downLoadFilePath = "$downLoadPath$name";
            downloadFile($file, $downLoadFilePath, $service);
            break;
        }

        
    }
}




















