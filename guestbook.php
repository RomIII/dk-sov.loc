<?php
include_once('./config.php');
if (isset($_GET['id'])) $id = Save::number($_GET['id']);

$js = '<script src="https://www.google.com/recaptcha/api.js"></script>';

$content = '';
$obj = new Guestbook();

if (isset($_POST['text'])) {
    $arrValue['name'] = Save::text($_POST['name']);
    $arrValue['mail'] = Save::text($_POST['mail']);
    $arrValue['text'] = Save::text($_POST['text']);
    $obj->arrValue = $arrValue;
    if (isset($_POST['g-recaptcha-response']) && $_POST['g-recaptcha-response']) {
        $secret = CAPTCHA_SECRET_KEY;
        $ip = $_SERVER['REMOTE_ADDR'];
        $response = $_POST['g-recaptcha-response'];
        $rsp = file_get_contents("https://www.google.com/recaptcha/api/siteverify?secret=$secret&response=$response&remoteip=$ip");
        $arr = json_decode($rsp, TRUE);
        if ($arr['success']) {
            if (!empty($arrValue['text']) && $arrValue['text'] <> '') {
                $db->queryInsert(DB_PREFIX . '_guestbook', $arrValue);
                $content .= '<b>Ваш коментарий добавлен!</b><br>';
            } else {
                $content .= '<b>Отсутствует текст</b>';
            }
        }

    }
}

$content .= $obj->mainList();

$row['tit'] = 'Гостевая книга';
$row['des'] = 'Гостевая книга';
$row['key'] = 'Гостевая книга';


$templater = array(
    '%_TIT_%' => $row['tit'],
    '%_DES_%' => $row['des'],
    '%_KEY_%' => $row['key'],
    '%_JS_%' => TemplatesSite::js_template() . $js,
    '%_LEFT_MENU_%' => TemplatesSite::showMenu(),
    '%_NEWS_%' => Feed::widget(),
    '%_CONTENT_%' => '<div class="text">' . $content . '</div>',
);
TemplatesSite::main_template($templater);

?>