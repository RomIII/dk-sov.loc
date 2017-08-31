<?php
session_start();
include_once('../config.php');
if ($_SESSION['kod'] !== md5(md5(DB_PREFIX))) exit ("Ошибка регистрации");

$table_name = DB_PREFIX . '_gallerylevel'; // название таблицы с которой работаем
$folder = 'gallerylevel'; //название папки куда грузить картинки, должно совпадать с названием класса модели

$gallery_id = (int)$_GET['id'];
$res = $db->queryRow($table_name, array('tit'), "`id`=" . $gallery_id);

$js = '
<script type="text/javascript" src="/js/jquery.js"></script>
<script type="text/javascript" src="/js/jquery-ui.js"></script>
<script type="text/javascript" src="/js/ajaxupload.js" ></script>
                <script type="text/javascript" >
                    $(function () {
                        var btnUpload = $(\'#upload\');
                        var status = $(\'#status\');
                        new AjaxUpload(btnUpload, {
                            action: \'gallerylevelajax.php?id=' . $gallery_id . '\',
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
                             $.post("gallerylevelajax.php", {id:$(this).attr(\'id\'),photo_id:$(this).attr(\'photo_id\')}, function(theResponse){});
                             $(this).parent().parent().remove();
                         });
                    });
                </script>';

$content = '<h1>' . $res['tit'] . '</h1>
<table id=addtext border=0 width=100%>
            <tr>
                <td  valign=top><br>

                    </td>
                <td ><h1></h1>
                    <div id=sort>
                        <ul id=files>';
$pathMain = UPLOADS_PATH . '/' . $folder . '/' . $gallery_id . '/mini';
if (is_dir($pathMain)) {
    $path = scandir($pathMain);
    foreach ($path as $k) {
        if($k!='.' AND $k!='..') {
            $content .= '<li>
                <table>
                <tr>
                <td width=160><img src="/uploads/gallerylevel/' . $gallery_id . '/mini/' . $k . '" class="img-responsive" width="100"></td>
                <td><button type="button" photo_id="' . $k . '" id="' . $gallery_id . '" class="close remove-button form-control-static" aria-label="Close" onClick="javascript: return false;"><span aria-hidden="true">&times;</span></button></td>
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


$templater = array(
    '%_TITLE_%' => 'Администрирование : фотогалерея',
    '%_JS_%' => $js,
    '%_LOGO_%' => '',
    '%_SITE_%' => SITE_URL,
    '%_EXIT_%' => '<div id="exit"><a href="index.php?act=0">Выход</a></div>',
    '%_UP_MENU_%' => TemplatesAdm::up_menu_template($up_menu),
    '%_LEFT_MENU_%' => '<div id="upload"><span><img src=./images/upload_multi.png alt="загрузка фото" title="загрузка фото"></span></div>
                    <span id="status"></span>',
    '%_CONTENT_%' => $content,
);
TemplatesAdm::main_template($templater);
?>