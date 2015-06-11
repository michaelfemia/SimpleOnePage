<?
include('../global_functions.php');
include('setupFields.php');
//Rebuild global_variables.php with form input values
if(isset($_POST['siteSetup'])){
	$placeFillers=array();$newValues=array();
	foreach($setupFields as $setupVariable=>$setupField){
		$placeFillers[]="{".$setupVariable."}";
	}
	foreach($setupFields as $setupVariable=>$setupField){
		$newValues[]=$_POST[$setupVariable];
	}
	$template=file_get_contents('globalVariablesTemplate.php');
	$rewrite=str_replace($placeFillers,$newValues,$template);		
	$newpage=stripslashes($rewrite);
	$html_file='globalVariables.php';
	$fp= fopen($html_file,"w"); 
	fwrite($fp, $newpage);
	fclose($fp);
	print '<html><head>';
	print '<script>';
	print "window.location.replace('../login/?register=1');";
	print '</script>';
	print '</head>';
}

?>

