<?php
include_once('./config.php');
if (isset($_GET['id'])) $id = Save::number($_GET['id']);


$content = '';
$obj = new Search();
$js='';

if(trim(@$_POST['search'])!=''){
$content=$obj->showResult($_POST['search']);
}else{
$content='по вашему запросу ничего не найдено';}

$row['tit'] = 'Результат поиска';
$row['des'] = 'Результат поиска';
$row['key'] = 'Результат поиска';


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