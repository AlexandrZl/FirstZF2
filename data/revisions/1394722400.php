<?php

class Db_Revision_1394722400
{

    public $upQuery = <<<EOD
     CREATE TABLE `posts` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `title` varchar(256) NOT NULL,
    `text` text(400) NOT NULL,
    PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1;



EOD;

    public $downQuery = <<<EOD



EOD;

    public function preUp(\Zend\Db\Adapter\Adapter $db)
    {
    }

    public function postUp(\Zend\Db\Adapter\Adapter $db)
    {
    }

    public function preDown(\Zend\Db\Adapter\Adapter $db)
    {
    }

    public function postDown(\Zend\Db\Adapter\Adapter $db)
    {
    }


}

