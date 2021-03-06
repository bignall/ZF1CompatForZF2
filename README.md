ZF1CompatForZF2 [![Analytics](https://ga-beacon.appspot.com/UA-44748521-8/ZF1CompatForZF2/readme)](https://github.com/igrigorik/ga-beacon)
===============

By [Rosina Bignall](http://rosinabignall.com/)

ZF1CompatForZF2 is a compatibility layer for Zend Frameworks version 2 (ZF2)
to allow it to work in conjunction with Zend Frameworks version 1 (ZF1) apps.

The goal is to allow you to write ZF2 Apps (or use pieces of ZF2 in other
apps) that will work with a legacy ZF1 app.

Installation
============

To include it in your app via composer:

	"require": {
        "bignall/zf1compat-for-zf2": "dev-master"
    }

You may also clone the repo and set up your own loading

Zend Session Database SaveHandler Compatibility
===============================================

ZF2 tracks the session name as well as the session id.  ZF1 tracks only the
session id.  In order to make db session save handler compatible with ZF1
sessions (so that you can use the same session for both apps) we add a new
save handler Zf1DbTableGateway.

See a longer explanation on my blog: 

Usage
-----
Set up database table such as:

Oracle:

    CREATE TABLE SYSTEM_SESSION ( 
        ID CHAR(32) NOT NULL ,
        NAME CHAR(32),
        MODIFIED INTEGER,
        LIFETIME INTEGER,
        DATA CLOB,
        CONSTRAINT MIS_SESSION_ID_PK PRIMARY KEY(ID) )

Note that while the ZF2 docs state that NAME is required, for ZF1 compatibility, 
NAME should not be required as ZF1 does not use it.


    $tableGateway = new Zend\Db\TableGateway\TableGateway(...);
    $saveHandler  = new ZF1CompatForZF2\Zend\Session\SaveHandler\Zf1DbTableGateway($tableGateway, new Zend\Session\SaveHandler\DbTableGatewayOptions($saveHandlerOptions));
    $manager      = new Zend\Session\SessionManager();
    $manager->setSaveHandler($saveHandler);


Contributing 
============

Feel free to fork the repo, make your changes and send a pull request.  

Thanks
======

The initial work on this was done as part of my work for [Social & Scientific
Systems, Inc.](http://www.s-3.com/).
