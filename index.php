<?php
/*
    Simple PHP Templates v. 0.0.1
    Copyright (C) 2010  Daniel Hazelton

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License along
    with this program; if not, write to the Free Software Foundation, Inc.,
    51 Franklin Street, Fifth Floor, Boston, MA 02110-1301 USA.
*/
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
