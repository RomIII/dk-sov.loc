<?php

class Search
{
    public $moduleName = 'search';
    public $content;
    public $capchaKey = '';
    //массив с полями для поиска
    public $searchArea = array(
        'event' => 'Афиша',
        'free_page' => 'Страница',
        'main_menu' => 'Страница',
        'feed' => 'Новость',
    );

    public static function showForma()
    {
        $content = '
        <div id="search">
        <div class="d1">
            <form action="/search.php" method="post">
            <input type="text" placeholder="Поиск" name="search">
            <button type="submit"></button>
            </form>
        </div>
        </div>';
        return $content;
    }

    public function showResult($val)
    {
        $val = Save::textSearch($val);
        $db = DB::instance();
        $content = '<h1>Результат поиска</h1><ul>';
        foreach ($this->searchArea as $k => $v) {
            $sql = "SELECT `id`,`tit` FROM `".DB_PREFIX."_" . $k . "` WHERE `text` LIKE '%".$val."%' ORDER BY `id` DESC LIMIT 30";
            $result = $db->mysqli->query($sql);
            if ($result) {
                while ($row = $result->fetch_assoc()) {
                    $url = $k.'.php?id='.$row['id'];
                    if($k=='main_menu')$url='index.php?a=0&id='.$row['id'];
                    if($k=='free_page')$url='index.php?a=1&id='.$row['id'];
                    $content .= '<li><a href="./'.$url.'">'.$v .' : ' . $row['tit'].'</a></li>';
                }
            }
        }
        $content.='</ul>';
        return $content;
    }

}