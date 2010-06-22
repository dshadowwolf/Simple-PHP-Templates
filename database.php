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
      $var_query = "SELECT page-variables.name, page-variables.value
		    FROM (page-name JOIN name-variable-interp ON page-name.id=name-variable-interpt.page-name)
		      JOIN page-variables ON page-variables.id=name-variable-iterp.variable
		    WHERE page-name.id=:page-id;";
      $data_query = "SELECT page-name.id as id, page-data.data as data FROM page-name JOIN page-data ON page-name.data = page-data.id WHERE page-name.name = :page-name;";
      
      $dbc = new PDO( $settings['database']['type'] . ":host=" . $settings['database']['host'] . ";dbname=" . $settings['database']['dbname'], $settings['database']['user'], $settings['database']['pass'] );
      $vq = $dbc->prepare($var_query);
      $dq = $dbc->prepare($data_query);
      $dq->bindParam(':page-name', $page_name, PDO::PARAM_STR);
      $dq->execute();
      $rdq = $dq->fetch(PDO::FETCH_ASSOC);
      $this->__body = $rdq['data'];
      $vq->bindParam(':page-id', int($rdq['id']), PDO::PARAM_INT);
      $vq->execute();
      if( $vq->rowCount() > 0 ) {
	  $rvq = $vq->fetchAll(PDO::FETCH_ASSOC);
	  foreach( $vq as $var ) {
	      if( $var['name'] == 'header' ) {
		  $this->__has_header = true;
		  $q = $dbc->prepare("SELECT page-data.data as data FROM page-data WHERE page-data.id = :pdid;");
		  $q->bindParam( ":pdid", int($var['value']), PDO::PARAM_INT );
		  $q->execute();
		  $rq = $q->fetch(PDO::FETCH_ASSOC);
		  $this->__header = $rq['data'];
	      } else {
		  if( !$this->__has_vars ) { $this->__has_vars = true; }
		  $this->__vars[strtolower($var['name'])] = $var['value'];
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
?>
