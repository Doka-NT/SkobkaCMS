<?php
class ContentEvent {
	public static function SitemapRebuild(&$data){
		global $pdo;
		$q = $pdo->query("SELECT * FROM content WHERE status = 1");
		while($res = $pdo->fetch_object($q)){
			$data[] = array(
				'loc'=>Path::Url('content/'.$res->id),
			);
		}
	}
}