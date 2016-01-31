phalphp 
=================

 基于phalcon框架的PHP应用，可用于Restful API,命令行应用及Web应用。
 
A PHP  application  for APIs,cli,and webapp using the Phalcon framework.

Requirements
---------
PHP 5.4 or greater


Required PHP Modules
- OpenSSL
- Phalcon (http://phalconphp.com/en/download)
- PDO-MySQL


To check for those modules is installed/enabled for CLI use
```bash
$ php -m | egrep "(phalcon|pdo_mysql|openssl)"
phalcon
pdo_mysql
openssl
```

Database Configuration
--------------
Open  `phalphp/app/config.php` and setup your database connection credentials

```php
$settings = array(
        'database' => array(
                'adapter' => 'Mysql', /* Possible Values: Mysql, Postgres, Sqlite */
                'host' => 'your_ip_or_hostname',
                'username' => 'your_username',
                'password' => 'your_password',
                'name' => 'your_database_schema',
                'port' => 3306
        ),
);
```

Import the tables into your mysql database
```bash
mysql -u root -p your_database_schema < phalphp/mysql.data.sql
```
Import the tables into your Postgres Server
```bash
psql -U root -W -f postgres.data.sql your_database_schema
```


usage:
------------

 [API](README-api.md)
 
 [CLI](README-cli.md)
 
 [Web APP](README-app.md)

 
