<?php
session_start();
include_once('../config.php');
if ($_SESSION['kod'] !== md5(md5(DB_PREFIX))) exit ("Ошибка регистрации");

$action = 'feed.php'; // название этого файла
$table_name = DB_PREFIX . '_feed'; // название таблицы с которой работаем
$folder = 'feed'; //название папки куда грузить картинки, должно совпадать с названием класса модели

//text,textarea,spaweditor,hidden,date
$formItems = array(
    array('title' => 'Название', 'type' => 'text', 'name' => 'tit', 'value' => '', 'save' => 'text'),
    array('title' => 'дата', 'type' => 'date', 'name' => 'date', 'value' => '', 'save' => 'text'),
    array('title' => 'Ключевые слова', 'type' => 'text', 'name' => 'key', 'value' => '', 'save' => 'text'),
    array('title' => 'Описание', 'type' => 'text', 'name' => 'des', 'value' => '', 'save' => 'text'),
    array('title' => 'Teкст ', 'type' => 'ckeditor', 'name' => 'text', 'value' => '', 'save' => 'text_html')
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
        $lastId = $db->lastId;
        mkdir(UPLOADS_PATH . '/' . $folder . '/' . $lastId);
        mkdir(UPLOADS_PATH . '/' . $folder . '/' . $lastId . '/mini');
        mkdir(UPLOADS_PATH . '/' . $folder . '/' . $lastId . '/tmp');
        $content = 'Данные добавлены';

        if ($_FILES) {
            $upload = new Upload();
            $upload->setPath(UPLOADS_PATH . '/' . $folder);
            $upload->setMiniSize(array(200, 200));
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
    $js = '
<script type="text/javascript" src="/js/ajaxupload.js" ></script>
                <script type="text/javascript" >
                    $(function () {
                        var btnUpload = $(\'#upload\');
                        var status = $(\'#status\');
                        new AjaxUpload(btnUpload, {
                            action: \'feedajax.php?id=' . $id . '\',
                            name: \'filesToUpload\',
                            sizeLimit: \'3\',
                            max: \'3\',
                            onSubmit: function (file, ext) {
                                if (!(ext && /^(jpg|png|jpeg|gif)$/.test(ext))) {
                                    // extension is not allowed
                                    status.text(\'Only JPG, PNG or GIF files are allowed\');
                                    return false;
                                }
                                status.html(\'Загружаю...<br><img src=images/loading.gif>\');
                            },
                            onComplete: function (file, response) {
                                //On completion clear the status
                                status.text(\'\');
                                //Add uploaded file to list
                                if (response !== "error") {
                                    $(\'#files\').append(response).addClass(\'success\');
                                } else {
                                    $(\'#files\').text(\'ошибка загрузки\').addClass(\'error\');
                                }
                            }
                        });
                         $("body").on("click", ".remove-button", function () {
                             $.post("feedajax.php", {id:$(this).attr(\'id\'),photo_id:$(this).attr(\'photo_id\')}, function(theResponse){});
                             $(this).parent().parent().remove();
                         });
                    });
                </script>';


    $content .= '
<div id="upload"><span><img src=./images/upload_multi.png alt="загрузка фото" title="загрузка фото"></span></div>
                    <span id="status"></span>
<table id=addtext border=0 width=100%>
            <tr>
                <td  valign=top><br>

                    </td>
                <td ><h1></h1>
                    <div id=sort>
                        <ul id=files>';
    $pathMain = UPLOADS_PATH . '/' . $folder . '/' . $id . '/mini';
    if (is_dir($pathMain)) {
        $path = scandir($pathMain);
        foreach ($path as $k) {
            if ($k != '.' AND $k != '..') {
                $content .= '<li>
                <table>
                <tr>
                <td width=160><img src="/uploads/feed/' . $id . '/mini/' . $k . '" class="img-responsive" width="100"></td>
                <td><button type="button" photo_id="' . $k . '" id="' . $id . '" class="close remove-button form-control-static" aria-label="Close" onClick="javascript: return false;"><span aria-hidden="true">&times;</span></button></td>
                </tr>
                </table>
                 </li>';
            }
        }
    }

    $content .= '</ul>
                    </div>

                </td>
            </tr>
        </table>
';
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

    $pagination = new Pagination($table_name,20,array('moduleName'=>$action));
    $pagination->createPagination();
    $content .= $pagination->show();

    if ($result = $db->mysqli->query('SELECT * FROM `' . $table_name . '` ORDER BY `nom` DESC LIMIT ' . $pagination->start . ',' . $pagination->countOnPage)) {
        while ($row = $result->fetch_assoc()) {
            $class == 'item1' ? $class = 'item2' : $class = 'item1';
            $row['class'] = $class;
            $content .= Feed::admItem($row, $action, $folder);
        }
    }
    $content .= $pagination->show();
}


$templater = array(
    '%_TITLE_%' => 'Администрирование : лента',
    '%_JS_%' => TemplatesAdm::js_template() . $js,
    '%_LOGO_%' => '',
    '%_SITE_%' => SITE_URL,
    '%_EXIT_%' => '<div id="exit"><a href="index.php?act=0">Выход</a></div>',
    '%_UP_MENU_%' => TemplatesAdm::up_menu_template($up_menu),
    '%_LEFT_MENU_%' => '<a href=./' . $action . '?a=1>Добавить</a>',
    '%_CONTENT_%' => $content,
);
TemplatesAdm::main_template($templater);
?>