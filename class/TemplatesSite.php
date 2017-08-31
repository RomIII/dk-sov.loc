<?php

class TemplatesSite extends TemplatesAdm
{
    static function main_template($arrValues)
    {
		$content = '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
        <html>
        <head>
            <title>%_TIT_%</title>
            <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
	        <meta name=description content="" >
            <meta name=keywords content="" >
            <meta name="REVISIT-AFTER" content="5 days">
            <meta name="robots" content="all">
            <link href="./css/style.css" rel="stylesheet" type="text/css">
            <link href="./css/bootstrap.min.css" rel="stylesheet" type="text/css">
            <link rel="stylesheet" type="text/css" href="./css/bootstrap-social.css" />
            <link rel="stylesheet" href="./css/font-awesome.min.css">
            <link rel="stylesheet" type="text/css" href="http://fonts.googleapis.com/css?family=Roboto+Slab:400,700|PT+Sans:400,700,400italic&amp;subset=latin,cyrillic-ext,cyrillic">
            %_JS_%
        </head>
        <body>
        <div id="header_layout">
            <div id="header">
            '.Carousel::show().'
            <div id="tel">
            <b>МБУДК "Современник"</b><br>
            <b>тел:</b> 8-496-227-83-22, 8-495-993-93-64. <b>mail:</b> sovremennik16@yandex.ru
            '.Search::showForma().'
            </div>

            </div>
        </div>
        <div id="content">
        <div id="left">%_LEFT_MENU_%

        <br>
        %_NEWS_%
        </div>
        <div id="right">%_CONTENT_%</div>
        </div>
        <div id="footer_layout">
            <div id="footer">
            <div id="adr">
            <p>МО, город Дмитров, улица Большевистская, дом 16.<br>
            Тел: 8-496-227-83-22,8-495- 993-93-64<br>
            sovremennik16@yandex.ru</p>
            </div>
            <div id="soc">

                <a class="btn btn btn-social-icon btn-vk" href="https://vk.com/club127186035">
                    <span class="fa fa-vk"></span>
                </a>

        <a class="btn btn-social-icon btn-facebook">
        <span class="fa fa-facebook"></span>
        </a>

                <a class="btn btn-social-icon btn-instagram" href="https://www.instagram.com/sovremennik_dk">
                    <span class="fa fa-instagram"></span>
                </a>


            </div>
            <div id="hotlog">
            <!-- HotLog -->
<span id="hotlog_counter"></span>
<span id="hotlog_dyn"></span>
<script type="text/javascript"> var hot_s = document.createElement(\'script\');
hot_s.type = \'text/javascript\'; hot_s.async = true;
hot_s.src = \'http://js.hotlog.ru/dcounter/2552819.js\';
hot_d = document.getElementById(\'hotlog_dyn\');
hot_d.appendChild(hot_s);
</script>
<noscript>
<a href="http://click.hotlog.ru/?2552819" target="_blank">
<img src="http://hit20.hotlog.ru/cgi-bin/hotlog/count?s=2552819&im=663" border="0"
title="HotLog" alt="HotLog"></a>
</noscript>
<!-- /HotLog -->
            </div>
            </div>
        </div>
        </body>
        </html>';

        echo TemplatesAdm::dress_template($arrValues, $content);
    }

    static function js_template()
    {
        $content = '
        <script type="text/javascript" src="/js/jquery.js"></script>
        <script type="text/javascript" src="/js/bootstrap.min.js"></script>';
        return $content;
    }

    static function showMenu(){
        $arrMenu = array();
        $db=DB::instance();
        if ($result = $db->mysqli->query('SELECT `link`,`id` FROM `' . DB_PREFIX . '_main_menu` ORDER BY `nom`')) {
            while ($row = $result->fetch_assoc()) {
                $arrMenu[$row['link']] = 'http://' . SITE_URL . '?a=0&amp;id=' . $row['id'];
                //$arrMenu[]=($row['link'] => 'http://'.SITE_URL.'?a=0&id='.$row['id']);
            }
        }
        if (!is_array($arrMenu) or $arrMenu == '') return;
        $content = '<ul>';
        $content .= '<li><a href="http://'.SITE_URL.'/event.php"> Афиша </a></li>';
        $content .= '<li><a href="http://'.SITE_URL.'/gallerylevel.php"> Фотогалерея </a></li>';
        $content .= '<li><a href="http://'.SITE_URL.'/feed.php"> Новости </a></li>';
        $content .= '<li><a href="http://'.SITE_URL.'/guestbook.php"> Гостевая книга </a></li>';
        foreach ($arrMenu as $key => $value) {
            $content .= '<li><a href="' . $value . '">' . $key . '</a></li>';
        }
        $content .= '</ul>';
        return $content;
    }

}