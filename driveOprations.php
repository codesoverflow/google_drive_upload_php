<?php
require_once __DIR__ .'/config/config.php';

require __DIR__ . '/vendor/autoload.php';


require_once __DIR__ ."/customCodes/driveShared.php";
require_once __DIR__ ."/customCodes/driveGeneralOperations.php";
require_once __DIR__ ."/customCodes/driveUpload.php";
require_once __DIR__ ."/customCodes/driveDownload.php";
require_once __DIR__ ."/customCodes/driveDeletes.php";
require_once __DIR__ ."/customCodes/driveFullUpload.php";


ini_set('memory_limit', '4096M');


function showMainOptions() {

    
    $service = getService();


    printMessage("", 3);

    printMessage("==============================================================================================", 3);

    printMessage("Select one of the below options", 2);

    printMessage("1: Upload files", 1);
    printMessage("2: Download file", 1);
    printMessage("3: Delete specific uploaded file", 1);
    printMessage("4: Delete all uploaded file", 1);
    printMessage("5: List uploaded files", 1);
    printMessage("6: Upload specific file", 1);
    printMessage("7: Delete Uploading local files", 1);
    printMessage("8: Auto Process Drive files", 1);

    $selectedOption = readStdin();

    if($selectedOption == 1) {
        uploadFiles();
    } else if($selectedOption == 2) {
        listFiles($service, true);

        printMessage("Please enter the file name to download: ", 2);
        $fileToDownload = readStdin();

        fileToDownload($fileToDownload);
    } else if($selectedOption == 3) {
        listFiles($service, true);

        printMessage("Please enter the file name to delete: ", 2);
        $fileToDelete = readStdin();
        deleteSpecificFile($service, $fileToDelete);
    } else if($selectedOption == 4) {
        printMessage("Deleting all files: ", 2);
        deleteFiles($service);
    } else if($selectedOption == 5) {
        printMessage("Uploaded all files: ", 2);
        listFiles($service, true);
    } else if($selectedOption == 6) {
        showUploadingFiles();
        printMessage("Please enter the file name to upload: ", 2);

        $uploadSpecificFile = readStdin();

        uploadFiles($uploadSpecificFile);
    } else if($selectedOption == 7) {
        global $fileUploadPath;
        deleteUploadingFiles($fileUploadPath);
    }

    else if($selectedOption == 8) {
        startAutoProcessingDrive();
    }
    
    else {
        printMessage("Wrong option Selected ", 2);
    }

    showMainOptions();
    
}

showMainOptions();

//uploadFiles();

//listFiles($service, true);






















