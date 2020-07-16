<?php
class FileOptions
{
    public $fileName;
    public $host;
    public $name;
    public $password;
    public $csvfile;
    public $optionAction;
    public $iscreate = false;
    public $isfile = false;

    public function __construct()
    {
        $shortopts = "";
        $shortopts .= "h:";  // : optional
        $shortopts .= "u:";
        $shortopts .= "p:";

        $longopts = array(
            "file::",           // Means insert so need to input host, username and password
            "create_table",     // Optional value. Need to input host, username and password
            "dry_run::",        // Also need to input csv file name
            "help",
        );
        $options = getopt($shortopts, $longopts);

        foreach ($options as $optionk => $optionv) {
            if ($optionk == 'help') {
                help();
            }
            elseif ($optionk == 'create_table') {
                $this->optionAction = 'create_table';
                $this->iscreate = true;
            } elseif ($optionk == 'file') {
                $this->optionAction = 'file';
                $this->fileName = $optionv;
                $this->isfile = true;
            } elseif ($optionk == 'dryRun') {
                $this->$optionAction = 'dryRun';
                $this->csvfile = $optionv;
            } elseif ($optionk == 'h' || $optionk == 'u' || $optionk == 'p')
            {
                if ($optionk == 'h') {
                    $this->host = $optionv;
                }
                if ($optionk == 'u') {
                    $this->name = $optionv;
                }
                if ($optionk == 'p') {
                    $this->password = $optionv;
                }
            } else {
                echo "\n Invalid Option. Enter again! \n";
            }
        }
    }
    public function checkActionOption()
    {
        if ($this->iscreate && isset($this->host) && isset($this->name) && isset($this->password)){
                return $this->optionAction;
        }
        elseif ($this->isfile && isset($this->host) && isset($this->name) && isset($this->password)) {
                    return $this->optionAction;
        }
        else {
            error();
        }
    }
}
class CsvFile
{
    public $csvfile;
    public $csvArray = array();

    public function __construct($csvfile)
    {
        $this->csvfile = $csvfile->fileName;
    }
    public function readCSV()
    {
        //Read in to array
        $fline = file($this->csvfile);
        for ($i = 1; $i < count($fline); $i++)  // Ignore heading line at $i=0
        {
            $line = explode(',', $fline[$i]);
            $name = $usersArr[$i]['name'] = trim($line[0]);
            $surname = $usersArr[$i]['surname'] = trim($line[1]);
            $email = $usersArr[$i]['email'] = trim($line[2]);
            $this->checkValidCsvName($this->csvfile);
            $this->checkInput($name, $surname, $email);
        }
    }
    public function checkValidCsvName($csvname)
    {
        return;
    }

    public function checkInput($name, $surname, $email)
    {

        $this->checkConvert($name, $surname); // Not yet working- not fully implemented


        $email_pregm = preg_match(
            "^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,3})$^", $email);
        if (!$email_pregm) {
            echo "\nemail error: " . $email ."\n";
            error();
            exit;
        }
    }

    public function checkConvert($name, $surname)
    {
        for ($i = 1; $i < 2; $i++)
            if (1 == $i)
            {
                $checkName = $name;
                $this->checkConvertEach($checkName, $isSurname);
            } elseif (2 == $i)
            {
                $checkName = $checkSurname;
                $isSurname = true;
                $this->checkConvertEach($checkName, $isSurname);
            }
    }

    public function checkConvertEach($name, $isSurname)
    {
        if (ctype_upper($name)) {
            {
                strtolower($name);
                ucfirst($name);
            }
        } elseif (ctype_lower($name)) {
            ucfirst($name);
        } else {
            strtolower($name); // not each char is lower then convert
            ucfirst($name);
        }
    }
}
class Db
{
    public $pdo;
    public $host;
    public $role;
    public $password;
    public $optionAction;
    public $dbname;
    public $tablename = 'users';
    public $pgrole     = 'postgres';
    public $pgpassword = 'postgres';
    public $pgdbname   = 'postgres';
    public $crdbname   = 'users';
    public $crtable    = 'users';
    public $file;

    /**
     * @param bool $ispgUser     as we need to use postgres account
     *  Using postgres account and DB before creating a role and database
     */
    public function __construct ($dbDetails)
    {
        $this->host = $dbDetails->host;
        $this->name = $dbDetails->name;
        $this->password = $dbDetails->password;
        $this->optionAction = $dbDetails->optionAction;
        $this->dbname = 'users';
        $this->role = 'users';
    }

    public function connect($ispgUser=false)
    {
        if ($this->ispgUser)
        {$dsn = "pgsql:host=$this->host;dbname=$this->pgdbname";}
        else
        {$dsn = "pgsql:host=$this->host;dbname=$this->dbname";}
        $options = [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ];
        try {
            if ($this->ispgUser)
            {$this->pdo = new PDO($dsn,$this->pgrole,$this->pgpassword,$options);}
            else
            {$this->pdo = new PDO($dsn,$this->role,$this->password,$options);}
        } catch (\PDOException $e) {
            throw new \PDOException($e->getMessage(), (int)$e->getCode());
        }
        return $this->pdo;
    }
    public function createUser()
    {
        $this->pdo = $this->connect($this->ispgUser=true);
        $sql = "CREATE ROLE $this->role WITH PASSWORD '$this->password' SUPERUSER CREATEDB"; // LOGIN here -remove CREATE ?
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();

        //$this->pdo = $this->connect($this->ispgUser=true);
        $sql = "ALTER ROLE $this->role WITH LOGIN";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();
        //    alter role  - for some reason I just couldn't create the role with LOGIN
        //    with the same above execution in one command
        //    Check - can be solved by GRANT ... ???  ++++++++++
        return;
    }

    public function createDatabase()  // Now we already have role created
    {
        $this->pdo = $this->connect($this->ispgUser=true);
        $sql = "CREATE DATABASE $this->crdbname
        with OWNER = $this->role
          encoding = 'UTF8'";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();
        $this->dbname = $this->crdbname;
        return;
    }
    public function createTable()
    {
        $this->pdo = $this->connect($this->ispgUser=false);
        // create table if not exits syntax ************* TO DO
        $sql = "CREATE TABLE $this->crtable (
        name 		varchar(40) NOT NULL,
        surname 	varchar(80) NOT NULL,
        email		varchar(100)  PRIMARY KEY,
        UNIQUE(email))";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();
        echo "\nCreate table successful\n";
        return;

    }

    public function insert()
    {
        $fline = file($this->file);
        $this->dbname = $this->crdbname;
        $pdo = $this->connect($this->ispgUser=false); // using $this->pdo so don't repeat -- TO DO TRY ++++++++++++++
        $table = $this->tablename;
        for ($i = 1; $i < count($fline); $i++)  // Ignore heading line at $i=0
        {
            $line = explode(',', $fline[$i]);
            $name = $usersArr[$i]['name'] = trim($line[0]);
            $surname = $usersArr[$i]['surname'] = trim($line[1]);
            $email = $usersArr[$i]['email'] = trim($line[2]);
            checkInput($name,$surname,$email);
            /*$email_pregm = preg_match(
                    "^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,3})$^", $email);
            if (!$email_pregm)
            {
                error();
            }*/

            try
            {

                $sql = "insert into $table (name,surname,email) values(:name, :surname,:email)";
                $stmt = $pdo->prepare($sql);
                $stmt->bindValue(':name', $name);
                $stmt->bindValue(':surname', $surname);
                $stmt->bindValue(':email', $email);
                $stmt->execute();                // TO DO +++++++++ COMMIT ROLLBACK  +++++++++++++++++++=
            }
            catch (\PDOException $e)
            {
                throw new \PDOException($e->getMessage(), (int)$e->getCode());
            }
        }
     }
}
    /*if ($dryRun) {
        $pdo->rollback();
        echo "\n Dry Run \n";
    }
    $pdo->commit();
}
checkInput();
            $email_pregm = preg_match(
                    "^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,3})$^", $email);
            if (!$email_pregm)
            {
                error();
            }
/*$sql = "insert into $table(name,surname,email) values(:name, :surname,:email)";
$stmt = $pdo->prepare($sql);
$stmt->execute();*/
    //  Insert one to test transaction



function insertOne($ispgUser, $dryRun, $host, $user, $password, $table)
{

    $pdo = $this->connect($ispgUser = false, $host, $user, $password, $table);

    try {
        $pdo->beginTransaction();

        $sql = "insert into $table(name,surname,email) values('Ringo', 'Black', 'bblack@gmail.com')";

        $stmt = $pdo->prepare($sql);
        $stmt->execute();
        if ($dryRun) {
            $pdo->rollback();
            echo "\n Dry Run \n";
            exit;
        }
        $pdo->commit();
    } catch (\PDOException $e) {
        $pdo->rollBack();
        throw $e;
    }
}
function help()
{
    echo "\n";
    echo "=========================================================================================";
    echo "\n";
    echo "--file [csv file name] – this is the name of the CSV to be parsed";
    echo "\n";
    echo "--create_table – this will cause the PostgreSQL users table to be built ";
    echo "\n";
    echo "(and no further action will be taken)";
    echo "\n";
    echo "--dry_run – this will be used with the --file directive in case we want ";
    echo "\n";
    echo "to run the script but not insert into the DB.";
    echo "\n";
    echo " All other functions will be executed, but the database won't be altered";
    echo "\n";
    echo "-u – PostgreSQL username";
    echo "\n";
    echo "-p – PostgreSQL password";
    echo "\n";
    echo "-h – PostgreSQL host";
    echo "\n";
    echo "--help – which will output the above list of directives with details.";
    echo "\n";
    echo "=========================================================================================";
    echo "\n";
}
function error()
{
    echo "\n";
    echo "=========================================";
    echo "\n";
    echo "Invalid Options. Enter again!";
    echo "\n";
    echo "=========================================";
    echo "\n";
    exit;
}
?>