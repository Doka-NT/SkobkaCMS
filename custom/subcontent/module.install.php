<?php
global $pdo;
$res = $pdo->query("CREATE TABLE IF NOT EXISTS `subcontent` (
  `scid` int(11) NOT NULL AUTO_INCREMENT,
  `content_id` int(11) NOT NULL,
  `parent` int(11) NOT NULL,
  `weight` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`scid`),
  KEY `content_id` (`content_id`,`parent`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;");
if($res)
	Notice::Message('Модуль подматериалы установлен');