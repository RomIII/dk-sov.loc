<?php

class TemplatesAdm
{
    static function admItem($arrValues, $action)
    {
//----------------------------------------------------------------------------------------------------------------------
//отредактировать внешний вид пункта списка, в паттернах название столбца значение которого нужно вставить
//----------------------------------------------------------------------------------------------------------------------
        $content = '
       <li id="arrayorder_%_ID_%" >
       <table border=0 class="%_CLASS_%"><tr><td>%_LINK_%</td>
       <td width="200px"><a href="./' . $action . '?a=2&amp;id=%_ID_%">редактировать</a><br><a href="./' . $action . '?a=3&amp;id=%_ID_%" onclick="return confirm (\'Точно удалить запись?\');">удалить</a></td>
       </tr></table></li>';
//----------------------------------------------------------------------------------------------------------------------
        foreach ($arrValues as $key => $value) {
            $arrFrom[] = '%_' . mb_strtoupper($key) . '_%';
            $arrTo[] = $value;
        }
        return str_replace($arrFrom, $arrTo, $content);
    }

    static function main_template($arrValues)
    {
        $content = '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
        <html>
        <head>
            <title>%_TITLE_%</title>
            <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
            <link href="./images/style.css" rel="stylesheet" type="text/css">
            <link href="./images/jquery-ui.css" rel="stylesheet" type="text/css">
            <link rel="stylesheet" type="text/css"
                  href="http://fonts.googleapis.com/css?family=Roboto+Slab:400,700|PT+Sans:400,700,400italic&amp;subset=latin,cyrillic-ext,cyrillic">
            %_JS_%
        </head>
        <body>
        <div id="header_layout">
            <div id="header">
                <div id="logo">%_LOGO_%</div>
                <div id="up_text">Администрирование сайта %_SITE_%</div>
                %_EXIT_%
                <ul>
                    %_UP_MENU_%
                </ul>
            </div>
        </div>
        <div id="content">
        <div id="left">%_LEFT_MENU_%</div>
        <div id="right">%_CONTENT_%</div>
        </div>
        <div id="footer_layout">
            <div id="footer"></div>
        </div>
        </body>
        </html>';

        echo TemplatesAdm::dress_template($arrValues, $content);
    }

    static function up_menu_template($arrValues = '')
    {
        if (!is_array($arrValues) or $arrValues == '') return;
        $content = '<ul>';
        foreach ($arrValues as $key => $value) {
            $content .= '<li><a href="' . $value . '">' . $key . '</a></li>';
        }
        $content .= '</ul>';
        return $content;
    }

    static function js_template()
    {
        $content = '<script type="text/javascript" src="/js/jquery.js"></script>
                    <script type="text/javascript" src="/js/jquery-ui.js"></script>
                    ';
        return $content;
    }

    static function forma()
    {
        $content = '
        <div style="width: 300px; height: 280px; margin: 100px;">
            <form action="./index.php" method="post">
                <div>Логин</div>
                <div><input type=\'text\' style="width:100%;" name="login" id="login" tabindex="1"></div>
                <div>Пароль</div>
                <div><input type="password" style="width:100%;" name="pass" id="pass" tabindex="2"></div>
                <div>Проверочный код</div>
                <div><img src="../captcha/kod.php" border="0" alt="" style="float: left;">
                    <input type="text" style="float: right; margin-top: 12px;width:50%;" name="keystring" tabindex="3"></div>
                <div style="clear:both;"></div>
                <div align="center" style="margin-top: 20px;clear:both;">
                    <input type="submit" value="Войти"  tabindex="4">
                    <input type="button" value="Отмена" tabindex="5" onclick="window.location.href=\'/\';">
                </div>
            </form>
        </div>';
        return $content;
    }

    static function dress_template($arrValues, $template)
    {
        $keys = array('%_', '_%');
        foreach ($arrValues as $key => $value) {
            $key = str_replace($keys, '', $key);
            $arrFrom[] = '%_' . mb_strtoupper($key) . '_%';
            if ($value != '') {
                $arrTo[] = $value;
            } else {
                $arrTo[] = '';
            }
        }
        $content = str_replace($arrFrom, $arrTo, $template);

//----------------МОДУЛЬ ДЛЯ ПЛОХОВИДЯЩИХ-------------------------------------------------------------------------------
if(!array_key_exists('%_EXIT_%',$arrValues)){
        $urlEyes='http://'.$_SERVER['HTTP_HOST'].$_SERVER['PHP_SELF'].'?'.$_SERVER['QUERY_STRING'];
        if($_SERVER['QUERY_STRING']!=''){
            $urlEyes.='&b=1';
        }else{
            $urlEyes.='b=1';
        }
        if(isset($_GET['b'])&&(int)$_GET['b']==1){
            $urlEyes='http://'.$_SERVER['HTTP_HOST'].$_SERVER['PHP_SELF'].'?'.str_replace('b=1','',$_SERVER['QUERY_STRING']);
        }
        if(substr($urlEyes,-1)=='?')$urlEyes=substr($urlEyes, 0, -1);
        if(substr($urlEyes,-1)=='&')$urlEyes=substr($urlEyes, 0, -1);
        $old['body']='</body>';
        $new['body'] =('<div id="fvb" itemprop="Copy" style="z-index:99;background:#fff;position:fixed;right:0;top:0;">
        <a href="' . $urlEyes . '" rel="nofollow">
            <img src="./images/128.png" width="128" height="64">
        </a>
        </div></body>');
        $new['css']='<link href="./css/style.css" rel="stylesheet" type="text/css">';

        if (isset($_GET['b']) && (int)$_GET['b'] == 1) {
            preg_match_all("/<[Aa][\s]{1}[^>]*[Hh][Rr][Ee][Ff][^=]*=[ '\"\s]*([^ \"'>\s#]+)[^>]*>/", $content, $matches);
            $urls = $matches[1]; // Берём то место, где сама ссылка (благодаря группирующим скобкам в регулярном выражении)
            $newUrls = [];
            for ($i = 0; $i < count($urls); $i++) {
                if (strpos($urls[$i], '?') === false) {
                    $newUrls[$i] = $urls[$i] . '?b=1';
                } else {
                    $newUrls[$i] = $urls[$i] . '&b=1';
                }
                //echo "<br>" . $urls[$i] . " --- " . $newUrls[$i];
            }
            $content = str_replace($urls, $newUrls, $content);
			unset($urls);
			unset($newUrls);
            $new['css']='<link href="./css/style_eyes.css" rel="stylesheet" type="text/css">';
        }

        $old['css'] = '<link href="./css/style.css" rel="stylesheet" type="text/css">';
		//странный костыль конкретно для этого сервака
		$old['kostil']='&b=1&b=1';
		$new['kostil']='&b=1';
		//--------------------------------------------

        $content = str_replace($old, $new, $content);
		}
//----------------------------------------------------------------------------------------------------------------------
        return $content;
    }
}

?>