<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');


$config['is_system_on'] = "y";
$config['ip2nation'] = "n";
$config['ip2nation_db_date'] = "1335677198";
$base_url = ((isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == "on") ? "https" : "http");
$base_url .= "://".$_SERVER['HTTP_HOST'];
$admin_url  = $base_url . '/casa.php';


/*
|--------------------------------------------------------------------------
| ExpressionEngine Config Items
|--------------------------------------------------------------------------
|
| The following items are for use with ExpressionEngine.  The rest of
| the config items are for use with CodeIgniter, some of which are not
| observed by ExpressionEngine, e.g. 'permitted_uri_chars'
|
*/

$config['app_version'] = '273';
$config['install_lock'] = "";
$config['license_number'] = "5343-3237-2705-9088";
$config['debug'] = "2";
//$config['cp_url'] = $admin_url;
$config['cp_url'] = "/var/www/packvehicular.com/system/index.php";
$config['doc_url'] = "http://ellislab.com/expressionengine/user-guide/";
$config['system_on'] = "y";
$config['allow_extensions'] = 'y';
$config['site_label'] = 'La Positiva';
$config['cookie_prefix'] = '';
$config['cookie_domain'] = "";
//$config['secure_forms'] = "n";


//------------------------------STUFF ADDED--------------------------

$config['site_url'] = $base_url; 
$config['server_path'] = FCPATH;
$config['site_index'] = '';
$config['theme_folder_url'] = $config['site_url']."/themes/";
$config['theme_folder_path'] = $config['server_path']."/themes/";
$config['save_tmpl_files'] = "y";
//$config['tmpl_file_basepath'] = $config['server_path']."/templates/";
$config['avatar_url'] = $base_url."/uploads/system/avatars/";
$config['avatar_path'] = $config['server_path']."/uploads/system/avatars/";
$config['photo_url'] = $base_url."/uploads/system/member_photos/";
$config['photo_path'] = $config['server_path']."/uploads/system/member_photos/";
$config['sig_img_url'] = $base_url."/uploads/system/signature_attachments/";
$config['sig_img_path'] = $config['server_path']."/uploads/system/signature_attachments/";
$config['prv_msg_upload_path'] = $config['server_path']."/uploads/system/pm_attachments/";

/* CodeIgniter Configuration
-------------------------------------------------------------------*/
$config['base_url'] = $config['site_url'];
$config['uri_protocol'] = 'AUTO';
$config['language'] = 'english';
$config['charset'] = 'UTF-8';
$config['subclass_prefix'] = 'EE_';
$config['permitted_uri_chars'] = 'a-z 0-9~%.:_\\-';
$config['enable_query_strings'] = FALSE;
$config['directory_trigger'] = 'D';
$config['controller_trigger'] = 'C';
$config['function_trigger'] = 'M';
$config['log_threshold'] = 0;
$config['log_path'] = '';
$config['log_date_format'] = 'Y-m-d H:i:s';
$config['time_reference'] = 'local';

$config['index_page'] = "";
$config['url_suffix'] = '';
$config['enable_hooks'] = FALSE;
$config['cache_path'] = '';
$config['encryption_key'] = '';
$config['global_xss_filtering'] = FALSE;
$config['csrf_protection'] = FALSE;
$config['compress_output'] = FALSE;
$config['rewrite_short_tags'] = TRUE;
$config['proxy_ips'] = "";


/* End of file config.php */
/* Location: ./system/expressionengine/config/config.php */