<?php
$settings = parse_ini_file('settings.ini',true);
require_once('template_handler.php');
$no_output = false;

if( isset($_GET['page']) ) {
    $title = strtolower($_GET['page']);
} else {
    $title = 'main';
}

if( $title == "index.php" ) {
  $title = "main";
}

$template = new Template($settings['site']['primary_template']);
$template->load_page($title);

if(!$no_output) {
  echo $template->display();
} 
?>
