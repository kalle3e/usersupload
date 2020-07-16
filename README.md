
Please note when running options 
--file, --dryRun and --create_table -h -u -p are also required (to connect to the database)
 
 
User Table Definition
The PostgreSQL table should contain at least these fields:
• name
• surname
• email (email should be set to a UNIQUE index).

Script Command Line Directives
The PHP script should include these command line options (directives):
• --file [csv file name] – this is the name of the CSV to be parsed
• --create_table – this will cause the PostgreSQL users table to be built (and no further action
will be taken)
• --dry_run – this will be used with the --file directive in case we want to run the script but not
insert into the DB. All other functions will be executed, but the database won't be altered
• -u – PostgreSQL username
• -p – PostgreSQL password
• -h – PostgreSQL host
• --help – which will output the above list of directives with details.
