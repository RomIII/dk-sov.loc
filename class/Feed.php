<?php

class Feed
{
    public $moduleName = 'feed';
    public $content;

    public function show()
    {
        return $this->content;
    }

    public function mainList()
    {
        $db = DB::instance();
        $pagination = new Pagination(DB_PREFIX . '_' . $this->moduleName, 3, array('moduleName' => $this->moduleName));
        $pagination->createPagination();
        $content ='';
        if ($result = $db->mysqli->query('SELECT * FROM `' . DB_PREFIX . '_' . $this->moduleName . '` ORDER BY `nom` DESC LIMIT ' . $pagination->start . ',' . $pagination->countOnPage)) {
            $content .= '
        <div id="' . $this->moduleName . '_div">
            <h2>Новости</h2>';
            $content.=' <ul>';
            while ($row = $result->fetch_assoc()) {
                $content .= '
                    <li class="cf">
                    <a href="/' . $this->moduleName . '.php?id=' . $row['id'] . '">';
                if (file_exists('./uploads/' . $this->moduleName . '/mini/' . $row['id'] . '.jpg')) {
                    $content .= '<div class="tumb"><img src="/uploads/' . $this->moduleName . '/mini/' . $row['id'] . '.jpg" alt="' . $row['tit'] . '"></div>';
                }
                $content .= '<div class="cont"><span>' . Save::date($row['date']) . '</span>
                <br>' . $row['tit'] . ' (' . $row['count'] . ' фото)</div></a>
                </li>';
            }
            $content .= '</ul>
                </div>';
                $content .= $pagination->show();
        }
        return $content;
    }

    public function oneItem($id = '')
    {
        $content = 'Новость не найдена';
        if ($id != '') {
            $db = DB::instance();
            $arrResult = $db->querySelect(DB_PREFIX . '_feed', '', '`id`=' . $id . ' LIMIT 1');
            $row = $arrResult->fetch_assoc();
            $content = '
            <div id="' . $this->moduleName . '_div">
            <h2>Новости</h2>
            <h1>' . $row['tit'] . '</h1>';
            if (file_exists('./uploads/feed/' . $row['id'] . '.jpg')) {
                $content .= '<img src="/uploads/feed/' . $row['id'] . '.jpg">';
            }
            $content .=  $row['text'] ;
            //вывод фотогалереи
            $content .= '<ul class="gal">';
            $pathMain = UPLOADS_PATH . '/' . $this->moduleName . '/' . $id . '/mini';
            if (is_dir($pathMain)) {
                $path = scandir($pathMain);
                foreach ($path as $k) {
                    if ($k != '.' AND $k != '..') {
                        $content .= '<li>
                 <a href="/uploads/' . $this->moduleName . '/' . $id . '/' . $k . '" class="highslide" onclick="return hs.expand(this)"><img src="/uploads/' . $this->moduleName . '/' . $id . '/mini/' . $k . '"></a></td></tr>
                
                 </li>';
                    }
                }
            }
            $content .= '</ul></div>';
        }
        return $content;
    }

    public static function widget()
    {

        $db = DB::instance();

        $content = '';
        if ($result = $db->mysqli->query('SELECT * FROM `' . DB_PREFIX . '_feed` ORDER BY `date` DESC LIMIT 3')) {
            $content .= '
        <div id="feed_widget">
            <h2>Новости</h2>
             <ul>';
            while ($row = $result->fetch_assoc()) {
                $content .= '<li class="cf"><a href="/feed.php?id=' . $row['id'] . '">';
                if (file_exists('./uploads/feed/mini/' . $row['id'] . '.jpg')) {
                    $content .= '<div class="tumb"><img src="/uploads/feed/mini/' . $row['id'] . '.jpg" alt="' . $row['tit'] . '" ></div>';
                }
                $content .= '<div class="cont"><span>' . Save::date($row['date']) . '</span><br>' . $row['tit'] . '</div></a>
                </li>
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
        $content = '<table border=0 class="%_CLASS_%"><tr><td width="100">';
        if (file_exists('../uploads/' . $folder . '/mini/' . $arrValues['id'] . '.jpg')) $content .= '<img src="/uploads/' . $folder . '/mini/' . $arrValues['id'] . '.jpg?' . time() . '">';
        $content .= '</td><td>%_TIT_%<br>%_DATE_%</td><td width="200"><a href="./' . $action . '?a=2&id=%_ID_%">редактировать</a><br><a href="./' . $action . '?a=3&id=%_ID_%" onclick="return confirm (\'Точно удалить запись?\');">удалить</a></td></tr></table>';
//----------------------------------------------------------------------------------------------------------------------
        foreach ($arrValues as $key => $value) {
            $arrFrom[] = '%_' . mb_strtoupper($key) . '_%';
            $arrTo[] = $value;
        }
        return str_replace($arrFrom, $arrTo, $content);
    }


    public static function install()
    {
        mkdir(UPLOADS_PATH . '/feed');
        mkdir(UPLOADS_PATH . '/feed/mini');
        mkdir(UPLOADS_PATH . '/feed/tmp');
        return "
        CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "_feed` (
            `id` int(10) unsigned NOT NULL auto_increment,
            `nom` int(11) NOT NULL default '0',
            `tit` text NOT NULL,
            `date` timestamp default CURRENT_TIMESTAMP,
            `key` text NOT NULL,
            `des` text NOT NULL,
            `text` text NOT NULL,
            `count` int(11) NOT NULL default '0',
          PRIMARY KEY (`id`)
        ) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;";
    }
}