<?php
//внес изменения опять
//где изменения пошли в мастер


include_once('./config.php');
if (isset($_GET['id'])) $id = Save::number($_GET['id']);

if (!isset($_GET['a'])) {
    //запрос по умолчанию
    $arrResult = $db->querySelect(DB_PREFIX . '_main_menu', '', '`nom`=1 LIMIT 1');
} else {
    $a = Save::number($_GET['a']);
    if ($a == 0) {
        // страница из меню
        $arrResult = $db->querySelect(DB_PREFIX . '_main_menu', '', '`id`=' . $id . ' LIMIT 1');
    }
    if ($a == 1) {
        //свободная страница
        $arrResult = $db->querySelect(DB_PREFIX . '_free_pages', '', '`id`=' . $id . ' LIMIT 1');
    }
}
$js = '<script type="text/javascript" src="/js/highslide/highslide-with-gallery.js"></script>
        <link rel="stylesheet" type="text/css" href="/js/highslide/highslide.css" />
        <!--[if IE 6]>
        <link rel="stylesheet" type="text/css" href="/js/highslide/highslide-ie6.css" />
        <![endif]-->

        <script type="text/javascript">
            hs.graphicsDir = \'./js/highslide/graphics/\';
            hs.align = \'center\';
            hs.transitions = [\'fade\', \'fade\'];
            hs.outlineType = \'rounded-white\';
            hs.fadeInOut = false;
            hs.dimmingOpacity = 0.25;

            // Add the controlbar
            hs.addSlideshow({
                //slideshowGroup: \'group1\',
                interval: 5000,
                repeat: false,
                useControls: true,
                fixedControls: \'fit\',
                overlayOptions: {
                    opacity: 0.75,
                    position: \'bottom center\',
                    hideOnMouseOut: false
                }
            });
        </script>';

if ($arrResult) {
    $row = $arrResult->fetch_assoc();

    if(!isset($_GET['a'])){
        $event = new Event();
        $row['text']= $row['text'].' <br> '.$event->mainList();
    }

    $templater = array(
        '%_TIT_%' => $row['tit'],
        '%_JS_%' => TemplatesSite::js_template().$js,
        '%_LEFT_MENU_%' => TemplatesSite::showMenu(),
        '%_NEWS_%' => Feed::widget().'<br>'.Banner::widget(),
        '%_CONTENT_%' => $row['text'],

    );
    TemplatesSite::main_template($templater);
} else {
    echo 'database is not install';
}

?>