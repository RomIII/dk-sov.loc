<?php
class Pagination
{
    private $page;//Номер текущей страницы.
    public $countOnPage;
    public $total;
    public $start;
    public $table;
    public $url;
    public $c = '';//контент

    /**
     * Pagination constructor.
     * @param $table
     * @param int $countOnPage
     * @param string $route - id раздела каталога если нужна пагинация не по всей таблицы а только конкретный каталог
     * @sql условие для получения числа total, если нужна выборка по таблице
     */
    public function __construct($table, $countOnPage = 10, $route = array(),$sql='')
    {
        if (isset($_GET['page'])) {
            $this->page = (int)$_GET['page'];//получать из гет параметра
        } else {
            $this->page = 1;
        }
        $this->table = $table;
        $this->countOnPage = $countOnPage;
        $this->start = ($this->page - 1) * $countOnPage;
        $db = DB::instance();
        if ($sql=='') {
            $r=$db->querySelect($this->table,array('id'),'1=1');
        } else {
            $r=$db->querySelect($this->table,array('id'),$sql);
        }
        $this->total = $r->num_rows;
        $this->url = './' . $route['moduleName'];
        if(!strpos($this->url,'.php'))$this->url.='.php';
    }

    /*
     * подготавливаем к выводу
     */
    public function createPagination()
    {
        $page = $this->page;
        $pages = ceil($this->total / $this->countOnPage);
        if ($this->total <= $this->countOnPage) return;

        //склеиваем все сгенерированные части навигатора
        $c = '
          <nav aria-label="Page navigation">
          <ul class="pagination cf">';

        if ($page > 3) {
           $c .= ' <li><a href="' . $this->url . '?page=1" >1</a></li>';
        if($page - 2!=2){$c .= '<li class="disabled"><span aria-hidden="true">...</span></li>';}
        }
        for ($i = ($page - 2); $i < ($page + 5); $i++) {
            if ($i > 0 && $i < $pages + 1) {
                if ($i == ($page)) {
                    $alink = '<li class="active"><span aria-hidden="true">' . $i . '</span></li> ';
                } else {
                    $alink = '<li><a href="' . $this->url . '?page=' . $i . '" >' . $i . '</a></li>';
                }
                $c .= $alink;
            }
        }
        if ($page < $pages - 4) {
            if ($i != $pages) {
                $c .= '<li class="disabled"><span aria-hidden="true">...</span></li>';
            }
            $c .= '<li><a href="' . $this->url . '?page=' . $pages . '" >' . $pages . '</a></li>';
        }

        $c .= '</ul>
        </nav>';
        // возвращаем полученный список страниц
        $this->c = $c;
    }

    /**
     * выводим блок с пагинацией
     * @return string
     */
    public function show()
    {
        return $this->c;
    }
}