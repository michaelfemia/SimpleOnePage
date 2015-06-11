<?
include('global_functions.php');
$extraStyle="form#setup *{display:block;width:350px;margin:0 auto 0 auto;} ";
$extraStyle.="form#setup input{font-size:22px;height:26px;} ";
$extraStyle.="form#setup label{font-family:'lato';text-transform:uppercase;padding-top:15px;} ";
$editingPage=true;
include('headnav.php');
include('mgmt/globalVariables.php');
include('mgmt/setupFields.php');

print '<form id="setup" method="post" action="mgmt/updateGlobal.php" style="width:80%; margin:0 auto 0 auto;">';
foreach($setupFields as $setupName=>$setupLabel){
	print '<label for="'.$setupName.'">'.$setupLabel.'</label>';
	print '<input name="'.$setupName.'" value="'.${$setupName}.'">';
}
print '<button name="siteSetup" type="submit" value="setup">Set-up Site</button>';
print '</form>';
?>

