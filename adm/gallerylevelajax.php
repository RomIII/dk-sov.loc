<?php

include_once('../config.php');
@$gallery_id = (int)$_GET['id'];

$module = 'gallerylevel';
$folder = 'gallerylevel/' . $gallery_id;
$db=DB::instance();

if (isset($_POST['photo_id'])) {
    $photo = $_POST['photo_id'];
    if (strpos($photo, ".") === FALSE) $photo = $photo . '.jpg';
    $dir = (int)$_POST['id'];
    unlink(UPLOADS_PATH . '/gallerylevel/' . $dir . '/' . $photo);
    unlink(UPLOADS_PATH . '/gallerylevel/' . $dir . '/mini/' . $photo);
    $db->simple_query("UPDATE `".DB_PREFIX."_gallerylevel` SET `count`=`count`-1 WHERE `id`=".$dir);
}


if ($_FILES) {

    if (!is_dir(UPLOADS_PATH . '/' . $folder)) {
        mkdir(UPLOADS_PATH . '/' . $folder);
        mkdir(UPLOADS_PATH . '/' . $folder . '/mini');
        mkdir(UPLOADS_PATH . '/' . $folder . '/tmp');
    }
    $upload = new Upload();
    $upload->setPath(UPLOADS_PATH . '/' . $folder);
    $upload->setMiniSize(array(200, 200));

    if (count($_FILES['filesToUpload'])) {
        $x4 = ($_FILES['filesToUpload']['name']);
        foreach ($x4 as $key => $value) {
            $arr['upload_file']['name'] = $_FILES['filesToUpload']['name'][$key];
            $arr['upload_file']['tmp_name'] = $_FILES['filesToUpload']['tmp_name'][$key];
            $arr['upload_file']['type'] = $_FILES['filesToUpload']['type'][$key];
            $n = explode(" ", microtime());
            $name = str_replace('.', '', $n[1] . $n[0] . rand(0, 100));
            $upload->uploadFile($arr, $name, true);
            $upload->showSuccess($module,$gallery_id, $name);
            $db->simple_query("UPDATE `".DB_PREFIX."_gallerylevel` SET `count`=`count`+1 WHERE `id`=".$gallery_id);
        }
    }

}


?>