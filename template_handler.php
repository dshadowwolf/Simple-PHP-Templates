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

require_once("page-data.php");

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
    $page_details = new PageData($page_name);

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
