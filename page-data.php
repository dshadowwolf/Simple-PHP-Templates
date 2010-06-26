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

/* This is not a complete work - in fact it is far from being production ready. It is brand new code. */
/* Still not complete, but is closer to being done */
/* all data selection logic moved to database.php as part of 'pdDatabase' class */
class PageData {
  private $__has_header = false;
  private $__has_vars = false;
  private $__vars = array();
  private $__body = "";
  private $__header = "";

  public function __construct($page_name) {
    global $dbc;
      
    $data = $dbc->get_page( $page_name );
    $this->__body = $data['data'];
    if( $data['headers'] ) {
      $this->__header = $data['headers'];
      $this->__has_header = true;
    }
      
    if( $data['vars'] ) {
      $this->__has_vars = true;
      foreach( $data['vars'] as $k => $v ) {
	      $this->__vars[strtolower($k)] = $v;
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
