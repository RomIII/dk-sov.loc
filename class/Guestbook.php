<?php

class Guestbook
{
    public $moduleName = 'guestbook';
    public $content;
    public $capchaKey = '';
    public $arrValue = array();

    function __construct()
    {
        if ($this->capchaKey == '') $this->capchaKey = CAPTCHA_KEY;
    }

    public function show()
    {
        return $this->content;
    }

    public function mainList()
    {
        $db = DB::instance();
        $content = '';
        $pagination = new Pagination(DB_PREFIX . '_' . $this->moduleName, 20, array('moduleName' => $this->moduleName));
        $pagination->createPagination();

        if ($result = $db->mysqli->query('SELECT * FROM `' . DB_PREFIX . '_' . $this->moduleName . '` ORDER BY `id` LIMIT ' . $pagination->start . ',' . $pagination->countOnPage)) {
            $content .= '<div id="' . $this->moduleName . '_div">
            <h2>Гостевая книга</h2>';
            $content .= $this->showForma();
            $content .= '<ul>';
            while ($row = $result->fetch_assoc()) {
                $content .= '<li class="cf">';

                $content .= '<span>' . Save::date($row['date']) . '</span>
                <br>' . $row['name'] . ' <br>' . $row['text'] . '
                </li>';
            }
            $content .= '</ul>';
            $content .= '</div>';
            $content .= $pagination->show();
        }
        return $content;
    }

    public function showForma()
    {
        $content = '
        <form action="./' . $this->moduleName . '.php" class="form cf" method="POST">
            <input type="text" placeholder="Имя" class="name" name="name"
            value="';
        if (array_key_exists('name', $this->arrValue)) $content .= $this->arrValue['name'];
        $content .= '">
            <input type="text" placeholder="Контактная информация" class="mail" name="mail" value="';
        if (array_key_exists('mail', $this->arrValue)) $content .= $this->arrValue['mail'];
        $content .= '">
            <textarea name="text" id="" cols="30" rows="10" placeholder="Сообщение" class="message">';
        if (array_key_exists('text', $this->arrValue)) $content .= $this->arrValue['text'];
        $content .= '</textarea>
        <div class="g-recaptcha" data-sitekey="' . $this->capchaKey . '"></div>
            <input type="submit" value="Отправить" class="submit">
        </form>';
        return $content;
    }

    public function oneItem($id = '')
    {
        return;
    }

    public static function widget()
    {
        return;
    }


    static function admItem($arrValues, $action, $folder = '')
    {
//----------------------------------------------------------------------------------------------------------------------
//отредактировать внешний вид пункта списка, в паттернах название столбца значение которого нужно вставить
//----------------------------------------------------------------------------------------------------------------------
        $content = '<table border=0 class="%_CLASS_%"><tr><td width="100">';
        $content .= '</td><td>%_NAME_% - %_DATE_%<br>%_TEXT_%</td><td width="200"><a href="./' . $action . '?a=2&id=%_ID_%">редактировать</a><br><a href="./' . $action . '?a=3&id=%_ID_%" onclick="return confirm (\'Точно удалить запись?\');">удалить</a></td></tr></table>';
//----------------------------------------------------------------------------------------------------------------------
        foreach ($arrValues as $key => $value) {
            $arrFrom[] = '%_' . mb_strtoupper($key) . '_%';
            $arrTo[] = $value;
        }
        return str_replace($arrFrom, $arrTo, $content);
    }


    public static function install()
    {
        return "
        CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "_guestbook` (
            `id` int(10) unsigned NOT NULL auto_increment,
            `nom` int(11) NOT NULL default '0',
            `date` timestamp default CURRENT_TIMESTAMP,
            `name` text NOT NULL,
            `mail` text NOT NULL,
            `text` text NOT NULL,
          PRIMARY KEY (`id`)
        ) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;";
    }
}