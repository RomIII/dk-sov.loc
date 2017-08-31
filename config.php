<?php
ini_set('error_reporting', 0);
ini_set('display_errors', 0);
/*
ini_set("display_errors", "1");
ini_set("display_startup_errors", "1");
ini_set('error_reporting', E_ALL);
*/
define('DB_PREFIX', 'sov');
define('SITE_URL', 'dk-sov.loc');
define('ADMIN_MAIL','1633131@mail.ru');

define('UPLOADS_PATH', __DIR__ . '/uploads');

//https://www.google.com/recaptcha/admin#list
define('CAPTCHA_KEY','6LfkryIUAAAAACL9RxtRZ-_zWXdEQ6VvjmZZrmyz');
define('CAPTCHA_SECRET_KEY','6LfkryIUAAAAANi3kAQA3p-pYp_DBDZXoafA3BiK');

$url_first_mod = 'main_menu.php'; // страница на которую переходить после авторизации

$up_menu = array(
    'страницы в меню' => 'main_menu.php',
    'простые страницы' => 'free_page.php',
    //'новости'=>'news.php',
    'фотогалерея' => 'gallerylevel.php',
    'афиша' => 'event.php',
    'лента' => 'feed.php',
    'гостевая' => 'guestbook.php',
    'баннеры' => 'banner.php',
    'карусель'=>'carousel.php',
    //'фотки'=>'gallery.php'
);//ссылки на модули в верхнем меню
//ядро
include_once(__DIR__ . '/class/FormBilder.php');
include_once(__DIR__ . '/class/DB.php');
include_once(__DIR__ . '/class/TemplatesAdm.php');
include_once(__DIR__ . '/class/TemplatesSite.php');
include_once(__DIR__ . '/class/Save.php');
include_once(__DIR__ . '/class/Upload.php');
include_once(__DIR__ . '/class/Pagination.php');

//модули
//include_once(__DIR__.'/class/News.php');
include_once(__DIR__.'/class/Carousel.php');
//include_once(__DIR__.'/class/Gallery.php');
include_once(__DIR__ . '/class/Gallerylevel.php');
include_once(__DIR__ . '/class/Event.php');
include_once(__DIR__ . '/class/Feed.php');
include_once(__DIR__.'/class/Guestbook.php');
include_once(__DIR__.'/class/Mail.php');
include_once(__DIR__.'/class/Banner.php');
include_once(__DIR__.'/class/Search.php');

$db = DB::instance();
?>