<?php
session_start();
include_once('../config.php');
if ($_SESSION['kod'] !== md5(md5(DB_PREFIX))) exit ( "Ошибка регистрации" );

$action = 'free_page.php'; // название этого файла
$table_name = DB_PREFIX.'_free_pages'; // название таблицы с которой работаем

//text,textarea,ckeditor,hidden
$formItems = array(
    array('title' => 'Название в списке', 'type' => 'text', 'name' => 'name', 'value' => '', 'save' => 'text'),
    array('title' => 'Заголовок страницы', 'type' => 'text', 'name' => 'tit', 'value' => '', 'save' => 'text'),
    array('title' => 'Ключевые слова', 'type' => 'text', 'name' => 'key', 'value' => '', 'save' => 'text'),
    array('title' => 'Описание', 'type' => 'text', 'name' => 'des', 'value' => '', 'save' => 'text'),
    array('title' => 'Cодержимое страницы', 'type' => 'ckeditor', 'name' => 'text', 'value' => '', 'save' => 'text_html')
);

class ListItem {
    static function admItem($arrValues, $action)
    {
//----------------------------------------------------------------------------------------------------------------------
//отредактировать внешний вид пункта списка, в паттернах название столбца значение которого нужно вставить
//----------------------------------------------------------------------------------------------------------------------
        $content = '<table border=0 class="%_CLASS_%"><tr><td>%_TIT_%<br>http://'.SITE_URL.'/index.php?a=1&id=%_ID_%</td><td width="200"><a href="./'.$action.'?a=2&id=%_ID_%">редактировать</a><br><a href="./'.$action.'?a=3&id=%_ID_%" onclick="return confirm (\'Точно удалить запись?\');">удалить</a></td></tr></table>';
//----------------------------------------------------------------------------------------------------------------------
        foreach ($arrValues as $key => $value) {
            $arrFrom[] = '%_' . mb_strtoupper($key) . '_%';
            $arrTo[] = $value;
        }
        return str_replace($arrFrom, $arrTo, $content);
    }
}
$content='';
$arrColumns = array('id', 'nom');
foreach ($formItems as $arr) {
    $arrColumns[] = $arr['name'];
}

if (isset($_GET['a'])) {
    $a = Save::number($_GET['a']);
} else {
    $a = '';
}

//форма добавления
if ($a==1) {
    $form = new FormBilder($action);
    $form->buildForm($formItems);
    $content = $form->drawForm();
}
//добавление/обноление данных в таблице
if ($_POST) {
    $countPost = 0;
    $arrSavedPost = array();
    foreach ($_POST as $key => $value) {
        $key = Save::text($key);
        if ($key == 'id') {
            $value = Save::number($value);
        } else {
            $method=$formItems[$countPost]['save'];
            $value = Save::$method($value);
        }
        $arrSavedPost[$key] = $value;
        $countPost++;
    }
    if (isset($arrSavedPost['id']) && $arrSavedPost['id'] != '') {
        $db->queryUpdate($table_name, $arrSavedPost, '`id`=' . $arrSavedPost['id']);
        $content = 'Изменения сохранены';
    } else {
      $maxnom = $db->simple_query("SELECT MAX(`nom`) FROM `" . $table_name . "`");
        if ($maxnom) {
            $m = $maxnom->fetch_row();
            $max = $m[0] + 1;
        } else {
            $max = 1;
        }
        $arrSavedPost['nom']=$max;
        $db->queryInsert($table_name, $arrSavedPost);
        $content = 'Данные добавлены';
    }
}
// форма для редактирования
if ($a == 2) {
    $id = Save::number($_GET['id']);
    $arrRes = $db->querySelect($table_name, $arrColumns, '`id`=' . $id . ' LIMIT 1')->fetch_assoc();
    $countArr = count($formItems);
    $i = 0;
    while ($i < $countArr) {
        $formItems[$i]['value'] = $arrRes[$formItems[$i]['name']];
        $i++;
    }
    $formItems[] = array('title' => '', 'type' => 'hidden', 'name' => 'id', 'value' => $id);
    $form = new FormBilder($action);
    $form->buildForm($formItems);
    $content = $form->drawForm();
}
//удаление
if($a==3){
    $id =  Save::number($_GET['id']);
    $arrRes = $db->simple_query("DELETE FROM `".$table_name."` WHERE `id`='".$id."' LIMIT 1");
    header('location: ' . $action);
}
//вывод списка
if($a=='') {
    $class='item2';
    if ($result = $db->mysqli->query('SELECT * FROM `' . $table_name . '` ORDER BY `nom` DESC')) {
        while( $row = $result->fetch_assoc() ){
            $class=='item1'? $class='item2':$class='item1';
            $row['class']=$class;
            $content.= ListItem::admItem($row, $action);
        }
    }
}
//после аджах запроса меняем порядок итемов
if (isset($_POST['arrayorder'])) {
    $array = $_POST ['arrayorder'];
    $count = 1;
    foreach ($array as $idval) {
        $idval = Save::number($idval);
        $query = "UPDATE `" . DB_PREFIX . "_main_menu` SET `nom` = '" . $count . "' WHERE `id` = " . $idval;
        $db->simple_query($query);
        $count++;
    }
}


$templater = array(
    '%_TITLE_%' => 'Администрирование : свободные страницы',
    '%_JS_%' => TemplatesAdm::js_template(),
    '%_LOGO_%' => '',
    '%_SITE_%' => SITE_URL,
    '%_EXIT_%' =>'<div id="exit"><a href="index.php?act=0">Выход</a></div>',
    '%_UP_MENU_%' => TemplatesAdm::up_menu_template($up_menu),
    '%_LEFT_MENU_%'=>'<a href=./'.$action.'?a=1>Добавить</a>',
    '%_CONTENT_%' => $content,
);
TemplatesAdm::main_template($templater);
?>