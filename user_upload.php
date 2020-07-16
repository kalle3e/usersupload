<?php
include 'libraries.php';

$foption = new FileOptions();
$optionActn = $foption->checkActionOption();
if ($optionActn)
{
     process($optionActn, $foption);
}


function process($optionActn, $dbDetails){
    //  connect DB (PDO) if $options is create_file, file or dryRun
    if ("create_table" == $optionActn)
    {
        /**
         *   SELECT from DB check if exists - ELSE - error
         */
        $database = new Db($dbDetails);
        $database->createUser();
        $database->createDatabase();
        $database->createTable();
        exit;
    }
    if (('file' == $optionActn) || 'dryRun' == $optionActn)
    {
        /**
         *   Read in csv then SELECT from DB check if exists?
         *
         *   Before insert, data exist? SELECT DB against DataArray
         */
        // checkCSV then check in DB exist
        $fileOptions = new FileOptions();
        $csvFile = new CsvFile($fileOptions);
        if('dryRun' == $fileOptions->$optionActn)
        {$optionActn = $fileOptions->optionAction;}
        $csvFile->readCSV();
        //$database = new $Db($fileOptions);
        new $Db($fileOptions);

    }
}
?>