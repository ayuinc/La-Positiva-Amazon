<?php 

session_start();

$cmd = isset($_GET["cmd"]) ? $_GET["cmd"] : null;

//include PATH_THIRD."pdf_press/dompdf/dompdf_config.inc.php";
include PATH_THIRD."pdf_press/dompdf/www/functions.inc.php";

switch ($cmd) {
  case "clear-font-cache":
    $files = glob(DOMPDF_FONT_DIR."*.{UFM,AFM,ufm,afm}.php", GLOB_BRACE);
    foreach($files as $file) {
      unlink($file);
    }
  break;
  
  case "install-font":
    //if (!auth_ok()) break;
    
    $family = $_POST["family"];
    $data = $_FILES["file"];
	$message = "";
    
    foreach($data["error"] as $name => $error) {
      if ($error) {
        switch($error) {
          case UPLOAD_ERR_INI_SIZE:
          case UPLOAD_ERR_FORM_SIZE:
            $message = "The uploaded file exceeds the upload_max_filesize directive in php.ini."; break;
          case UPLOAD_ERR_PARTIAL: 
            $message = "The uploaded file was only partially uploaded."; break;
          case UPLOAD_ERR_NO_FILE: 
			$error = 0;
            break;
          case UPLOAD_ERR_NO_TMP_DIR: 
            $message =  "Missing a temporary folder."; break;
          default: 
            $message =  "Unknown error";
        }
        continue;
      }
      
      $weight = "normal";
      $style  = "normal";
      
      switch($name) {
        case "bold":   
          $weight = "bold"; break;
          
        case "italic": 
          $style  = "italic"; break;
          
        case "bold_italic": 
          $weight = "bold";
          $style  = "italic"; 
        break;
      }
      
      $style_arr = array(
        "family" => $family,
        "weight" => $weight,
        "style"  => $style,
      );
      
      Font_Metrics::init();
      
      if (!Font_Metrics::register_font($style_arr, $data["tmp_name"][$name])) {
        $message = $data["name"][$name]." is not a valid font file";
      }
      else {
        $message = "The <strong>$family $weight $style</strong> font was successfully installed !<br />";
		$error = 0;
      }
    }
  
	$_SESSION['font-error'] = $error;
	$_SESSION['font-message'] = $message;
   break;
}