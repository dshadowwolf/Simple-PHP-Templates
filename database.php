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
/* this is the completely abstract version */

class Database {
  private $__connection = "";
    
  public function __construct() {
    global $settings;
    $this->__connection = new PDO( $settings['database']['type'] . ":host=" . $settings['database']['host'] . ";dbname=" . $settings['database']['dbname'], $settings['database']['user'], $settings['database']['pass'] );
  }
    
  public function prepare($stmnt) {
    return $this->__connection->prepare($stmnt);
  }
};

class pdDatabase extends Database {
  private $__variables_query = "SELECT page-variables.name, page-variables.value FROM (page-name JOIN name-variable-interp ON page-name.id=name-variable-interpt.page-name) JOIN page-variables ON page-variables.id=name-variable-iterp.variable WHERE page-name.id=:page-id;";
  private $__page_query = "SELECT page-name.id as id, page-data.data as data FROM page-name JOIN page-data ON page-name.data = page-data.id WHERE page-name.name = :page-name;";
  private $__headers_query"SELECT page-data.data as data FROM page-data JOIN page-name ON page-name.headers = page-data.id WHERE page-name.name = :page-name;";
    
  public function __construct() {
    parent::__construct();
  }
    
  public function get_page( $pname ) {
    $q = array("","","");
    $r = array("","","");
    $q[0] = parent::prepare( $this->__page_query );
    $q[0]->bindParam( ':page-name', $pname, PDO::PARAM_STR );
    $q[0]->execute();
    $r[0] = $q[0]->fetch(PDO::FETCH_ASSOC);
    $q[1] = parent::prepare( $this->__variables_query );
    $q[1]->bindParam( ':page-id', $r[0]['id'], PDO::PARAM_INT );
    $q[1]->execute();
    $r[1] = $q[1]->fetchAll(PDO::FETCH_ASSOC);
    $q[2] = parent::prepare( $this->__headers_query );
    $q[2]->bindParam( ':page-name', $pname, PDO::PARAM_STR );
    $q[2]->execute();
    $r[2] = $q[2]->fetchAll(PDO::FETCH_ASSOC);
	
    $res = array( 'data' => $r[0]['data'], 'headers' => false, 'vars' => false );
    if( $q[1]->rowCount() > 0 ) {
	    $res['vars'] = array();
	    foreach( $r[1] as $rv ) {
        array_push($res['vars'], array( 'name' => $rv['page-variables.name'], 'value' => $rv['page-variables.value'] ) );
	    }
    }
	
    if( $q[2]->rowCount() > 0 ) {
	    $res['headers'] = "";
	    foreach( $r[2] as $rv ) {
        $res['headers'] .= $rv;
	    }
    }
	
    return $rv;
  }
};
