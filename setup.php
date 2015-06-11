<?
$protected=true;
include('global_functions.php');
$extraStyle="form#setup *{display:block;width:350px;margin:0 auto 0 auto;} ";
$extraStyle.="form#setup input{font-size:22px;height:26px;} ";
$extraStyle.="form#setup label{font-family:'lato';text-transform:uppercase;padding-top:15px;} ";
$editingPage=true;
include('headnav.php');
include('mgmt/globalVariables.php');
$setupFields=array(
	"html_root"=>"HTML Root",
	"domain_title"=>"Domain Title",
	"titleTagline"=>"Tagline",
	"adminName"=>"Administrator Name",
	"adminEmail"=>"Administrator Email",
	"adminPassword"=>"Administrator Password",
	"twitterHandle"=>"Twitter Handle",
	"dbHost"=>"Database Host",
	"dbDatabase"=>"Database Name",
	"dbUsername"=>"Database Username",
	"dbPassword"=>"Database Password",
	"imageDirectory"=>"Image Directory",
	"searchDescription"=>"SEO Description",
	"socialMediaThumbIMG"=>"Social Media Thumbnail",
	"searchKeywords"=>"Site Keywords",
	"googleAnalytics"=>"Google Analytics Script"
);

//Rebuild global_variables.php with form input values
if(isset($_POST['siteSetup'])){
	$placeFillers=array();$newValues=array();
	foreach($setupFields as $setupVariable=>$setupField){
		$placeFillers[]="{".$setupVariable."}";
	}
	foreach($setupFields as $setupVariable=>$setupField){
		$newValues[]=$_POST[$setupVariable];
	}
	$template=file_get_contents('mgmt/globalVariablesTemplate.php');
	$rewrite=str_replace($placeFillers,$newValues,$template);		
	$newpage=stripslashes($rewrite);
	$html_file="mgmt/globalVariables.php";
	$fp = fopen($html_file,"w"); 
	fwrite($fp, $newpage); 
	fclose($fp);
	//print '<script>location.reload(true);</script>';
}



print '<form id="setup" method="post" action="" style="width:80%; margin:0 auto 0 auto;">';
foreach($setupFields as $setupName=>$setupLabel){
	print '<label for="'.$setupName.'">'.$setupLabel.'</label>';
	print '<input name="'.$setupName.'" value="'.${$setupName}.'">';
}
print '<button name="siteSetup" type="submit" value="setup">Set-up Site</button>';
print '</form>';
?>

