<?php

class Carousel
{
    public $moduleName = 'carousel';
    public $content = '';

    public function __construct()
    {
        $this->content .= '
            <div id="carousel-example-generic" class="carousel slide more" data-ride="carousel">
              <!-- Indicators -->
              <ol class="carousel-indicators">
                <li data-target="#carousel-example-generic" data-slide-to="0" class="active"></li>
                <li data-target="#carousel-example-generic" data-slide-to="1"></li>
                <li data-target="#carousel-example-generic" data-slide-to="2"></li>
              </ol>

              <!-- Wrapper for slides -->
              <div class="carousel-inner" role="listbox">';

        $db = DB::instance();
        $i = 0;
        if ($result = $db->mysqli->query('SELECT * FROM `' . DB_PREFIX . '_carousel` ORDER BY `nom` DESC')) {
            while ($row = $result->fetch_assoc()) {

                $this->content .= '<div class="item ';
                if ($i == 0) $this->content .= ' active';
                $this->content .= ' ">';
                if ($row['url'] != '') $this->content .= '<a href="' . $row['url'] . '">';
                $this->content .= '<img src="/uploads/carousel/' . $row['id'] . '.jpg" alt="' . $row['name'] . '" width="100%">';
                if ($row['url'] != '') $this->content .= '</a>';
                $this->content .= '
                 
                </div>';
                $i++;
            }
        }
        $this->content .= '
              </div>

              <!-- Controls -->
              <a class="left carousel-control" href="#carousel-example-generic" role="button" data-slide="prev">
                <span class="glyphicon glyphicon-chevron-left" aria-hidden="true"></span>
                <span class="sr-only">Previous</span>
              </a>
              <a class="right carousel-control" href="#carousel-example-generic" role="button" data-slide="next">
                <span class="glyphicon glyphicon-chevron-right" aria-hidden="true"></span>
                <span class="sr-only">Next</span>
              </a>
            </div>';
    }

    public static function show()
    {
        $content = '
            <div id="carousel-example-generic" class="carousel slide more mycarousel" data-ride="carousel">

              <div class="carousel-inner" role="listbox">';

        $db = DB::instance();
        $i = 0;
        if ($result = $db->mysqli->query('SELECT * FROM `' . DB_PREFIX . '_carousel` ORDER BY `nom`')) {
            while ($row = $result->fetch_assoc()) {

                $content .= '<div class="item ';
                if ($i == 0) $content .= ' active';
                $content .= ' ">';
                if ($row['url'] != '') $content .= '<a href="' . $row['url'] . '">';
                $content .= '<img src="/uploads/carousel/' . $row['id'] . '.jpg" alt="' . $row['name'] . '" width="100%">';
                if ($row['url'] != '') $content .= '</a>';
                $content .= '

                </div>';
                $i++;
            }
        }
        $content .= '
              </div>

              <!-- Controls -->
              <a class="left carousel-control" href="#carousel-example-generic" role="button" data-slide="prev">
                <span class="glyphicon glyphicon-chevron-left" aria-hidden="true"></span>
                <span class="sr-only">Previous</span>
              </a>
              <a class="right carousel-control" href="#carousel-example-generic" role="button" data-slide="next">
                <span class="glyphicon glyphicon-chevron-right" aria-hidden="true"></span>
                <span class="sr-only">Next</span>
              </a>
            </div>';
        return $content;
    }

    static function admItem($arrValues, $action, $folder = '')
    {
//----------------------------------------------------------------------------------------------------------------------
//отредактировать внешний вид пункта списка, в паттернах название столбца значение которого нужно вставить
//----------------------------------------------------------------------------------------------------------------------
        $content = '
            <li id="arrayorder_%_ID_%">
            <table border=0 class="%_CLASS_%"><tr><td width="100">';
        if (file_exists('../uploads/' . $folder . '/mini/' . $arrValues['id'] . '.jpg')) $content .= '<img src="/uploads/' . $folder . '/mini/' . $arrValues['id'] . '.jpg?' . time() . '">';
        $content .= '</td><td>%_NAME_%</td><td width="200"><a href="./' . $action . '?a=2&id=%_ID_%">редактировать</a><br><a href="./' . $action . '?a=3&id=%_ID_%">удалить</a></td></tr></table>
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
        mkdir(UPLOADS_PATH . '/carousel');
        mkdir(UPLOADS_PATH . '/carousel/mini');
        mkdir(UPLOADS_PATH . '/carousel/tmp');
        return "
        CREATE TABLE `" . DB_PREFIX . "_carousel` (
            `id` int(10) unsigned NOT NULL auto_increment,
            `nom` int(11) NOT NULL default '0',
            `name` text NOT NULL,
            `url` text NOT NULL,
          PRIMARY KEY (`id`)
        ) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;";
    }

}