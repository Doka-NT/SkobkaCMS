<?php
global $pdo;
$pdo->query("
CREATE TABLE IF NOT EXISTS `qbuilder` (
  `qbid` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `path` varchar(255) NOT NULL,
  `block` int(11) NOT NULL,
  `query` text NOT NULL,
  `template` longtext NOT NULL,
  `rules` text NOT NULL,
  PRIMARY KEY (`qbid`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;   
");