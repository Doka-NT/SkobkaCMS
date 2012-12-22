<?php
global $pdo;
$pdo->query("
CREATE TABLE IF NOT EXISTS `comments` (
  `cid` int(11) NOT NULL AUTO_INCREMENT,
  `parent` int(11) NOT NULL,
  `object_id` int(11) NOT NULL,
  `object` varchar(255) NOT NULL,
  `created` int(11) NOT NULL,
  `status` int(11) NOT NULL,
  `content` longtext NOT NULL,
  `uid` int(11) NOT NULL,
  PRIMARY KEY (`cid`),
  KEY `uid` (`uid`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;    
");
Notice::Message('Не забудьте выставить права на странице '.Theme::Render('link','admin/user/groups','настроек груп пользователей'));