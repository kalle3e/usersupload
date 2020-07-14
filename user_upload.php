<?php
include 'libraries.php';

$optionsInput = array();
$database = new Db();

$shortopts  = "";
$shortopts .= "h:";  // : optional
$shortopts .= "u:";
$shortopts .= "p:";

$longopts  = array(
    "file::",           // Means insert so need to input host, username and password
    "create_table",     // Optional value. Need to input host, username and password
    "dry_run::",        // Also need to input csv file name
    "help",
);
$options = getopt($shortopts, $longopts);

foreach ($options as $optionk => $optionv)
{
    if ($optionk == 'help')
    {
        help();
    }
    elseif($optionk == 'create_table')
    {
        $optionsInput['create_table'] = true;
    }
    elseif($optionk == 'h' || $optionk == 'u' || $optionk == 'p' )//  Need to enter file as well (Table has to exist for insert)
    {
        if ($optionk == 'h' )
        {
            $optionsInput['h'] = $optionv;
        }
        if ($optionk == 'u' )
        {
            $optionsInput['u'] = $optionv;
        }
        if ($optionk == 'p' )
        {
            $optionsInput['p'] = $optionv;
        }
    }
    elseif($optionk == 'file')
    {

        $optionsInput['file'] = $optionv;
    }
    elseif($optionk == 'dryRun') //  Need to enter file as well (Table has to exist for insert)
    {
        $optionsInput['dryRun']= $optionk;
    }
    else
    {echo "\n Invalid Option. Enter again! \n";}
}

//  Having saved the input options
if(isset($optionsInput['create_table']) && isset($optionsInput['h']) && isset($optionsInput['u'])  && isset($optionsInput['p']))
//connect(true, $user, $password, $db);
//createuser($rolep, $passwordp); // by admin
//createDatabase($role,$password,$db); // non-admin
//createTable($table); // non-admin
//insert($table);
    {
        /**
         *   check table doesn't yet exist SELECT table
         */

        $database->host = $optionsInput['h'];
        $database->role = $optionsInput['u'];
        $database->password = $optionsInput['p'];
        $database->createUser();
        $database->createDatabase();
        $database->createTable();
        exit();
    }
    else
    {
        error();
    }
if(isset($optionsInput['file']) && isset($optionsInput['h']) && isset($optionsInput['u'])  && isset($optionsInput['p']))
{
    //if input file is abcdefghij (10 char long) of .csv
    $database->host = $optionsInput['h'];
    $database->role = $optionsInput['u'];
    $database->password = $optionsInput['p'];
    $database->insert();
}
?>