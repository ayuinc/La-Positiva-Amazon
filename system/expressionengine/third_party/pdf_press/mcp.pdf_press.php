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
 File: mcp.pdf_press.php
-----------------------------------------------------
 Dependencies: dompdf/
-----------------------------------------------------
 Purpose: Allows an EE template to be saved as a PDF
=====================================================
*/
require_once("dompdf/dompdf_config.inc.php");

if ( ! defined('EXT'))
{
    exit('Invalid file request');
}

class Pdf_press_mcp {
	
	var $site_id = 1;
	var $base_url;
	var $perpage = 25;
	
	function __construct() { 
		// Make a local reference to the ExpressionEngine super object 
		$this->EE =& get_instance(); 
		$this->site_id = $this->EE->config->item('site_id');
		$this->base_url = BASE.AMP.'C=addons_modules'.AMP.'M=show_module_cp'.AMP.'module=pdf_press';
	}
	
	function index() {
		$this->EE->load->helper('form');
		$this->EE->load->library('table');	
		$this->EE->load->library('javascript');
		
		$this->EE->cp->set_variable('cp_page_title', $this->EE->lang->line('pdf_press_module_name'));
		
		$vars['server_configs'] = $this->_test_server();
		$vars['dompdf_configs'] = $this->_test_dompdf();
		
		return $this->EE->load->view('index', $vars, TRUE);
	}
	
	function preview() {
		$this->EE->load->helper('form');
		$this->EE->load->library('table');	
		$this->EE->load->library('javascript');

		$this->EE->cp->set_variable('cp_page_title', $this->EE->lang->line('pdf_press_module_name'));
		
		$action_id = $this->EE->cp->fetch_action_id('Pdf_press', 'create_pdf');
		
		$url = $this->EE->functions->create_url("")."?ACT=$action_id&attachment=0&path=";

		$vars['dom_path'] = $url;
		$vars['paper_sizes'] = $this->_get_paper_sizes();

		return $this->EE->load->view('preview', $vars, TRUE);
	}
	
	function settings() {
		$this->EE->load->helper('form');
		$this->EE->load->library('table');	
		$this->EE->load->library('javascript');

		$this->EE->cp->set_variable('cp_page_title', $this->EE->lang->line('pdf_press_module_name'));
		
		$query = $this->_get_settings($this->perpage, 0);
		
		$vars['settings']	= array();
		
		$sizes = array_keys($this->_get_paper_sizes());
		
		foreach($query->result() as $row) {	
			$setting_data = json_decode($row->data, true);
			
			$setting_label = "<p>".lang('key').": ".$setting_data['key']." | ".lang('attachment').": "
				.($setting_data['attachment'] == 1 ? lang('Download') : lang('Browser'))." | ".lang('orientation').": "
				.$setting_data['orientation']
				." | ".lang('size').": ".$setting_data['size']." | ".lang('filename').": ".$setting_data['filename']
				." | ".lang('encrypt').": ".($setting_data['encrypt'] == 1 ? lang('Yes') : lang('No'))."</p>";
				
			$vars['settings'][] = array(
				'id'			=> $row->id,
				'key'			=> $row->key,
				'data'			=> $setting_label,
				'setting_link'	=> BASE.AMP.'C=addons_modules'.AMP.'M=show_module_cp'.AMP.'module=pdf_press'.AMP.'method=edit_setting'.AMP.'id='.$row->id,
			);
		}
		
		$total = $this->EE->db->count_all('pdf_press_configs');

		$this->EE->load->library('pagination');
		$p_config = $this->pagination_config('settings', $total);

		$this->EE->pagination->initialize($p_config);

		$vars['pagination'] = $this->EE->pagination->create_links();
		
		return $this->EE->load->view('settings', $vars, TRUE);
	}
	
	function edit_setting() {
		$this->EE->load->helper('form');
		$this->EE->load->library('table');	
		$this->EE->load->library('javascript');
		
		$this->EE->cp->set_variable('cp_page_title', lang('pdf_press_module_name'));
		
		$vars['form_hidden'] = null;
		$vars['action_url'] = BASE.AMP.'C=addons_modules'.AMP.'M=show_module_cp'.AMP.'module=pdf_press'.AMP.'method=save_setting';
		
		$id = $this->EE->input->get("id");
		$query = $this->EE->db->get('pdf_press_configs', $id);
		$row = $query->row();
		
		$vars['data'] = $this->_edit_setting_form($row);
		$vars['preset_page_title'] = lang('Edit Preset: '.$row->key);
		
		return $this->EE->load->view('setting-form', $vars, TRUE);
	}
	
	function new_setting() {
		$this->EE->load->helper('form');
		$this->EE->load->library('table');	
		$this->EE->load->library('javascript');
		
		$this->EE->cp->set_variable('cp_page_title', lang('pdf_press_module_name'));
		
		$vars['form_hidden'] = null;
		$vars['action_url'] = BASE.AMP.'C=addons_modules'.AMP.'M=show_module_cp'.AMP.'module=pdf_press'.AMP.'method=save_setting';
		$vars['data'] = $this->_new_setting_form();
		$vars['preset_page_title'] = lang('New Preset:');
		
		return $this->EE->load->view('setting-form', $vars, TRUE);
	}
	
	function fonts() {
		session_start();
		$_SESSION["authenticated"] = true;
		return $this->EE->load->view('fonts', null, TRUE); 
	}
	
	function font_controller() {
		require_once(PATH_THIRD."pdf_press/dompdf/www/controller.php");
		$font_error = $_SESSION['font-error'];
		$font_message = $_SESSION['font-message'];

		if($font_error) {
			$this->EE->session->set_flashdata('message_failure',$font_message); 
		} else {
			$this->EE->session->set_flashdata('message_success',$font_message); 
		}
		$this->EE->functions->redirect(BASE.AMP.'C=addons_modules'.AMP.'M=show_module_cp'.AMP.'module=pdf_press'.AMP.'method=fonts');
	}
	
	function save_setting() {
		
		//validation?
		$this->EE->load->helper(array('form', 'url'));
		$this->EE->load->library('form_validation');
		$this->EE->form_validation->set_rules('key', lang('key'), 'required');
		
		$config_data = array(
			'key'			=> $this->EE->input->post('key'),
			'attachment'	=> $this->EE->input->post('attachment'),
			'orientation'	=> $this->EE->input->post('orientation'),
			'size'			=> $this->EE->input->post('size'),
			'filename'		=> $this->EE->input->post('filename'),
			'encrypt'		=> $this->EE->input->post('encrypt'),
			'userpass'		=> $this->EE->input->post('userpass'),
			'ownerpass'		=> $this->EE->input->post('ownerpass'),
			'can_print'		=> $this->EE->input->post('can_print'),
			'can_modify'	=> $this->EE->input->post('can_modify'),
			'can_copy'		=> $this->EE->input->post('can_copy'),
			'can_add'		=> $this->EE->input->post('can_add'),
		);

		$id = $this->EE->input->post('id');

		$data = array(
			'id'	=> $id,
			'key'	=>  $this->EE->input->post('key'),
			'data'	=> json_encode($config_data)
		);
		
		if ($this->EE->form_validation->run() == FALSE)
		{	
			$this->EE->load->library('table');	
			$this->EE->load->library('javascript');
			
			$vars['form_hidden'] = null;
			$vars['action_url'] = BASE.AMP.'C=addons_modules'.AMP.'M=show_module_cp'.AMP.'module=pdf_press'.AMP.'method=save_setting';
			
			$row = (object) array('id' => $id, 'key' => $this->EE->input->post('key'), 'data' => json_encode($config_data));
			$vars['data'] = $this->_edit_setting_form($row);
			$vars['preset_page_title'] = lang('Edit Preset: '.$row->key);
			
			$this->EE->session->set_flashdata('message_failure',lang('setting_form_error')); 
			
			return $this->EE->load->view('setting-form', $vars, TRUE);
		}
		else
		{
			//$this->load->view('formsuccess');

			if($id == "") {
				$this->EE->db->insert('pdf_press_configs', $data);
			}
			else {
				$this->EE->db->where('id', $id);
				$this->EE->db->update('pdf_press_configs', $data);
			}

			$this->EE->functions->redirect(BASE.AMP.'C=addons_modules'.AMP.'M=show_module_cp'.AMP.'module=pdf_press'.AMP.'method=settings');
		}
				
		
	}
	
	function _new_setting_form() {
		$paper_sizes = array_keys($this->_get_paper_sizes());
		$sizes = array();
		
		foreach($paper_sizes as $index => $size) {
			$sizes[$size] = $size;
		}
		
		$data = array(	
			'key'			=> form_hidden('id', '').form_input('key', ''),
			'attachment'	=> "<span style='padding-left:10px;'>".form_radio('attachment', '1', TRUE)." ".lang('Download')."</span><span style='padding-left:10px;'>".form_radio('attachment', '0', FALSE)." ".lang('Browser')."</span>",//form_input('attachment', ''),
			'size'			=> form_dropdown('size', $sizes, DOMPDF_DEFAULT_PAPER_SIZE), //form_input('member_email', $query->row('member_email')),
			'orientation'	=> "<span style='padding-left:10px;'>".form_radio('orientation', 'portrait', TRUE)." ".lang('Portrait')."</span><span style='padding-left:10px;'>".form_radio('orientation', 'landscape', FALSE)." ".lang('Landscape')."</span>",//form_input('orientation', ''),
			'filename'		=> form_input('filename', ''),
			''				=> lang('encrypt_description'),
			'encrypt'		=> "<span style='padding-left:10px;'>".form_radio('encrypt', '1', FALSE)." ".lang('Yes')."</span><span style='padding-left:10px;'>".form_radio('encrypt', '0', TRUE)." ".lang('No')."</span>",//form_checkbox('encrypt','yes'),
			'userpass'		=> form_password('userpass', ''),
			'ownerpass'		=> form_password('ownerpass', ''),
			'can_print'		=> "<span style='padding-left:10px;'>".form_radio('can_print', '1', TRUE)." ".lang('Yes')."</span><span style='padding-left:10px;'>".form_radio('can_print', '0', FALSE)." ".lang('No')."</span>", //form_checkbox('can_print', 'yes'),
			'can_modify'	=> "<span style='padding-left:10px;'>".form_radio('can_modify', '1', TRUE)." ".lang('Yes')."</span><span style='padding-left:10px;'>".form_radio('can_modify', '0', FALSE)." ".lang('No')."</span>", //form_checkbox('can_modify', 'yes'),
			'can_copy'		=> "<span style='padding-left:10px;'>".form_radio('can_copy', '1', TRUE)." ".lang('Yes')."</span><span style='padding-left:10px;'>".form_radio('can_copy', '0', FALSE)." ".lang('No')."</span>", //form_checkbox('can_copy', 'yes'),
			'can_add'		=> "<span style='padding-left:10px;'>".form_radio('can_add', '1', TRUE)." ".lang('Yes')."</span><span style='padding-left:10px;'>".form_radio('can_add', '0', FALSE)." ".lang('No')."</span>", //form_checkbox('can_add', 'yes'),
		);
		
		return $data;
	}
	
	function _edit_setting_form($row) {
		$paper_sizes = array_keys($this->_get_paper_sizes());
		$sizes = array();
		
		$setting_data = json_decode($row->data, true);
		
		foreach($paper_sizes as $index => $size) {
			$sizes[$size] = $size;
		}
		
		$data = array(	
			'key'			=> form_hidden('id', $row->id).form_input('key', $row->key)."<br/><p><strong style='color:red'>".form_error('key')."</strong></p>",
			'attachment'	=> "<span style='padding-left:10px;'>".form_radio('attachment', '1', ($setting_data['attachment'] == 1 ? TRUE : FALSE) )." ".lang('Download')."</span><span style='padding-left:10px;'>".form_radio('attachment', '0', ($setting_data['attachment'] == 0 ? TRUE : FALSE))." ".lang('Browser')."</span>",
			'size'			=> form_dropdown('size', $sizes, $setting_data['size'] ), 
			'orientation'	=> "<span style='padding-left:10px;'>".form_radio('orientation', 'portrait', ($setting_data['orientation'] == 'portrait' ? TRUE : FALSE) )." ".lang('Portrait')."</span><span style='padding-left:10px;'>".form_radio('orientation', 'landscape', ($setting_data['orientation'] == 'landscape' ? TRUE : FALSE) )." ".lang('Landscape')."</span>",
			'filename'		=> form_input('filename', $setting_data['filename']),
			''				=> lang('encrypt_description'),
			'encrypt'		=> "<span style='padding-left:10px;'>".form_radio('encrypt', '1', ($setting_data['encrypt'] == 1 ? TRUE : FALSE) )." ".lang('Yes')."</span><span style='padding-left:10px;'>".form_radio('encrypt', '0', ($setting_data['encrypt'] == 0 ? TRUE : FALSE) )." ".lang('No')."</span>",
			'userpass'		=> form_password('userpass', $setting_data['userpass']),
			'ownerpass'		=> form_password('ownerpass', $setting_data['ownerpass']),
			'can_print'		=> "<span style='padding-left:10px;'>".form_radio('can_print', '1', ($setting_data['can_print'] == 1 ? TRUE : FALSE) )." ".lang('Yes')."</span><span style='padding-left:10px;'>".form_radio('can_print', '0', ($setting_data['can_print'] == 0 ? TRUE : FALSE) )." ".lang('No')."</span>", 
			'can_modify'	=> "<span style='padding-left:10px;'>".form_radio('can_modify', '1', ($setting_data['can_modify'] == 1 ? TRUE : FALSE) )." ".lang('Yes')."</span><span style='padding-left:10px;'>".form_radio('can_modify', '0',($setting_data['can_modify'] == 0 ? TRUE : FALSE) )." ".lang('No')."</span>", 
			'can_copy'		=> "<span style='padding-left:10px;'>".form_radio('can_copy', '1', ($setting_data['can_copy'] == 1 ? TRUE : FALSE) )." ".lang('Yes')."</span><span style='padding-left:10px;'>".form_radio('can_copy', '0', ($setting_data['can_copy'] == 0 ? TRUE : FALSE) )." ".lang('No')."</span>",
			'can_add'		=> "<span style='padding-left:10px;'>".form_radio('can_add', '1', ($setting_data['can_add'] == 1 ? TRUE : FALSE) )." ".lang('Yes')."</span><span style='padding-left:10px;'>".form_radio('can_add', '0', ($setting_data['can_add'] == 0 ? TRUE : FALSE) )." ".lang('No')."</span>", 
		);
		
		return $data;
	}
	
	function _test_server() {
		$server_configs = array(
		  "PHP Version" => array(
		    "required" => "5.0",
		    "value"    => phpversion(),
		    "result"   => version_compare(phpversion(), "5.0"),
		  ),
		  "DOMDocument extension" => array(
		    "required" => true,
		    "value"    => phpversion("DOM"),
		    "result"   => class_exists("DOMDocument"),
		  ),
		  "PCRE" => array(
		    "required" => true,
		    "value"    => phpversion("pcre"),
		    "result"   => function_exists("preg_match") && @preg_match("/./u", "a"),
		    "failure"  => "PCRE is required with Unicode support (the \"u\" modifier)",
		  ),
		  "Zlib" => array(
		    "required" => true,
		    "value"    => phpversion("zlib"),
		    "result"   => function_exists("gzcompress"),
		    "fallback" => "Recommended to compress PDF documents",
		  ),
		  "MBString extension" => array(
		    "required" => true,
		    "value"    => phpversion("mbstring"),
		    "result"   => function_exists("mb_send_mail"), // Should never be reimplemented in dompdf
		    "fallback" => "Recommended, will use fallback functions",
		  ),
		  "GD" => array(
		    "required" => true,
		    "value"    => phpversion("gd"),
		    "result"   => function_exists("imagecreate"),
		    "fallback" => "Required if you have images in your documents",
		  ),
		  "APC" => array(
		    "required" => "For better performances",
		    "value"    => phpversion("apc"),
		    "result"   => function_exists("apc_fetch"),
		    "fallback" => "Recommended for better performances",
		  ),
		  "GMagick or IMagick" => array(
		    "required" => "Better with transparent PNG images",
		    "value"    => null,
		    "result"   => extension_loaded("gmagick") || extension_loaded("imagick"),
		    "fallback" => "Recommended for better performances",
		  ),
		);

		if (($gm = extension_loaded("gmagick")) || ($im = extension_loaded("imagick"))) {
		  $server_configs["GMagick or IMagick"]["value"] = ($im ? "IMagick ".phpversion("imagick") : "GMagick ".phpversion("gmagick"));
		}
		
		return $server_configs;
	}
	
	function _test_dompdf() {
		$dompdf_constants = array();
		$defined_constants = get_defined_constants(true);

		$dom_constants = array(
		  "DOMPDF_DIR" => array(
		    "desc" => "Root directory of DOMPDF",
		    "success" => "read",
		  ),
		  "DOMPDF_INC_DIR" => array(
		    "desc" => "Include directory of DOMPDF",
		    "success" => "read",
		  ),
		  "DOMPDF_LIB_DIR" => array(
		    "desc" => "Third-party libraries directory of DOMPDF",
		    "success" => "read",
		  ),
		  "DOMPDF_FONT_DIR" => array(
		    "desc" => "Additional fonts directory",
		    "success" => "read",
		  ),
		  "DOMPDF_FONT_CACHE" => array(
		    "desc" => "Font metrics cache",
		    "success" => "write",
		  ),
		  "DOMPDF_TEMP_DIR" => array(
		    "desc" => "Temporary folder",
		    "success" => "write",
		  ),
		  "DOMPDF_CHROOT" => array(
		    "desc" => "Restricted path",
		    "success" => "read",
		  ),
		  "DOMPDF_UNICODE_ENABLED" => array(
		    "desc" => "Unicode support (thanks to additionnal fonts)",
		  ),
		  "DOMPDF_ENABLE_FONTSUBSETTING" => array(
		    "desc" => "Enable font subsetting, will make smaller documents when using Unicode fonts",
		  ),
		  "DOMPDF_PDF_BACKEND" => array(
		    "desc" => "Backend library that makes the outputted file (PDF, image)",
		    "success" => "backend",
		  ),
		  "DOMPDF_DEFAULT_MEDIA_TYPE" => array(
		    "desc" => "Default media type (print, screen, ...)",
		  ),
		  "DOMPDF_DEFAULT_PAPER_SIZE" => array(
		    "desc" => "Default paper size (A4, letter, ...)",
		  ),
		  "DOMPDF_DEFAULT_FONT" => array(
		    "desc" => "Default font, used if the specified font in the CSS stylesheet was not found",
		  ),
		  "DOMPDF_DPI" => array(
		    "desc" => "DPI scale of the document",
		  ),
		  "DOMPDF_ENABLE_PHP" => array(
		    "desc" => "Inline PHP support",
		  ),
		  "DOMPDF_ENABLE_JAVASCRIPT" => array(
		    "desc" => "Inline JavaScript support",
		  ),
		  "DOMPDF_ENABLE_REMOTE" => array(
		    "desc" => "Allow remote stylesheets and images",
		    "success" => "remote",
		  ),
		  "DOMPDF_ENABLE_CSS_FLOAT" => array(
		    "desc" => "Enable CSS float support (experimental)",
		  ),
		  "DOMPDF_ENABLE_HTML5PARSER" => array(
		    "desc" => "Enable the HTML5 parser (experimental)",
		  ),
		  "DEBUGPNG" => array(
		    "desc" => "Debug PNG images",
		  ),
		  "DEBUGKEEPTEMP" => array(
		    "desc" => "Keep temporary image files",
		  ),
		  "DEBUGCSS" => array(
		    "desc" => "Debug CSS",
		  ),
		  "DEBUG_LAYOUT" => array(
		    "desc" => "Debug layout",
		  ),
		  "DEBUG_LAYOUT_LINES" => array(
		    "desc" => "Debug text lines layout",
		  ),
		  "DEBUG_LAYOUT_BLOCKS" => array(
		    "desc" => "Debug block elements layout",
		  ),
		  "DEBUG_LAYOUT_INLINE" => array(
		    "desc" => "Debug inline elements layout",
		  ),
		  "DEBUG_LAYOUT_PADDINGBOX" => array(
		    "desc" => "Debug padding boxes layout",
		  ),
		  "DOMPDF_LOG_OUTPUT_FILE" => array(
		    "desc" => "The file in which dompdf will write warnings and messages",
		    "success" => "write",
		  ),
		  "DOMPDF_FONT_HEIGHT_RATIO" => array(
		    "desc" => "The line height ratio to apply to get a render like web browsers",
		  ),
			"DOMPDF_AUTOLOAD_PREPEND" => array(
		    "desc" => "Prepend the dompdf autoload function to the SPL autoload functions already registered instead of appending it",
		  ),
		  "DOMPDF_ADMIN_USERNAME" => array(
		    "desc" => "The username required to access restricted sections",
		    "secret" => true,
		  ),
		  "DOMPDF_ADMIN_PASSWORD" => array(
		    "desc" => "The password required to access restricted sections",
		    "secret" => true,
		    "success" => "auth",
		  ),
		);
	
		foreach($defined_constants["user"] as $const => $value) {
			if(array_key_exists($const, $dom_constants)) {
				$val_format = "";
				$val_desc = "";
				if (isset($dom_constants[$const]["secret"])) {
		        	$val_format = "******";
		        }
		        else {
		          	$val_format = var_export($value, true); 
		        }
	
				if (isset($dom_constants[$const]["desc"])) 
					$val_desc = $dom_constants[$const]["desc"];
				
				$message = "";
		        if (isset($dom_constants[$const]["success"])) {
		          switch($dom_constants[$const]["success"]) {
		            case "read":  
		              $success = is_readable($value);
		              $message = ($success ? "Readable" : "Not readable");
		            break;
		            case "write": 
		              $success = is_writable($value);
		              $message = ($success ? "Writable" : "Not writable");
		            break;
		            case "remote": 
		              $success = ini_get("allow_url_fopen");
		              $message = ($success ? "allow_url_fopen enabled" : "allow_url_fopen disabled");
		            break;
		            case "backend": 
		              switch (strtolower($value)) {
		                case "cpdf": 
		                  $success = true;
		                break;
		                case "pdflib":
		                  $success = function_exists("PDF_begin_document");
		                  $message = "The PDFLib backend needs the PDF PECL extension";
		                break;
		                case "gd":
		                  $success = function_exists("imagecreate");
		                  $message = "The GD backend requires GD2";
		                break;
		              }
		            break;
		            case "auth": 
		              $success = !in_array($value, array("admin", "password"));
		              $message = ($success ? "OK" : "Password should be changed");
		            break;
		          }
		          //echo 'class="' . ($success ? "ok" : "failed") . '"';
		        }
	
				$dompdf_constants[$const] = array(
					'value'		=> $val_format,
					'desc'		=> $val_desc,
					'success'	=> $success,
					'message'	=> $message
				);
			}
		}
		
		return $dompdf_constants;
	}

	function _get_paper_sizes() {
		$PAPER_SIZES = array(
		    "4a0" => array(0,0,4767.87,6740.79),
		    "2a0" => array(0,0,3370.39,4767.87),
		    "a0" => array(0,0,2383.94,3370.39),
		    "a1" => array(0,0,1683.78,2383.94),
		    "a2" => array(0,0,1190.55,1683.78),
		    "a3" => array(0,0,841.89,1190.55),
		    "a4" => array(0,0,595.28,841.89),
		    "a5" => array(0,0,419.53,595.28),
		    "a6" => array(0,0,297.64,419.53),
		    "a7" => array(0,0,209.76,297.64),
		    "a8" => array(0,0,147.40,209.76),
		    "a9" => array(0,0,104.88,147.40),
		    "a10" => array(0,0,73.70,104.88),
		    "b0" => array(0,0,2834.65,4008.19),
		    "b1" => array(0,0,2004.09,2834.65),
		    "b2" => array(0,0,1417.32,2004.09),
		    "b3" => array(0,0,1000.63,1417.32),
		    "b4" => array(0,0,708.66,1000.63),
		    "b5" => array(0,0,498.90,708.66),
		    "b6" => array(0,0,354.33,498.90),
		    "b7" => array(0,0,249.45,354.33),
		    "b8" => array(0,0,175.75,249.45),
		    "b9" => array(0,0,124.72,175.75),
		    "b10" => array(0,0,87.87,124.72),
		    "c0" => array(0,0,2599.37,3676.54),
		    "c1" => array(0,0,1836.85,2599.37),
		    "c2" => array(0,0,1298.27,1836.85),
		    "c3" => array(0,0,918.43,1298.27),
		    "c4" => array(0,0,649.13,918.43),
		    "c5" => array(0,0,459.21,649.13),
		    "c6" => array(0,0,323.15,459.21),
		    "c7" => array(0,0,229.61,323.15),
		    "c8" => array(0,0,161.57,229.61),
		    "c9" => array(0,0,113.39,161.57),
		    "c10" => array(0,0,79.37,113.39),
		    "ra0" => array(0,0,2437.80,3458.27),
		    "ra1" => array(0,0,1729.13,2437.80),
		    "ra2" => array(0,0,1218.90,1729.13),
		    "ra3" => array(0,0,864.57,1218.90),
		    "ra4" => array(0,0,609.45,864.57),
		    "sra0" => array(0,0,2551.18,3628.35),
		    "sra1" => array(0,0,1814.17,2551.18),
		    "sra2" => array(0,0,1275.59,1814.17),
		    "sra3" => array(0,0,907.09,1275.59),
		    "sra4" => array(0,0,637.80,907.09),
		    "letter" => array(0,0,612.00,792.00),
		    "legal" => array(0,0,612.00,1008.00),
		    "ledger" => array(0,0,1224.00, 792.00),
		    "tabloid" => array(0,0,792.00, 1224.00),
		    "executive" => array(0,0,521.86,756.00),
		    "folio" => array(0,0,612.00,936.00),
		    "commercial #10 envelope" => array(0,0,684,297),
		    "catalog #10 1/2 envelope" => array(0,0,648,864),
		    "8.5x11" => array(0,0,612.00,792.00),
		    "8.5x14" => array(0,0,612.00,1008.0),
		    "11x17"  => array(0,0,792.00, 1224.00),
		  );
		return $PAPER_SIZES;
	}

	function _get_settings($perpage, $rownum) {
		$this->EE->db->select('id, key, data');
		
		if($perpage > 0)
			$query = $this->EE->db->get('pdf_press_configs', $perpage, $rownum);
		else
			$query = $this->EE->db->get('pdf_press_configs');
			
		return $query;
	}
	
	function pagination_config($method, $total_rows) {
		//pass data to paginate class
		$config = array();
		$config['base_url'] = BASE.AMP.'C=addons_modules'.AMP.'M=show_module_cp'.AMP.'module=pdf_press'.AMP.'method='.$method;
		$config['total_rows'] = $total_rows;
		$config['per_page'] = $this->perpage;
		$config['page_query_string'] = TRUE;
		$config['query_string_segment'] = 'rownum';
		$config['full_tag_open'] = '<p id="paginationLinks">';
		$config['full_tag_close'] = '</p>';
		$config['prev_link'] = '<img src="'.$this->EE->cp->cp_theme_url.'images/pagination_prev_button.gif" width="13" height="13" alt="<" />';
		$config['next_link'] = '<img src="'.$this->EE->cp->cp_theme_url.'images/pagination_next_button.gif" width="13" height="13" alt=">" />';
		$config['first_link'] = '<img src="'.$this->EE->cp->cp_theme_url.'images/pagination_first_button.gif" width="13" height="13" alt="< <" />';
		$config['last_link'] = '<img src="'.$this->EE->cp->cp_theme_url.'images/pagination_last_button.gif" width="13" height="13" alt="> >" />';
		
		return $config;
	}
}

?>