<?php
session_start();
include_once('../config.php');


if (isset($_GET['act']) && Save::number($_GET['act']) == '0') {
    session_unset();
    session_destroy();
    header('Location: http://' . SITE_URL);
}
if (isset($_POST['login'])) $save_login = Save::text($_POST['login']);
if (isset($_POST['pass'])) $save_pass = Save::text($_POST['pass']);
if (isset($_POST['keystring'])) {$keystring = Save::text($_POST['keystring']);}else{$keystring='';}
if (isset($_SESSION['captcha_keystring'])) $captcha_keystring = Save::text($_SESSION['captcha_keystring']);


if (isset($captcha_keystring) && $captcha_keystring == $keystring) {

    $arrRes = $db->querySelect(DB_PREFIX . '_auth', array('user', 'pass'), '`user`=\'' . $save_login . '\' LIMIT 1')->fetch_assoc();

    if ($save_login == $arrRes['user'] && $save_pass == $arrRes['pass'] && $save_login !='' && $save_pass !='') {
        $_SESSION['kod'] = md5(md5(DB_PREFIX));
        header('location: ' . $url_first_mod);
    } else {
        $content = 'Введены ошибочные данные.';
        $content .= TemplatesAdm::forma();
    }
} else {
    $content = TemplatesAdm::forma();
}

$templater = array(
    '%_TITLE_%' => 'Администрирование',
    '%_JS_%' => TemplatesAdm::js_template(),
    '%_LOGO_%' => '',
    '%_SITE_%' => SITE_URL,
    '%_EXIT_%' => '',
    '%_UP_MENU_%' => '',
    '%_LEFT_MENU_%' => '',
    '%_CONTENT_%' => $content,
);

TemplatesAdm::main_template($templater);
?>