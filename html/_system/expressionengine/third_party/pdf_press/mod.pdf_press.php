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
 File: mod.pdf_press.php
-----------------------------------------------------
 Dependencies: dompdf/
-----------------------------------------------------
 Purpose: Allows an EE template to be saved as a PDF
=====================================================
*/

require_once("dompdf/dompdf_config.inc.php");

if (! defined('BASEPATH')) exit('No direct script access allowed');

class Pdf_press {
	var $site_id = 1;
	
	function __construct() {
		
		$this->EE =& get_instance();
		
		$this->site_id = $this->EE->config->item('site_id');
		$this->EE->lang->loadfile('pdf_press');
		
		if(! class_exists('EE_Template'))
		{
			$this->EE->TMPL =& load_class('Template', 'libraries', 'EE_');
		}
	}
	
	public function save_to_pdf_form() {
		/* this method is deprecated, please use save_to_pdf or parse_pdf instead */
		$this->EE->output->show_user_error('general', $errors); 
		
		/*
		$tagdata = $this->EE->TMPL->tagdata;
		
		$path = $this->EE->TMPL->fetch_param('path', $this->EE->uri->uri_string());
		$attachment = $this->EE->TMPL->fetch_param('attachment', '1');
		$size = $this->EE->TMPL->fetch_param('size', DOMPDF_DEFAULT_PAPER_SIZE);
		$orientation = $this->EE->TMPL->fetch_param('orientation', 'portrait');
		$filename = $this->EE->TMPL->fetch_param('filename', '');
		
		// Load the form helper
		$this->EE->load->helper('form');
		
		$form_details = array('action'     => '',
	                  'name'           => 'save_to_pdf',
	                  'id'             => $this->EE->TMPL->form_id,
	                  'class'          => $this->EE->TMPL->form_class,
	                  'hidden_fields'  => array(
						'ACT'			=> $this->EE->functions->fetch_action_id('Pdf_press', 'create_pdf'),
						'path'			=> $path,
						'attachment'	=> $attachment,
						'size'			=> $size,
						'orientation'	=> $orientation,
						'filename'		=> $filename,
					  ),
	    );

		$output = $this->EE->functions->form_declaration($form_details);
		$output .= stripslashes($tagdata);
		$output .= "</form>";
		return $output;
		*/
	}
	
	public function save_to_pdf() {
		$path = $this->EE->TMPL->fetch_param('path', $this->EE->uri->uri_string());
		$attachment = $this->EE->TMPL->fetch_param('attachment', '1');
		$size = $this->EE->TMPL->fetch_param('size', DOMPDF_DEFAULT_PAPER_SIZE);
		$orientation = $this->EE->TMPL->fetch_param('orientation', 'portrait');
		$filename = $this->EE->TMPL->fetch_param('filename', '');
		
		$action_id = $this->EE->functions->fetch_action_id('Pdf_press', 'create_pdf');
		
		return $this->EE->functions->create_url("")."ACT=$action_id&path=".urlencode($path)."&size=".urlencode($size)."&orientation=$orientation&attachment=$attachment&filename=".urlencode($filename);
	}
	
	public function parse_pdf() {
		$settings = array(
			'attachment' 	=> $this->EE->TMPL->fetch_param('attachment', '1'),
			'orientation' 	=> $this->EE->TMPL->fetch_param('orientation', 'portrait'),
			'size'			=> $this->EE->TMPL->fetch_param('size', DOMPDF_DEFAULT_PAPER_SIZE),
			'filename'		=> $this->EE->TMPL->fetch_param('filename', ''),
			'encrypt'		=> false,
			'userpass'		=> '',
			'ownerpass'		=> '',
			'can_print'		=> true,
			'can_modify'	=> true,
			'can_copy'		=> true,
			'can_add'		=> true,
		);
		
		//get the key
		$key = $this->EE->TMPL->fetch_param('key', '');
		
		try {
		
			if($key != "") {
				//lookup the key & pull settings, if key not found then throw user error
				$data_settings = $this->_lookup_settings($key);
				//array merge the key with the user parameter overrides
				foreach($data_settings as $field => $value) {
					if($value != null && $value != "")
						$settings[$field] = $value;
				}
			}
		
			$html = $this->_render($this->EE->TMPL->tagdata);
		
			require_once("dompdf/dompdf_config.inc.php");
		
			$this->_generate_pdf($html, $settings);
			
		} catch (Exception $e) {
			$check_markup = $this->EE->lang->line('error_check_markup');
			$dompdf_error =  $this->EE->lang->line('dompdf_error');
			
			$errors = array($check_markup, 
					$dompdf_error.$e->getMessage());
			$this->EE->output->show_user_error('general', $errors); 
		}
	}
	
	public function create_pdf() {
		
		$settings = array(
			'attachment' 	=> $this->EE->input->get_post('attachment'),
			'orientation' 	=> $this->EE->input->get_post('orientation'),
			'size'			=> urldecode($this->EE->input->get_post('size')),
			'filename'		=> urldecode($this->EE->input->get_post('filename')),
			'encrypt'		=> false,
			'userpass'		=> '',
			'ownerpass'		=> '',
			'can_print'		=> true,
			'can_modify'	=> true,
			'can_copy'		=> true,
			'can_add'		=> true,
		);
		
		//get the key
		$key = $this->EE->input->get_post('key', '');		
		$path = urldecode($this->EE->input->get_post('path'));
		$filename = $settings['filename'];
		
		if($filename == "") {
			$filename = str_replace("/", "_", $path).".pdf";
			$settings['filename'] = $filename;
		}
		
		$full_url = $this->EE->functions->create_url($path);
		
		$html = $this->get_url_contents($full_url);
		
		require_once("dompdf/dompdf_config.inc.php");
		
		
			
		if($key != "") {
			//lookup the key & pull settings, if key not found then throw user error
			$data_settings = $this->_lookup_settings($key);
			//array merge the key with the user parameter overrides
			foreach($data_settings as $field => $value) {
				if($value != null && $value != "")
					$settings[$field] = $value;
			}
		}
			
		try {
			$this->_generate_pdf($html, $settings);
			
		} catch (Exception $e) {
			$check_markup = $this->EE->lang->line('error_check_markup');
			$dompdf_error =  $this->EE->lang->line('dompdf_error');
			
			$errors = array($check_markup, 
					$dompdf_error.$e->getMessage());
			$this->EE->output->show_user_error('general', $errors); 
		}
	}
	
	private function _lookup_settings($key) {
		$this->EE->db->select('id, key, data');
		$this->EE->db->where("key = '$key'");
		$query = $this->EE->db->get("pdf_press_configs");
		
		if($query->num_rows() > 0) {
			$row = $query->row();
			$setting_data = json_decode($row->data, true);
			$settings = array(
				'attachment' 	=> $setting_data['attachment'],
				'orientation' 	=> $setting_data['orientation'],
				'size'			=> $setting_data['size'],
				'filename'		=> $setting_data['filename'],
				'encrypt'		=> $setting_data['encrypt'],
				'userpass'		=> $setting_data['userpass'],
				'ownerpass'		=> $setting_data['ownerpass'],
				'can_print'		=> $setting_data['can_print'],
				'can_modify'	=> $setting_data['can_modify'],
				'can_copy'		=> $setting_data['can_copy'],
				'can_add'		=> $setting_data['can_add'],
			);
			
			return $settings;
		} else {
			
			$errors = array(lang('no_setting_found'), "missing setting preset: '$key'");
			$this->EE->output->show_user_error('general', $errors);
			
			//throw new Exception(lang('no_setting_found'));
		}
	}
	
	private function _generate_pdf($html, $settings) {
			
			
		$dompdf = new DOMPDF();
		$dompdf->load_html($html);
		$dompdf->set_paper($settings['size'], $settings['orientation']);
		$dompdf->render();
		
		if($settings['encrypt']) {
			$permissions = array();

			if($settings['can_print'])
				$permissions[] = 'print';

			if($settings['can_modify'])
				$permissions[] = 'modify';

			if($settings['can_copy'])
				$permissions[] = 'copy';

			if($settings['can_add'])
				$permissions[] = 'add';
				
			$dompdf->get_canvas()->get_cpdf()->setEncryption($settings['userpass'], $settings['ownerpass'], $permissions);
		}

		if($settings['attachment'] != '') {
			$options = array(
				'Attachment' => $settings['attachment']
			);
			$dompdf->stream($settings['filename'], $options);
		} else {
			$dompdf->stream($settings['filename']);	
		}
	}
	
	private function get_url_contents($url) {
		$fopen_enabled = ini_get('allow_url_fopen');
		/* gets the data from a URL */
		if($fopen_enabled) {
			return file_get_contents($url);
		} else if($this->curl_installed()) {
			//if fopen isn't enabled, then go get the html using curl
			$ch = curl_init();
			$timeout = 5;
			curl_setopt($ch, CURLOPT_URL, $url);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
			$data = curl_exec($ch);
			curl_close($ch);
			return $data;
		} else {
			$this->EE->output->show_user_error('general', $this->EE->lang->line('error_curl_fopen')); 
		}

	}

	//cool function from Veno Designs to render the template in its own context
	//http://venodesigns.net/tag/expressionengine/
	private function _render($text, $opts = array()) {
        /* Create a new EE Template Instance */
        $this->EE->TMPL = new EE_Template();

        /* Run through the initial parsing phase, set output type */
        $this->EE->TMPL->parse($text, FALSE);
		$this->EE->TMPL->final_template = $this->EE->TMPL->parse_globals($this->EE->TMPL->final_template);
        $this->EE->output->out_type = $this->EE->TMPL->template_type;

        /* Return source. If we were given opts to do template replacement, parse them in */
        if(count($opts) > 0) {
            $this->EE->output->set_output(
                $this->EE->TMPL->parse_variables(
                    $this->EE->TMPL->final_template, array($opts)
                )
            );
        } else {
            $this->EE->output->set_output($this->EE->TMPL->final_template);
        }
		return $this->EE->output->final_output;
    }

	private function curl_installed(){
	    return function_exists('curl_version');
	}
}

?>
