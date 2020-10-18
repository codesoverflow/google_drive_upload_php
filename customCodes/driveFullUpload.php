<?php
$nowDT = date(getNowDtFormat());

$cmpDt = array(
        'prefix' => getDbPrefix(),
        'sufix' => getDbSufix()
);


function setCustomSortPrefix($prfx) {
        global $cmpDt;

        $cmpDt['prefix'] = $prfx;
}

function setCustomSortSufix($sufix) {
        global $cmpDt;
        $cmpDt['sufix'] = $sufix;
}

function getDbPrefix() {
        return 'db-';
}

function getDbSufix() {
        return '.sql';
}

function getZipPrefix() {
        return 'file-';
}

function getZipSufix() {
        return '.zip';
}

function dbBackup() {
        global $fileUploadPath, $nowDT, $dbConfigs;
        $prefix = getDbPrefix();
        $sufix = getDbSufix();
        

        foreach($dbConfigs as $dbConfig) {

                $dbName = $dbConfig['dbName'];
                $mysql_dump = "$fileUploadPath$prefix$dbName-$nowDT$sufix";

                $dumpCmd = "mysqldump --complete-insert  --no-create-db   --user={$dbConfig['user']} --host={$dbConfig['host']} --password={$dbConfig['password']}  {$dbConfig['dbName']} > $mysql_dump  2>&1 | grep -v 'Warning: Using a password'";        
                                
                runCommand($dumpCmd);
        }
}

function zipBackup() {
        global $fileUploadPath, $nowDT, $zipCommand, $zipBackupsFrom;
        $prefix = getZipPrefix();
        $sufix = getZipSufix();
        

        foreach($zipBackupsFrom as $backUpName => $zipBackupFrom) {

                $zipBackupTo = "$fileUploadPath$prefix$backUpName-$nowDT$sufix";

                $zipCmd = "zip -r $zipBackupTo $zipBackupFrom  -x \*.zip  *wp-content/cache/supercache/*";
               
                runCommand($zipCmd);
        }
        
        
}


function getLocalDeletingPresrvingElements($prefix, $sufix) {
        global $fileUploadPath;
        $files = getFileList($fileUploadPath, true);
        

        $filesNew = array();

        foreach($files as $fileKey => $file) {
                if(getStrBetween($file, $prefix, $sufix)) {
                        $filesNew[$fileKey] = $file;
                }
        }
        

        return getDeletingPresrvingElements($filesNew);
}


function getStrBetween($string, $start, $end){
    $string = ' ' . $string;
    $ini = strpos($string, $start);
    if ($ini == 0) return '';
    $ini += strlen($start);
    $len = strpos($string, $end, $ini) - $ini;
    return substr($string, $ini, $len);
}

function deleteLocalFiles($prefix, $sufix) {
        setCustomSortPrefix($prefix);
        setCustomSortSufix($sufix);

        
        $delPreservFiles = getLocalDeletingPresrvingElements($prefix, $sufix);
        
        

        
        
        foreach($delPreservFiles['deletingEles'] as $keyDelFile => $delFile) {
                //unlink($keyDelFile);

                $deleteFileCmd = "rm $keyDelFile";

                //echo $deleteFileCmd; echo "=======";

                runCommand($deleteFileCmd);
        }

}

function deleteDriveFiles($prefix, $sufix) {
        $service = getService();
        setCustomSortPrefix($prefix);
        setCustomSortSufix($sufix);
        $files = listFiles($service); 

        $filesNew = array();

        foreach($files as $fileKey => $file) {
                if(getStrBetween($file->name, $prefix, $sufix)) {
                        $filesNew[$file->name] = $file->name;
                }
        }
        

        $delPreservFiles = getDeletingPresrvingElements($filesNew);


        foreach($delPreservFiles['deletingEles'] as $keyDelFile => $fileToDelete) {
                deleteSpecificFile($service, $fileToDelete);
        }
}

function getDeletingPresrvingElements($files) {
        
        //uksort($files, "compareDtCustom");

        $preservingEles = array();
        $deletingEles = array();

        $fileValues = array_values($files);
        $fileKeys = array_keys($files);
        
        $fileTimeStamps = [];
        foreach($fileValues as $fileValue) {
                $fileTimeStamps[] = getTimeStampOfFileName($fileValue);
        }
        $lastInsertedFileTimeStamp = max($fileTimeStamps);               

        foreach($fileValues as $fileIndex => $fileValue) {
                $file = $fileValues[$fileIndex];
                $fileKey = $fileKeys[$fileIndex];
                $fileTimeStamp = $fileTimeStamps[$fileIndex];

                if($fileTimeStamp < $lastInsertedFileTimeStamp) {
                        $deletingEles[$fileKey] = $file;
                        continue;
                }

                $preservingEles[$fileKey] = $file;
        }


        return array(
               'preservingEles' => $preservingEles,
               'deletingEles' => $deletingEles
       );
}



function getTimeStampOfFileName($stringContainsDate) {
        global $cmpDt, $zipBackupsFrom, $dbConfigs;

        

        $stringsToRemove = [];

        foreach($dbConfigs as $dbConfig) {
                $dbName = $dbConfig['dbName'];
                $stringsToRemove[] = $dbName;
        }

        foreach($zipBackupsFrom as $backUpName => $zipBackupFrom) {                
                $stringsToRemove[] = $backUpName;
        }

        
        foreach($stringsToRemove as $stringToRemove) {
                $stringContainsDate =  str_replace($stringToRemove."-","",$stringContainsDate);
        }

        


        $stringContainsDate =  str_replace($cmpDt['prefix'],"",$stringContainsDate);
        $stringContainsDate =  str_replace($cmpDt['sufix'],"",$stringContainsDate);


        $dateTime = DateTime::createFromFormat( getNowDtFormat(), $stringContainsDate, new DateTimeZone('Indian/Antananarivo'));
        $timestamp = $dateTime->getTimestamp();

        return $timestamp;
}



function startAutoProcessingDrive() {

        setNowDate();

        
        //deleteLocalFiles(getDbPrefix(), getDbSufix()); die;

        dbBackup();
        zipBackup();

        //die;
        

        // Delete local old backups
        deleteLocalFiles(getDbPrefix(), getDbSufix()); 
        deleteLocalFiles(getZipPrefix(), getZipSufix());

        //die;
        // Upload backups from upload folder
        uploadFiles();

        


        // Delete drive old backups
        deleteDriveFiles(getDbPrefix(), getDbSufix());
        deleteDriveFiles(getZipPrefix(), getZipSufix());
}