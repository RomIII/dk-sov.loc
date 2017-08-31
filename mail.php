<?php
include_once('./config.php');
if (isset($_GET['id'])) $id = Save::number($_GET['id']);

$js = '<script src="https://www.google.com/recaptcha/api.js"></script>';

$content = '';
$obj = new Mail();

if (isset($_POST['text'])) {
    @$arrValue['name'] = Save::text($_POST['name']);
    @$arrValue['contact'] = Save::text($_POST['contact']);
    @$arrValue['text'] = Save::text($_POST['text']);
    $obj->arrValue = $arrValue;
    if (isset($_POST['g-recaptcha-response']) && $_POST['g-recaptcha-response']) {
        $secret = CAPTCHA_SECRET_KEY;
        $ip = $_SERVER['REMOTE_ADDR'];
        $response = $_POST['g-recaptcha-response'];
        $rsp = file_get_contents("https://www.google.com/recaptcha/api/siteverify?secret=$secret&response=$response&remoteip=$ip");
        $arr = json_decode($rsp, TRUE);
        if ($arr['success']) {
            if (!empty($arrValue['text']) && $arrValue['text'] <> '') {
                $obj->to=ADMIN_MAIL;
                $obj->subject = 'Сообщение с сайта';
                $obj->body= $arrValue['name'].' - '.$arrValue['contact'].' <br>'.$arrValue['text'];
                $obj->send();
                header('Location: /mail.php?a=1');
            } else {
                $content .= '<b>Отсутствует текст</b>';
            }
        }

    }
} else {
    $content.=$obj->showForma();
}

//показываем после редиректа чтобы исключть f5
if(isset($_GET['a']) && $_GET['a']==1){
    $content = '<b>Ваш сообщение отправлено, скоро мы вам ответим!</b><br>';
}


$row['tit'] = 'Обратная связь';
$row['des'] = 'Обратная связь';
$row['key'] = 'Обратная связь';


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