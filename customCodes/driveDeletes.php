<?php

function deleteFile($service, $file) {

  $fileId = $file['id'];
  $fileName = $file['name'];
  printMessage("Deleting file: $fileName");
  try {
    $service->files->delete($fileId);
    printMessage("Deleted file: $fileName");
  } catch (Exception $e) {
    print "An error occurred: " . $e->getMessage();
    printMessage('', 3);
  }
}

function deleteFiles($service) {
    $listFiles = listFiles($service);

    foreach($listFiles as $listFile) {
        deleteFile($service, $listFile);
    }
}

function deleteSpecificFile($service, $fileToDelete) {
    $listFiles = listFiles($service);

    foreach($listFiles as $listFile) {
      if($listFile['name'] == $fileToDelete) {
        deleteFile($service, $listFile);
        break;
      }
      
    }
}





















