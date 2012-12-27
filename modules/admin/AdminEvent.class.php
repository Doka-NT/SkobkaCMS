<?php
class AdminEvent {
    public static function EventUpdate($cmsVersion){
        $sql = array();
        $startUpdateFromVersion = 170;
        $sql[200][] = "ALTER TABLE  `blocks` ADD  `ugid` INT NOT NULL DEFAULT  '0' AFTER  `not_pages` , ADD INDEX (  `ugid` )";
        $sql[200][] = "ALTER TABLE  `blocks` ADD  `show_title` TINYINT( 1 ) NOT NULL DEFAULT  '1' AFTER  `ugid`";
        return Core::UpdateDatabase($cmsVersion, $startUpdateFromVersion, $sql);
    }
}