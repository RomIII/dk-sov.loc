<?php
include_once('./config.php');
if (isset($_GET['id'])) $id = Save::number($_GET['id']);

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


$class = 'item2';
$content = '';

$obj = new Gallerylevel();

if (isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    $content .= $obj->oneItem($id);
} else {
    $content .= $obj->mainList();
}

$row['tit'] = 'Галерея';
$row['des'] = 'Галерея';
$row['key'] = 'Галерея';
$row['text'] = $content;


$templater = array(
    '%_TIT_%' => $row['tit'],
    '%_DES_%' => $row['des'],
    '%_KEY_%' => $row['key'],
    '%_JS_%' => TemplatesSite::js_template() . $js,
    '%_NEWS_%' => Feed::widget(),
    '%_LEFT_MENU_%' => TemplatesSite::showMenu(),
    '%_CONTENT_%' => '<div class="text">' . $row['text'] . '</div>',
);
TemplatesSite::main_template($templater);

?>