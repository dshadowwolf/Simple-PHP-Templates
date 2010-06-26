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
  private $__template = "";
  private $__page_data = "";
  private $__repl = array();
  private $__patt = array();
  private $__vars = array();
  
  public function __construct($primary_template) {
    $this->__template = file_get_contents($primary_template);
  }
  
  public function add_variable($name,$value) {
    array_push($this->__repl, $value);
    array_push($this->__patt, "/@@@".$name."@@@/imU");
    array_push($this->__vars, strtolower($name) );
  }
  
  public function load_page($page_name) {
    $page_details = new PageData($page_name);
    
    if( $page_details->has_vars() ) {
      foreach( $page_details->ext_vars() as $name => $value ) {
        array_push($this->__patt, "/@@@".$key."@@@/imU");
        array_push($this->__repl, $val );
        array_push($this->__vars, strtolower($val));
      }
    }
    array_push($this->__repl, $page_details->page_body());
    array_push($this->__patt, "/@@@CONTENTS@@@/imU");
    array_push($this->__vars, "contents");
    array_push($this->__vars, "headerbits");
    if( $page_details->has_header() ) {
      array_push($this->__repl, $page_details->page_header());
      array_push($this->__patt, "/@@@HEADERBITS@@@/imU");
    } else {
      array_push($this->__repl, "");
      array_push($this->__patt, "/@@@HEADERBITS@@@/imU");
    }
  }

  private function interpolate_values() {
    $lc = 0;
    
    $this->__page_data = $this->__template;
    while( preg_match( "/@@@.*@@@/Um", $this->__page_data ) ) {
      $this->__page_data = preg_replace( $this->__patt, $this->__repl, $this->__page_data );
      preg_match_all( "/@@@(.*)@@@/imU", $this->__page_data, $matches );
      foreach( $matches[1] as $var ) {
        if( !in_array( strtolower($var) ) ) {
          array_push( $this->__repl, "" );
          array_push( $this->__patt, "/@@@".$var."@@@/" );
          array_push( $this->__vars, strtolower($var) );
        }
      }
      $lc += 1;
      if( $lc > 5 ) {
        die( "<pre>Stuck in loop during variable interpolation, have had five loops and more variables still need to be replaced. Dumping page as-is, look for variables that were used but never defined in the system.</pre><br/><p>".$this->__page_data."</p></br>" );
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
  }
  
  public function display() {
    $this->interpolate_values();
    return $this->__page_data;
  }

  public function displayPage($page) {
    $this->load_page( $page );
    return $this->display();
  }
};
