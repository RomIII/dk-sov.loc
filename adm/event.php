<?php
session_start();
include_once('../config.php');
if ($_SESSION['kod'] !== md5(md5(DB_PREFIX))) exit ("Ошибка регистрации");

$action = 'event.php'; // название этого файла
$table_name = DB_PREFIX . '_event'; // название таблицы с которой работаем
$folder = 'event'; //название папки куда грузить картинки, должно совпадать с названием класса модели

$arr_type = array(
    array("id" => "событие", "name" => "событие"),
    array("id" => "выставка", "name" => "выставка"),
    array("id" => "концерт", "name" => "концерт"),
    array("id" => "театр", "name" => "театр"),
    array("id" => "шоу", "name" => "шоу"),
);

//text,textarea,spaweditor,hidden,date
$formItems = array(
    array('title' => 'дата начала', 'type' => 'date', 'name' => 'date_start', 'value' => '', 'save' => 'text'),
    array('title' => 'дата окончания (не обязательно)', 'type' => 'date', 'name' => 'date_finish', 'value' => '', 'save' => 'text'),
    array('title' => 'Тип', 'type' => 'select', 'name' => 'type', 'value' => '', 'save' => 'text', 'selectItems' => $arr_type),
    array('title' => 'Название', 'type' => 'text', 'name' => 'tit', 'value' => '', 'save' => 'text'),
    array('title' => 'Описание (в общем списке)', 'type' => 'textarea', 'name' => 'des', 'value' => '', 'save' => 'text'),
    array('title' => 'Полный текст (для отдельной страницы)', 'type' => 'ckeditor', 'name' => 'text', 'value' => '', 'save' => 'text_html'),
    array('title' => 'Цена', 'type' => 'text', 'name' => 'cost', 'value' => '', 'save' => 'text'),
);
$js = '<script type="text/javascript">
                    $(document).ready(function(){
                       $.datepicker.regional[\'ru\'] = {
                            closeText: \'Закрыть\',
                            prevText: \'&#x3c;Пред\',
                            nextText: \'След&#x3e;\',
                            currentText: \'Сегодня\',
                            monthNames: [\'Январь\',\'Февраль\',\'Март\',\'Апрель\',\'Май\',\'Июнь\',
                            \'Июль\',\'Август\',\'Сентябрь\',\'Октябрь\',\'Ноябрь\',\'Декабрь\'],
                            monthNamesShort: [\'Янв\',\'Фев\',\'Мар\',\'Апр\',\'Май\',\'Июн\',
                            \'Июл\',\'Авг\',\'Сен\',\'Окт\',\'Ноя\',\'Дек\'],
                            dayNames: [\'воскресенье\',\'понедельник\',\'вторник\',\'среда\',\'четверг\',\'пятница\',\'суббота\'],
                            dayNamesShort: [\'вск\',\'пнд\',\'втр\',\'срд\',\'чтв\',\'птн\',\'сбт\'],
                            dayNamesMin: [\'Вс\',\'Пн\',\'Вт\',\'Ср\',\'Чт\',\'Пт\',\'Сб\'],
                            dateFormat: \'yy-mm-dd\',
                            firstDay: 1,
                            isRTL: false
                        };
                        $.datepicker.setDefaults($.datepicker.regional[ "ru" ] );
                        $(".datepicker").datepicker();
                    });
                    </script>';

$content = '';
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
if ($a == 1) {
    $form = new FormBilder($action, $withFile = true);
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
            $t = (string)$formItems[$countPost]['save'];
            $value = Save::$t($value);
        }
        $arrSavedPost[$key] = $value;
        $countPost++;
    }
    if (isset($arrSavedPost['id']) && $arrSavedPost['id'] != '') {
        $db->queryUpdate($table_name, $arrSavedPost, '`id`=' . $arrSavedPost['id']);
        $content = 'Изменения сохранены';
        if ($_FILES) {
            $upload = new Upload();
            $upload->setPath(UPLOADS_PATH . '/' . $folder);
            $upload->setMiniSize(array(200, 200));
            $upload->setNormalSize(array(600, 2000));
            $upload->uploadFile($_FILES, $arrSavedPost['id'], true);
        }
    } else {
        $maxnom = $db->simple_query("SELECT MAX(`nom`) FROM `" . $table_name . "`");
        if ($maxnom) {
            $m = $maxnom->fetch_row();
            $max = $m[0] + 1;
        } else {
            $max = 1;
        }
        $arrSavedPost['nom'] = $max;
        $db->queryInsert($table_name, $arrSavedPost);
        $content = 'Данные добавлены';

        if ($_FILES) {
            $upload = new Upload();
            $upload->setPath(UPLOADS_PATH . '/' . $folder);
            $upload->setMiniSize(array(200, 200));
            $upload->setNormalSize(array(600, 2000));
            $upload->uploadFile($_FILES, $db->lastId, true);
        }
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
    $form = new FormBilder($action, $withFile = true, $class = $folder, $id = $id);
    $form->buildForm($formItems);
    $content = $form->drawForm();
}
//удаление
if ($a == 3) {
    $id = Save::number($_GET['id']);
    $arrRes = $db->simple_query("DELETE FROM `" . $table_name . "` WHERE `id`='" . $id . "' LIMIT 1");
    @  unlink('../uploads/' . $folder . '/' . $id . '.jpg');
    @  unlink('../uploads/' . $folder . '/mini/' . $id . '.jpg');
    header('location: ' . $action);
}
//вывод списка
if ($a == '') {
    $class = 'item2';
    if ($result = $db->mysqli->query('SELECT * FROM `' . $table_name . '` ORDER BY `date_start` ')) {
        while ($row = $result->fetch_assoc()) {
            $class == 'item1' ? $class = 'item2' : $class = 'item1';
            $row['date'] = Save::duration_date($row['date_start'], $row['date_finish']);
            $row['class'] = $class;
            $content .= Event::admItem($row, $action, $folder);
        }
    }
}


$templater = array(
    '%_TITLE_%' => 'Администрирование : афиша',
    '%_JS_%' => TemplatesAdm::js_template().$js,
    '%_LOGO_%' => '',
    '%_SITE_%' => SITE_URL,
    '%_EXIT_%' => '<div id="exit"><a href="index.php?act=0">Выход</a></div>',
    '%_UP_MENU_%' => TemplatesAdm::up_menu_template($up_menu),
    '%_LEFT_MENU_%' => '<a href=./' . $action . '?a=1>Добавить</a>',
    '%_CONTENT_%' => $content,
);
TemplatesAdm::main_template($templater);
?>