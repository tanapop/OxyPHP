![oxylogo.png](https://bitbucket.org/repo/p6xdM7/images/2318018827-oxylogo.png)

# OxyPHP v 0.9 Stable #

OxyPHP is an open source Object Oriented PHP framework project focused in security, performance, friendly interface and Automation of development.

Its current version is 0.9 stable, with the main features of: friendly URL, structured SQL generator, automatic helpers loader and automated database's CRUD generator, which creates, with one click, an entire MVC structure that does the basic database operations(Create, Read, Update, Delete) and the end-user views.

It already comes with some useful stuff included, like: JQuery, bootstrap and other helpers.

It is my main open source project and i am currently running against time to deploy the 1.0 version stable with the new features of Database's response data mapping and CRUD generation for related database entities.



## Next Steps ##


After releasing the version 1.0 stable, the community, that began to pop up, will implement, for the next versions, the following features:

- GNU/GPL License

- Complete documentation and website;

- GUI database administrator integrated;

- System Log Patterns;

- Security optimization;

- Garbage collector and other performance issues;

- Automatic site navigator creator;

- Huffman algorithm for request data by default (security and performance);

- PHP7 and NginX support;



## How do I get set up? ##


**> Requirements:**

- Apache 2 Web server

- PHP 5.5 or greater

- MySQL Database Server

- Virtual Host(for the friendly URL feature)


**> Steps:**

**1.** Put the root OxyPHP folder in your web server directory.(Ex.: "/var/www/html/" for Linux's Apache2 servers)

**2.** Create your database on your MySQL server

**3.** Configure your application in "config.ini" file, located at root OxyPHP's directory.(See the next section for further info)

**4.** Create your virtual host on your Apache. (If you don't know how to do it, please follow this tutorial: http://foundationphp.com/tutorials/apache_vhosts.php)



## Configuring my application ##

Within your OxyPHP's root directory there is a file named "config.ini". When you open for edition, this file, you will find some divisions. Here i will explain those one by one.

**1. DATABASE: ** 

Here's 7 database configuration constants:
```
#!php

- DBNAME: A string containing the name of your application's database.(Ex.: "foo_app_db");

- DBHOST: A string containing your application's database host address.(Ex.: "localhost");

- DBUSER: A string containing the user name for your database.(Ex.: "foo_app_db_user");

- DBPASS: A string containing your user's database password.

- DBTYPE: A string containing your database type. Default value is "mysql". (it does not support other database types in the current version)

- DBCLASS: A string containing the PHP library that the system will use to manage database. Currently it supports "mysqli" and "pdo"(default);

- DBCONNECTION_MAX_TRIES: An integer containing the number of tries that the application will do to connect to database before it fails. Default value is 5. (Database connection persistence)

```

**2. SUPER_ADMIN_USER: **

You can use these constants to create super admin users.

```
#!php

- ADMIN_NAME: A string containing the name of Super Admin User.(Ex.: "Super Administrator");

- ADMIN_EMAIL: A string containing an email address that can be used to contact the application's administrator or login;

- ADMIN_PASS: A string containing a password for this application's Super Administrator User, for login purposes;

```

**3. SYSTEM: **

Here's the main system configs constants. Know what you are doing before change some of these.

```
#!php

- DEFAULT_CONTROLLER: A string containing the default module's name of your application. It is the controller that will work on your home page.(default value is "home");

- DEFAULT_METHOD: Is your main method's name. It must be within your default controller.(default value is "index");

- SETUP_MODE: A boolean value. 1 for activated, 0 for inactive. Is the setup mode that you will use to generate your CRUD and MVC files automatically. See more in Running Application Section.(default value is 1);

- HANDLE_ERROR_TYPES: The PHP flags for error handling. Do not change this unless you know exactly what you're doing.

- HELPERS_AUTOLOAD: A boolean value that tells the system if you want to load helpers automatically. 1 to Yes, 0 to No. To see how helpers works in OxyPHP, go to Helpers section, within this documentation.(default value is 1);

```

**4. HELPERS: **

Here is where you configure the helpers you want to autoload. You give the helper's name and URL. You can pass, within URL, some arguments, if the helper's constructor method requires.

Example:
```
#!php
PHPAlert = "phpalert/class.phpalert.php?args[]=/helpers"
PHPMailer = "phpmailer/PHPMailerAutoload.php"
```
There is already 3 native helpers: [PHPAlert](https://bitbucket.org/gabriel-guelfi/php-alert), a tool to show up alerts to your end-users, [Insecticide](https://bitbucket.org/gabriel-guelfi/insecticide), which is a tool for debugging purposes, and [PHPMailer](https://github.com/PHPMailer/PHPMailer),a third party gadget for sending emails from your application.

All helpers loaded is available in the global instance "$this->helpers", and you can access it from almost all places within the application.

Example:
```
#!php

Inside a controller you can do:

<?php
$this->helpers->phpalert->add("An end-user alert.");
$this->helpers->phpalert->show();
?>
```



## Running Application ##

After creating your application's database and setting up the virtual host, just access the domain set in that Virtual Host:

```
#!php
http://oxyphp.local/
```

It will show up the Module(MVC) Generator screen, that will read your database and list the tables in it:

![Screenshot from 2017-03-21 15-19-18.png](https://bitbucket.org/repo/p6xdM7/images/3280251291-Screenshot%20from%202017-03-21%2015-19-18.png)
**:In the example shown in image above, i have a database with 4 tables in it: bar, example, foo and test.
*


Then you can click on the option, in each table listed, to generate a module for your application, based on the database table's structure. Each module created is a set of 4 files: a Model, 2 Views(one for listing data from the DB and one for registering/editing) and a Controller. You can edit each of these, accessing the respective "Models", "Views" and "Controllers" directories that will be generated automatically at your application's root folder. This files already do CRUD operations on your application. (Create, Read, Update, Delete).

After creating a module, you can access it using the friendly URL syntax in your browser's navigation bar:

```
#!php
http://oxyphp.local/foo/
```

You shall see something like this:

![Screenshot from 2017-03-21 15-28-55.png](https://bitbucket.org/repo/p6xdM7/images/1366632440-Screenshot%20from%202017-03-21%2015-28-55.png)

To deploy your application and stop showing the Module Generator screen at your home page, simply go to config.ini file and set SETUP_MODE = 0.



### Notes ###

- It is very important that you create your database in the appropriated manner, because the OxyPHP CRUD generator, will create your MVC files based on your database structure.

- Sorry for poor documentation. After releasing version 1.0, i will create a web site with full documentation in a similar layout to manual.php.net.



### Who am i? ###

My name is Gabriel Valentoni Guelfi. I'm an I.T. professional, specialized in PHP and web development. And a technology enthusiastic.

#### Contact me: ####
* Skype: gabriel-guelfi
* Email: gabriel.valguelfi@gmail.com
* Website: [gabrielguelfi.com.br](http://gabrielguelfi.com.br)
* Blog: [Develog](http://blog.gabrielguelfi.com.br)
* Linkedin: [Gabriel Guelfi](https://br.linkedin.com/in/gabriel-valentoni-guelfi-30ba8b4b)