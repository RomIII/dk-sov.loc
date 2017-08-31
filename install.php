<?php
//======================== Установка сайта ============================================================
// Настроить файл /class/config.php
// Запустить файл /install.php
// Удалить файл /install.php
//====================================================================================================
include_once(__DIR__ . '/config.php');

$query_array = array(
    //создаем таблицу авторизации и заполняем ее префиксами
    "CREATE TABLE IF NOT EXISTS  `" . DB_PREFIX . "_auth` (
	`user` text NOT NULL default '',
	`pass` text NOT NULL default ''
	) ENGINE=InnoDB DEFAULT CHARSET=utf8;",

    "INSERT INTO `" . DB_PREFIX . "_auth` (`user`, `pass`) VALUES ('" . DB_PREFIX . "', '" . DB_PREFIX . "');",
    //таблица основного меню
    "CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "_main_menu` (
	`id` int(10) unsigned NOT NULL auto_increment,
	`nom` int(11) NOT NULL default '0',
	`link` text NOT NULL,
	`tit` text NOT NULL,
	`key` text NOT NULL,
	`des` text NOT NULL,
	`text` text NOT NULL,
	PRIMARY KEY  (`id`)) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;",
    //заполняем основное меню стандартными разделами
    "INSERT INTO `" . DB_PREFIX . "_main_menu` (`nom`, `link`, `tit`, `key`, `des`, `text`) VALUES ('1', 'О нас', 'О нас', 'О нас', 'О нас', '<h1>О нас</h1>текст');",
    "INSERT INTO `" . DB_PREFIX . "_main_menu` (`nom`, `link`, `tit`, `key`, `des`, `text`) VALUES ('2', 'Услуги', 'Услуги', 'Услуги', 'Услуги', '<h1>Услуги</h1>текст');",
    "INSERT INTO `" . DB_PREFIX . "_main_menu` (`nom`, `link`, `tit`, `key`, `des`, `text`) VALUES ('3', 'Контакт', 'Контакт', 'Контакт', 'Контакт', '<h1>Контакт</h1>текст');",
    //создаем таблицу для простых страниц
    "CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "_free_pages` (
	`id` int(10) unsigned NOT NULL auto_increment,
	`nom` int(11) NOT NULL default '0',
	`name` text NOT NULL,
	`tit` text NOT NULL,
	`key` text NOT NULL,
	`des` text NOT NULL,
	`text` text NOT NULL,
	PRIMARY KEY  (`id`)) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;"
);

//добавляем запросы для установки таблиц модулей
foreach($up_menu as $key => $val){
    $val=ucfirst($val);
    $val=str_replace('.php','',$val);
    if(class_exists($val)) {
        $tmp=$val::install();
        if(is_array($tmp)){
            foreach ($tmp as $t) $query_array[] = $t;
        }else {
            $query_array[] = $tmp;
        }
    }
}
//выполняем запросы в базу
foreach ($query_array as $query) {
    echo $query . '<hr>';
    $db->simple_query($query);
}

?>