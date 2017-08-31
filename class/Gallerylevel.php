<?php

class Gallerylevel
{
    public $content;
    public $moduleName = 'gallerylevel';

    public function show()
    {
        return $this->content;
    }


    public function mainList()
    {
        $db = DB::instance();

        $content = '';
        if ($result = $db->mysqli->query('SELECT * FROM `' . DB_PREFIX . '_' . $this->moduleName . '` ORDER BY `nom` DESC')) {
            $content .= '
        <div id="' . $this->moduleName . '_div">
            <h2>Фотогалерея</h2>
             <ul>';
            while ($row = $result->fetch_assoc()) {
                $content .= '
                    <li><a href="/gallerylevel.php?id=' . $row['id'] . '">' . $row['tit'] . ' (' . $row['count'] . ' фото)</a></li>';

            }
            $content .= '</ul>
                </div>';
        }
        return $content;
    }

    public function oneItem($id = '')
    {
        $content = 'галерея не найдена';
        if ($id != '') {
            $db = DB::instance();
            $name=$db->queryRow(DB_PREFIX . '_' . $this->moduleName, array('tit'), '`id`='.$id);
            $content ='
            <div id="' . $this->moduleName . '_div">
            <h2>Фотогалерея - '.$name['tit'].'</h2>';
            $content .= '<ul class=gal>';
            $pathMain = UPLOADS_PATH . '/' . $this->moduleName . '/' . $id . '/mini';
            if (is_dir($pathMain)) {
                $path = scandir($pathMain);
                foreach ($path as $k) {
                    if ($k != '.' AND $k != '..') {
                        $content .= '<li>
                <a href="/uploads/' . $this->moduleName . '/' . $id . '/' . $k . '" class="highslide" onclick="return hs.expand(this)"><img src="/uploads/' . $this->moduleName . '/' . $id . '/mini/' . $k . '"></a>
                 </li>';
                    }
                }
            }
            $content .= '</ul></div>';
        }
        return $content;
    }

    public function widget()
    {

        $db = DB::instance();

        $content = '';
        if ($result = $db->mysqli->query('SELECT * FROM `' . DB_PREFIX . '_' . $this->moduleName . '` ORDER BY `nom` LIMIT 3')) {
            $content .= '
        <div id="' . $this->moduleName . '_div">
            <a href="/' . $this->moduleName . '.php">Галерея</a>
             <ul>';
            while ($row = $result->fetch_assoc()) {
                if (file_exists(UPLOADS_PATH . '/' . $this->moduleName . '/mini/' . $row['id'] . '.jpg')) {
                    $content .= '
                    <li><img src="/uploads/' . $this->moduleName . '/mini/' . $row['id'] . '.jpg" alt="' . $row['tit'] . '" class="img-thumbnail img-list" width="50"></li>
                ';
                }
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
        <li id="arrayorder_%_ID_%">
        <table border=0 class="%_CLASS_%"><tr><td>%_TIT_% (%_COUNT_% фото)</td><td width="200">
        <a href="./gallerylevelphoto.php?id=%_ID_%">Добавить фото</a><br>
        <a href="./' . $action . '?a=2&id=%_ID_%">редактировать</a><br>
        <a href="./' . $action . '?a=3&id=%_ID_%" onclick="return confirm (\'Точно удалить запись?\');">удалить галерею</a>
       </td></tr></table>
        </li>';
//----------------------------------------------------------------------------------------------------------------------

        foreach ($arrValues as $key => $value) {
            $arrFrom[] = '%_' . mb_strtoupper($key) . '_%';
            $arrTo[] = $value;
        }
        return str_replace($arrFrom, $arrTo, $content);
    }

    public static function install()
    {
        $moduleName = 'gallerylevel';
        mkdir(UPLOADS_PATH . '/' . $moduleName);
        mkdir(UPLOADS_PATH . '/' . $moduleName . '/mini');
        mkdir(UPLOADS_PATH . '/' . $moduleName . '/tmp');
        return array(
            "CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "_" . $moduleName . "` (
            `id` int(10) unsigned NOT NULL auto_increment,
            `nom` int(11) NOT NULL default '0',
            `tit` text NOT NULL,
			`count` int(11) NOT NULL default '0',
          PRIMARY KEY (`id`)
        ) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;",

//            "CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "_".$moduleName."_photo` (
//            `id` int(10) unsigned NOT NULL auto_increment,
//			`gallery_id` INT,
//            `nom` int(11) NOT NULL default '0',
//            `tit` text NOT NULL,
//          PRIMARY KEY (`id`)
//        ) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;",

        );
    }
}