<?php

class Banner
{
    public $content;

    public function show()
    {
        return $this->content;
    }

    public static function widget()
    {
        $db = DB::instance();

        $content = '';
        if ($result = $db->mysqli->query('SELECT * FROM `' . DB_PREFIX . '_banner` ORDER BY `nom` LIMIT 3')) {
            $content .= '
        <div id="banner_div">
             <ul>';
            while ($row = $result->fetch_assoc()) {
                $content .= '<li>';
                if($row['url']!='')$content.='<a href="' . $row['url'] . '" class="link-title">';
                if (file_exists('./uploads/banner/mini/' . $row['id'] . '.jpg')) {
                    $content .= '<img src="/uploads/banner/mini/' . $row['id'] . '.jpg" alt="' . $row['tit'] . '">';
                }
                if($row['url']!='')$content .= '</a>';
                $content.='</li>
                ';
            }
            $content .= '</ul>
                </div>';
        }
        return $content;
    }

    static function admItem($arrValues, $action, $folder = '')
    {
//----------------------------------------------------------------------------------------------------------------------
//отредактировать внешний вид пункта списка, в паттернах название столбца значение которого нужно вставить
//----------------------------------------------------------------------------------------------------------------------
        $content = '
<li id="arrayorder_%_ID_%" >
<table border=0 class="%_CLASS_%"><tr><td width="100">';
        if (file_exists('../uploads/' . $folder . '/mini/' . $arrValues['id'] . '.jpg')) $content .= '<img src="/uploads/' . $folder . '/mini/' . $arrValues['id'] . '.jpg?' . time() . '">';
        $content .= '</td><td>%_NAME_%<br>%_ALT_%</td><td width="200"><a href="./' . $action . '?a=2&id=%_ID_%">редактировать</a><br><a href="./' . $action . '?a=3&id=%_ID_%" onclick="return confirm (\'Точно удалить запись?\');">удалить</a></td></tr></table></li>';
//----------------------------------------------------------------------------------------------------------------------
        foreach ($arrValues as $key => $value) {
            $arrFrom[] = '%_' . mb_strtoupper($key) . '_%';
            $arrTo[] = $value;
        }
        return str_replace($arrFrom, $arrTo, $content);
    }

    public static function install()
    {
        mkdir(UPLOADS_PATH . '/banner');
        mkdir(UPLOADS_PATH . '/banner/mini');
        mkdir(UPLOADS_PATH . '/banner/tmp');
        return "
        CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "_banner` (
            `id` int(10) unsigned NOT NULL auto_increment,
            `nom` int(11) NOT NULL default '0',
            `name` text NOT NULL,
            `src` text NOT NULL,
            `url` text NOT NULL,
            `alt` text NOT NULL,
          PRIMARY KEY (`id`)
        ) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;";
    }
}