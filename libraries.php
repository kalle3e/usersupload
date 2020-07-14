<?php
class processSavedOptions
{
    //this is saved options read in an array
    public $savedOptions = array();


    private function process()
    {
        if(isset($this->savedOptions['create_table']) && isset($this->optionS['h'])  && isset($this->optionS['p']))
        {
            /**
             *   check table doesn't yet exist SELECT table
             */
        }
        else
        {
            error();
        }

    }


}
class ProcessOptions
{
    public $fileName;
    public $host;
    public $userName;
    public $password;
    public $createDb;
    public $dryRun = false;
    public $help = false;
    public $optionk;
    public $optionv;
    public $optionsE;
}
class Db
{
    public $host;
    public $role;
    public $password;
    public $dbname;
    public $tablename = 'testusers';
    public $pgrole     = 'postgres';
    public $pgpassword = 'postgres';
    public $pgdbname   = 'postgres';
    public $crdbname   = 'testusers'; //**************   change the name to users ***********
    public $crtable    = 'testusers'; //**************   change the name to users ***********
    public $ispgUser = false;
    public $dryRun = false;

    /**
     * @param bool $ispgUser     as we need to use postgres account
     *  Using postgres account and DB before creating a role and database
     */
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
            {$pdo = new PDO($dsn,$this->pgrole,$this->pgpassword,$options);}
            else
            {$pdo = new PDO($dsn,$this->role,$this->password,$options);}
        } catch (\PDOException $e) {
            throw new \PDOException($e->getMessage(), (int)$e->getCode());
        }
        return $pdo;
    }
    public function createUser()
    {
        $pdo = $this->connect($this->ispgUser=true);
        $sql = "CREATE ROLE $this->role WITH PASSWORD '$this->password' SUPERUSER CREATEDB"; // LOGIN here -remove CREATE ?
        $stmt = $pdo->prepare($sql);
        $stmt->execute();

        $pdo = $this->connect($this->ispgUser=true);
        $sql = "ALTER ROLE $this->role WITH LOGIN";
        $stmt = $pdo->prepare($sql);
        $stmt->execute();
        //    alter role  - for some reason I just couldn't create the role with LOGIN
        //    with the same above execution in one command
        //    Check - can be solved by GRANT ... ???  ++++++++++
        return;
    }

    public function createDatabase()  // Now we already have role created
    {
        $pdo = $this->connect($this->ispgUser=true);
        $sql = "CREATE DATABASE $this->crdbname
        with OWNER = $this->role
          encoding = 'UTF8'";
        $stmt = $pdo->prepare($sql);
        $stmt->execute();
        $this->dbname = $this->crdbname;
        return;
    }
    public function createTable()
    {
        $pdo = $this->connect($this->ispgUser=false);
        // create table if not exits syntax ************* TO DO
        $sql = "CREATE TABLE $this->crtable (
        name 		varchar(40) NOT NULL,
        surname 	varchar(80) NOT NULL,
        email		varchar(100)  PRIMARY KEY,
        UNIQUE(email))";
        $stmt = $pdo->prepare($sql);
        $stmt->execute();
        echo "\nCreate table successful\n";
        return;

    }

public function insert($host,$user,$password,$dbname,$crdbname)
    {
        createTable($host, $user, $password);
        $pdo = $this->connect($ispgUser = false, $host, $user, $password,$dname);
        $fline = file($file);
        for ($i = 1; $i < count($fline); $i++)  // Ignore heading line at $i=11
        {
            $line = explode(',', $fline[$i]);
            $usersArr[$i]['name'] = trim($line[0]);
            $usersArr[$i]['surname'] = trim($line[1]);
            $usersArr[$i]['email'] = trim($line[2]);
        }
        //var_dump($usersArr) ;
        //for ($j = 1; $j <= count($usersArr); $j++) {
        for ($j = 1; $j <= 2 ; $j++) {
            /*echo $usersArr[$j]['name'] . "\n";
            echo $usersArr[$j]['surname'] . "\n";
            echo $usersArr[$j]['email'] . "\n";*/
            //var_dump($usersArr[$j]['name']);
           /* $nameq = $usersArr[$j]['name'];
            $surnameq = $usersArr[$j]['surname'];
            $emailq = $usersArr[$j]['email'];*/
            //$stmt->bindValue(':name', "$usersArr[$j][\'name\']");
            $stmt->bindValue(':name', $usersArr[$j]['name']);
            $stmt->bindValue(':surname', $usersArr[$j]['surname']);
            $stmt->bindValue(':email', $usersArr[$j]['email']);
            /*$stmt->bindValue(':name', $nameq);
            $stmt->bindValue(':surname', $surnameq);
            $stmt->bindValue(':email', $emailq);*/
            $stmt->execute();
            $sql = "insert into $table(name,surname,email) values(:name, :surname,:email)";
            $stmt = $pdo->prepare($sql);
            $stmt->execute();
        }
    }
    /*$stmt->bindValue(':name', $usersArr[$j]['name']);
    $stmt->bindValue(':surname', $usersArr[$j]['name']);
    $stmt->bindValue(':email', $usersArr[$j]['email']);
    $stmt->execute();
    $sql = "insert into $table(name,surname,email) values(:name, :surname,:email)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute();*/
    //$stmt->bindValue(':name', Kathie);

    /*if ($dryRun) {
        $pdo->rollback();
        echo "\n Dry Run \n";
    }
    $pdo->commit();
}
}

/*$sql = "insert into $table(name,surname,email) values(:name, :surname,:email)";
$stmt = $pdo->prepare($sql);
$stmt->execute();*/
    //  Insert one to test transaction
    public function insertOne($ispgUser, $dryRun, $host, $user, $password, $table)
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
}
?>