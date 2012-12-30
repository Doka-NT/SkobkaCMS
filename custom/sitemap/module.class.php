<?php

class sitemap {

    public function Init() {
        Event::Bind('Cron', 'Sitemap::EventCron');
    }

    public static function EventCron() {
        self::_rebuild();
    }

    public function Menu() {
        return array(
            'admin/sitemap' => array(
                'rules' => array('Настройка сайта'),
                'title' => 'Обновить карту сайта',
                'callback' => 'sitemap::Rebuild',
                'group'=>'Настройки'
            ),
        );
    }

    private static function _rebuild() {
        $data = array(
            array(
                'loc' => '',
                'priority' => '1.0',
            ),
        );
        Event::Call('SitemapRebuild', $data);
        $items = '';
        foreach ($data as $url_item)
            $items .= self::_sitemap_item($url_item);
        file_put_contents('sitemap.xml', self::_sitemap($items));
        Notice::Message('<a href="/sitemap.xml">Карта сайта</a> обновлена');
    }

    public static function Rebuild() {
        self::_rebuild();
        Path::Back();
    }

    private static function _sitemap_item($data) {
        $base = 'http://' . $_SERVER['HTTP_HOST'];
        $out = '';

        $out .= '<loc>' . $base . $data['loc'] . '</loc>';

        if ($data['lastmod'])
            $out .= '<lastmod>' . $data['lastmod'] . '</lastmod>';
        if ($data['priority'])
            $out .= '<priority>' . $data['priority'] . '</priority>';
        if ($data['changefreq'])
            $out .= '<changefreq>' . $data['changefreq'] . '</changefreq>';
        return '<url>' . $out . '</url>';
    }

    private static function _sitemap($items) {
        return '<?xml version="1.0" encoding="UTF-8"?><urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . $items . '</urlset>';
    }

}