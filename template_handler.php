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

class PageDetails {
  private $__has_header = false;
  private $__has_vars = false;
  private $__vars = array();
  private $__body = "";
  private $__header = "";

  public function __construct($page_name) {
    global $settings;
    $page_file = $settings['site']['page_data_dir'] . '/' . $page_name . ".dat";
    $temp = file( $page_file );
    foreach( $temp as $line ) {
      if( preg_match( '/^([\w\-\.\[\]]+) = (.+)$/', $line, $m ) ) {
	if( preg_match( '/^(.*)$/', $m[1], $n ) ) {
	  switch( strtolower( trim( $m[1] ) ) ) {
	  case 'page_file':
	    $tfn = $settings['site']['page_base_dir'] . '/' . trim($m[2]);
	    $this->__body = file( $tfn );
	    break;
	  case 'header_file':
	    $tfnn = $settings['site']['page_otherdata'] . '/' . trim($m[2]);
	    $this->__header = file( $tfnn );
	    $this->__has_header = true;
	    break;
	  default:
	    $this->__has_vars = true;
	    $this->__vars[trim($n[1])] = trim($m[2]);
	  }
	}
      }
    }
  }

  public function has_vars() {
    return $this->__has_vars;
  }

  public function has_header() {
    return $this->__has_header;
  }

  public function ext_vars() {
    return $this->__vars;
  }

  public function page_body() {
    return $this->__body;
  }

  public function page_header() {
    return $this->__header;
  }
  };

class Template {
  private $__vars = array();
  private $__template = array();
  private $__page_data = "";
  
  public function __construct($primary_template) {
    $this->__template = file($primary_template);
  }

  public function add_variable($name,$value) {
    $this->__vars[$name] = $value;
  }

  public function load_page($page_name) {
    $page_details = new PageDetails($page_name);

    if( $page_details->has_vars() ) {
      foreach( $page_details->ext_vars() as $name => $value ) {
	$this->__vars[$name] = $value;
      }
    }
    $this->__vars["contents"] = $page_details->page_body();
    if( $page_details->has_header() ) {
      $this->__vars["headerbits"] = $page_details->page_header();
    }
  }

  private function interpolate_values() {
    $temp = array();
    $t2  = array();
    $t3 = array();
    $vars = $this->__vars;

    foreach( $this->__template as $line ) {
      if( preg_match( "/(.*)@@@(.*)@@@(.*)/", $line, $matches ) ) {
	$v = strtolower( $matches[2] );
	foreach( $this->__vars[$v] as $vline ) {
	  array_push( $temp, $vline );
	}
      } else {
	array_push( $temp, preg_replace( '/(.*)@@@(.*)@@@(.*)/e', '\\1 $this->__vars[strtolower("\\2")] \\3', $line ) );
      }
    }

    // we now do it a second time to get all the variables :)
    foreach( $temp as $line ) {
      if( preg_match( "/(.*)@@@(.*)@@@(.*)/", $line, $matches ) ) {
	$v = strtolower( $matches[2] );
	$val = $this->__vars[$v];
	if( $val != "\"\"" || strlen($val) > 0 ) {
	  $fline = $matches[1] . $val . $matches[3] . "\n";
	} else {
	  $fline = $matches[1] . $matches[3] . "\n";
	}
	array_push( $t2, $fline );
      } else {
	array_push( $t2, $line );
      }
    }

    // we now do it a second time to get all the variables :)
    foreach( $t2 as $line ) {
      if( preg_match( "/(.*)@@@(.*)@@@(.*)/", $line, $matches ) ) {
	$v = strtolower( $matches[2] );
	$val = $this->__vars[$v];
	if( $val != "\"\"" || strlen($val) > 0 ) {
	  $fline = $matches[1] . $val . $matches[3] . "\n";
	} else {
	  $fline = $matches[1] . $matches[3] . "\n";
	}
	array_push( $t3, $fline );
      } else {
	array_push( $t3, $line );
      }
    }
    // manage conditionals here
    // perhaps all extra code...
    // [@ if ( condition ) @] <data> [@ else/elsif @] <data> [@ endif @]
    // [@ set <name> = <value> @]
    // [@ for ( <setup> ) @] <data> [@ end @]
    // [@ foreach <loop name> ( <name> ) @] <data> [@ end @]
    // do this when needed - will take too much time to implement
    // right now
    $this->__page_data = implode("",$t2);
  }

  public function display() {
    $this->interpolate_values();
    return $this->__page_data;
  }

  public function displayPage($page) {
    $this->load_page( $page );
    return $this->display();
  }
}
?>
