<?php

function readVideoChunk ($handle, $chunkSize)
{
    $byteCount = 0;
    $giantChunk = "";
    while (!feof($handle)) {
        // fread will never return more than 8192 bytes if the stream is read buffered and it does not represent a plain file
        $chunk = fread($handle, 8192);
        $byteCount += strlen($chunk);
        $giantChunk .= $chunk;
        if ($byteCount >= $chunkSize)
        {
            return $giantChunk;
        }
    }
    return $giantChunk;
}


function uploadFile($client, $service, $fileName, $file_to_uplaod) {


    $fileType = mime_content_type($file_to_uplaod);

    $file = new Google_Service_Drive_DriveFile();
    $file->name = $fileName;
    $chunkSizeBytes = 1 * 1024 * 1024;

    $client->setDefer(true);
    $request = $service->files->create($file);


    // Create a media file upload to represent our upload process.
    $media = new Google_Http_MediaFileUpload(
        $client,
        $request,
        $fileType,
        null,
        true,
        $chunkSizeBytes
    );

    $media->setFileSize(filesize($file_to_uplaod));

    printMessage("Uploading file = $fileName : ", 2);

    $status = false;
    $handle = fopen($file_to_uplaod, "rb");
    $uploadedAmount = 0;
    while (!$status && !feof($handle)) {
        // read until you get $chunkSizeBytes from TESTFILE
        // fread will never return more than 8192 bytes if the stream is read buffered and it does not represent a plain file
        // An example of a read buffered file is when reading from a URL
        $chunk = readVideoChunk($handle, $chunkSizeBytes);
        $status = $media->nextChunk($chunk);

        $uploadedAmount = $chunkSizeBytes + $uploadedAmount;
        $uploadedMb = convertToMB($uploadedAmount);
        printOnSameLine($uploadedMb);
    }

    $result = false;
    if ($status != false) {
        $result = $status;
    }

    fclose($handle);

    printMessage("Uploading complete of file = $fileName  ", 2);
      
    return $result;
}

function uploadFiles($uploadSpecificFile = null) {
    global $fileUploadPath;
    
    getDirContents($fileUploadPath, false, $uploadSpecificFile);
}

function showUploadingFiles() {
    global $fileUploadPath;
    
    getDirContents($fileUploadPath, true);
}


function getDirContents($dir, $showUploadingFiles = false, $uploadSpecificFile = null, $results = array()) {
    $files = scandir($dir);

    foreach ($files as $key => $value) {
		$path = realpath($dir . DIRECTORY_SEPARATOR . $value);
		
        if (!is_dir($path)) {
            $results[] = $path;
        } else if ($value != "." && $value != "..") {
			//getDirContents($path, $results);
			
			getDirContents($path, $showUploadingFiles, $uploadSpecificFile, []);
			
			$results[] = $path;
            
        }
	}
    
    if(!count($results)) {
        printMessage("Nothing to upload", 3);
        return;
    }

    if($showUploadingFiles) {
        foreach($results as $fileToUpload) {
            $fileName = basename($fileToUpload);
            printMessage($fileName, 1);
        }
        return;
    }

    

    $client = getClient();
    $service = getService($client);
    
	foreach($results as $fileToUpload) {
        $fileName = basename($fileToUpload);
        if($uploadSpecificFile === null) {
            uploadFile($client, $service, $fileName, $fileToUpload);
        } else if($uploadSpecificFile == $fileName) {
            uploadFile($client, $service, $fileName, $fileToUpload);
        }
        
    }

    return $results;
}

function deleteUploadingFiles($folder_path) {
        $files = glob($folder_path.'/*');  
   
        // Deleting all the files in the list 
        foreach($files as $file) { 
            if(is_file($file)) {
                unlink($file);  
            } else {
                deleteUploadingFiles($file);
            }
        } 
}

















