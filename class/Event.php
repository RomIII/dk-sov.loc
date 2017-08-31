<?php

class Event
{
    public $content;
    public $moduleName = 'event';

    public function show()
    {
        return $this->content;
    }

    public function mainList()
    {
        $db = DB::instance();

        $content = '';
        $class = 'item1';
        if ($result = $db->mysqli->query('SELECT * FROM `' . DB_PREFIX . '_' . $this->moduleName . '` ORDER BY `date_start`')) {
            $content .= '
        <div id="' . $this->moduleName . '_div" class="cf">
            <h2>Афиша</h2>
             <ul>';
            while ($row = $result->fetch_assoc()) {
                $class == 'item1' ? $class = 'item2' : $class = 'item1';
                $row['date'] = Save::duration_date($row['date_start'], $row['date_finish']);
                $row['class'] = $class;
                $content .= '
                    <li class="'.$class.' cf">
                    <div class="cont">'.$row['date'].' (' . $row['type'] . ')
                    <h3>' . $row['tit'] . '</h3>' . $row['des'];
                if ($row['time'] != '') $content .= 'Начало:' . $row['time'];
                if ($row['cost'] != '') $content .= '<br>Цена: ' . $row['cost'];
                if ($row['text'] != '') $content .= '<br><a href="/' . $this->moduleName . '.php?id=' . $row['id'] . '">подробнее...</a><br>';             
                 $content.='   </div>
                    <div class="tumb">';
                    if (file_exists('./uploads/' . $this->moduleName . '/mini/' . $row['id'] . '.jpg')) {
                    $content .= '<a href=/uploads/' . $this->moduleName . '/' . $row['id'] . '.jpg class="highslide" onclick="return hs.expand(this)">
                        <img src="/uploads/' . $this->moduleName . '/mini/' . $row['id'] . '.jpg"></a>';
                }
                   $content.=' </div>
                    </li>';
            }
            $content .= '</ul>
                </div>';
        }
        return $content;
    }

    public function oneItem($id = '')
    {

        $content = 'событие не найдено';
        if ($id != '') {
            $db = DB::instance();
            $row = $db->queryRow(DB_PREFIX . '_' . $this->moduleName, '*', '`id`=' . $id);
            $content = Save::duration_date($row['date_start'], $row['date_finish']);
            $content .= '
            <div id="' . $this->moduleName . '_div" class="cf">
            <h2>' . $row['tit'] . ' (' . $row['type'] . ')</h2>' . $row['text'];

            if (file_exists('./uploads/' . $this->moduleName . '/' . $row['id'] . '.jpg')) {
                $content .= '<img src=/uploads/' . $this->moduleName . '/' . $row['id'] . '.jpg >';
            }

            if ($row['time'] != '') $content .= 'Начало:' . $row['time'];
            if ($row['cost'] != '') $content .= '<br>Цена: ' . $row['cost'];

        }
        $content.='</div>';
        return $content;
    }

    public function widget()
    {
        return '';
    }

    static function list_item_template($arrValues, $folder)
    {
        $content = '<div class="container">';
        if (file_exists('./uploads/' . $folder . '/mini/' . $arrValues['id'] . '.jpg')) $content .= '<img src="/uploads/' . $folder . '/mini/' . $arrValues['id'] . '.jpg?' . time() . '"  align="left" class="img-thumbnail img-right img-mini">';
        $content .= '<div class="text">%_DATE_%<br><a href="/event.php?id=' . $arrValues['id'] . '">%_TIT_%</a></div></div>
        <div class="container minimargin">
            <div class="row">
            <div class="col-md-12 ui-product-area"></div>
            </div>
        </div><div style="clear:both;"></div>';
//----------------------------------------------------------------------------------------------------------------------
        foreach ($arrValues as $key => $value) {
            $arrFrom[] = '%_' . mb_strtoupper($key) . '_%';
            $arrTo[] = $value;
        }
        return str_replace($arrFrom, $arrTo, $content);
    }

    static function admItem($arrValues, $action, $folder = '')
    {
//----------------------------------------------------------------------------------------------------------------------
//отредактировать внешний вид пункта списка, в паттернах название столбца значение которого нужно вставить
//----------------------------------------------------------------------------------------------------------------------
        $content = '<table border=0 class="%_CLASS_%">
<tr><td colspan="2" align="left">%_DATE_%</td></tr>
<tr><td width="100">';
        if (file_exists('../uploads/' . $folder . '/mini/' . $arrValues['id'] . '.jpg')) $content .= '<img src="/uploads/' . $folder . '/mini/' . $arrValues['id'] . '.jpg?' . time() . '" width=100px>';
        $content .= '</td><td>%_TIT_%<br></td><td width="200"><a href="./' . $action . '?a=2&id=%_ID_%">редактировать</a><br><a href="./' . $action . '?a=3&id=%_ID_%" onclick="return confirm (\'Точно удалить запись?\');">удалить</a></td></tr></table>';
//----------------------------------------------------------------------------------------------------------------------
        foreach ($arrValues as $key => $value) {
            $arrFrom[] = '%_' . mb_strtoupper($key) . '_%';
            $arrTo[] = $value;
        }
        return str_replace($arrFrom, $arrTo, $content);
    }


    public static function install()
    {
        mkdir(UPLOADS_PATH . '/event');
        mkdir(UPLOADS_PATH . '/event/mini');
        mkdir(UPLOADS_PATH . '/event/tmp');
        return "
        CREATE TABLE `" . DB_PREFIX . "_event` (
            `id` int(10) unsigned NOT NULL auto_increment,
            `nom` int(11) NOT NULL default '0',
            `date_start` timestamp default CURRENT_TIMESTAMP,
            `date_finish` timestamp,
            `tit` text NOT NULL,
            `type` text NOT NULL,
            `time` text,
            `cost` text,
            `des` text NOT NULL,
            `text` text NOT NULL,
          PRIMARY KEY (`id`)
        ) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;";
    }
}