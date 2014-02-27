<?php

/*
=====================================================
PDF Press
-----------------------------------------------------
 http://www.anecka.com/pdf_press
-----------------------------------------------------
 Copyright (c) 2013 Patrick Pohler
=====================================================
 This software is based upon and derived from
 ExpressionEngine software protected under
 copyright dated 2004 - 2012. Please see
 http://expressionengine.com/docs/license.html
=====================================================
 File: upd.pdf_press.php
-----------------------------------------------------
 Dependencies: dompdf/
-----------------------------------------------------
 Purpose: Allows an EE template to be saved as a PDF
=====================================================
*/

if ( ! defined('EXT'))
{
    exit('Invalid file request');
}

class Pdf_press_upd {

    var $version = '1.5';
    
    function __construct() { 
        // Make a local reference to the ExpressionEngine super object 
        $this->EE =& get_instance(); 
    }

	function install() {
		$this->EE->load->dbforge();
		
		$data = array(
			'module_name' => 'Pdf_press',
			'module_version' => $this->version,
			'has_cp_backend' => 'y',
			'has_publish_fields' => 'n'
		);
		
		$this->EE->db->insert('modules', $data);
		
		$this->install_actions();
		
		$this->install_configs();
		
		return true;
	}
	
	function uninstall() {
		$this->EE->load->dbforge();
		$this->EE->db->select('module_id');
		
		$query = $this->EE->db->get_where('modules', array('module_name' => 'Pdf_press'));
		
	    $this->EE->db->where('module_id', $query->row('module_id'));
	    $this->EE->db->delete('modules');

	    $this->EE->db->where('class', 'Pdf_press');
	    $this->EE->db->delete('actions');
	
		$this->EE->dbforge->drop_table('pdf_press_configs');
		
		return TRUE;
	}
	
	function install_actions() {
		$actions = array(
			array(
				'class'		=> 'Pdf_press',
				'method'	=> 'create_pdf'
			),
		);
		
		$this->EE->db->insert_batch('actions', $actions);
	}
	
	function install_configs() {
		$fields = array(
			'id'		=> array('type' => 'int', 'constraint' => '10', 'unsigned' => TRUE, 'auto_increment' => TRUE),
			'key'		=> array('type' => 'varchar', 'constraint' => '500'),
			'data' 		=> array('type' => 'text', 'null' => true, 'default' => null)
		);

		$this->EE->dbforge->add_field($fields);
		$this->EE->dbforge->add_key('id', TRUE);

		$this->EE->dbforge->create_table('pdf_press_configs');
	}
	
	
	function update($current = '')
	{
		$this->EE->load->dbforge();
		
		
		if ($current == '' OR $current == $this->version)
		{
			return FALSE;
		}
		
		if( version_compare($current, '1.5', '<') ) {
			$this->install_configs();
			return TRUE;
		}

	    return FALSE;
	}
}
?>