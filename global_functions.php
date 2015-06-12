<? header("Content-Type: text/html; charset=utf-8");
error_reporting(0);
date_default_timezone_set('America/New_York');
$root='/';
$blueImp=false;
$imageDirectory="img";
include('mgmt/globalVariables.php');
define("HOST",$dbHost);     
define("USER",$dbUsername);     
define("PASSWORD",$dbPassword);    
define("DATABASE",$dbDatabase);    
define("CAN_REGISTER", "any");
define("DEFAULT_ROLE", "member");
define("SECURE", FALSE);    // FOR DEVELOPMENT ONLY!!!!
$mysqli = new mysqli(HOST, USER, PASSWORD, DATABASE);
function retrieveBlockElements($blockElementID,$new){
	global $mysqli,$editingPage,$galleryImageForm,$blueImp;
	if($new==true){$editingPage=true;}
	$return="";$content="";
	
$sql=<<<EOT
SELECT pkBlockElementID,fkElementType,fldPosition,fldValue,
fldSelectName,fldHTMLType,fldTypeName,fldContentType  
FROM tblBlockElements LEFT JOIN tblBlockElementTypes ON 
tblBlockElements.fkElementType=tblBlockElementTypes.pkTypeID 
WHERE pkBlockElementID='$blockElementID'
EOT;
	$query=$mysqli->query($sql);
	while($blockElements=mysqli_fetch_array($query)){			
		$typeName=$blockElements['fldTypeName'];
		$basicType=$blockElements['fldContentType'];
		$idNumber=$blockElements['pkBlockElementID'];
		$htmlType=$blockElements['fldHTMLType'];
		$type=$blockElements['fkElementType'];
		$HTMLID=$typeName.$idNumber;
		$class=$blockElements['fldSelectName'];
		$value=$blockElements['fldValue'];
		$childCount=0;
	}

	//One Dimensional Elements 
	if($basicType<=2){
		$content.=$value;
		$editNode=simpleTextEditor($HTMLID,$basicType,$typeName,$value);
	}
		
	//Images
	if(in_array($basicType,array(3,4))){
		$returnImages=retrieveImages($blockElementID,$editingPage,$HTMLID,$type);
		$content.=$returnImages[0];
		$childCount=$returnImages[1];
	}

	//Tables
	if(in_array($type,array(4))){$content.=retrieveTable($idNumber,$editingPage);}

	//Single Vimeo Embed
	if($basicType==6){
		$width=1280;$height=700;
		if($editingPage==true){$width=720;$height=480;}
		$iframe='<iframe src="//player.vimeo.com/video/'.$value.'" ';
		$iframe.='width="'.$width.'" height="'.$height.'" frameborder="0" webkitallowfullscreen ';
		$iframe.='mozallowfullscreen allowfullscreen></iframe>';
		$iframe.=editOnly('<div><span>Vimeo ID:&nbsp;&nbsp;</span><span id="'.$HTMLID.'ID">'.$value.'</span></div>');
		$iframe.=editOnly(simpleTextEditor($HTMLID."ID",$basicType,$typeName,$value));
	}
	################################
	## Final Printing of Elements ##
	################################
	
	//BlueImp Player
	if($type==6 && $editingPage!==true){$return.=blueimpPlayer($idNumber);}
	$return.=editOnly(editNodeOpen($idNumber,$class,$basicType));
	
	if($basicType==6){$return.=$iframe;}
	else{$return.=blockElementHTMLWrap($htmlType,$typeName,$HTMLID,$childCount,$content);}
	if($basicType<=2){$return.=editOnly($editNode);}
	
	$return.=editOnly(editNodeClose());	
	
	//BlueImp JS
	if(($type==6) && ($editingPage!==true)){$return.=blueimpJS($idNumber);}
	return $return;
}
function retrieveBlocks($editBoolean){
	global $mysqli;
	//Select all contentBlocks tied to this Page
	$sql="SELECT pkPageBlockID,fldPageName,fldPageLink,fldNavRank ";
	$sql.="FROM tblPageBlocks ";
	$sql.="ORDER BY fldNavRank ASC";
	$returnBlocks=$mysqli->query($sql);
	
	//GET BLOCK IDS
	while($blocks=mysqli_fetch_array($returnBlocks)){
		$blockID=$blocks['pkPageBlockID'];
		print "\t".'<div class="pageBlock" id="block'.$blocks['pkPageBlockID'].'">'."\n";
			print "\t\t".'<div class="department" id="'.$blocks['fldPageLink'].'">'."\n";
			print editOnly("<h1>".$blocks['fldPageName'].'</h1>');
			$sql2="SELECT pkBlockElementID FROM tblBlockElements ";
			$sql2.="WHERE fkBlock='".$blockID."' ";
			$sql2.="ORDER BY fkBlock, fldPosition ASC ";
			$returnBlockElements=$mysqli->query($sql2);
			while($blockElements=mysqli_fetch_array($returnBlockElements)){
				print retrieveBlockElements($blockElements['pkBlockElementID'],false);
			}
			echo newElementAdditionButton($blockID);
			print "\t\t".'</div><!--.department-->'."\n";
		print "\t".'</div><!--.pageBlock-->'."\n\n";
	}
}

//Markup Printers
function printNavElement($parameters){
	global $html_root;
	$return="\t".'<a ';
	if(isset($parameters['linkID'])){$return.='id="'.$parameters['linkID'].'" ';}
	$return.='href="';
		if($parameters['thisURL']!=str_replace('www.','',$html_root).'/'){
			$return.=$html_root.'/';
		}
		$return.=$parameters['navElement'];
		$navElID=str_replace("#","",$parameters['navElement']);
	$return.='"> ';
		$return.="\t\t".'<div class="navelement" ';
		$return.='id="nav'.$navElID.'"';
		if(isset($parameters['style'])){$return.='style="'.$parameters['style'].'" ';}
		$return.='>'."\n";
			$return.="\t\t\t".'<h3 title="'.stripslashes($parameters['navHeading']).'" class="navElementText">'.stripslashes($parameters['navHeading']).'</h3>'."\n";
		$return.="\t\t".'</div>'."<!--NAVELEMENT-->\n";
	$return.="\t".'</a><!--HREF WRAPPER-->'."\n";
	return $return;
}
function blockElementHTMLWrap($htmlType,$elementTypeName,$elementID,$childCount,$content){
if($childCount<1){$class=$elementTypeName;}
if($childCount>0){$class=$elementTypeName." children".$childCount;}
$openTag=<<<EOT
<$htmlType class="$class" id="$elementID">
EOT;
$closeTag=<<<EOT
</$htmlType><!--#$elementID-->
EOT;
return $openTag.$content.$closeTag;
}
function editNodeOpen($elementIDNum,$class,$basicType){			
if($basicType<=2){$textClass=" text";}
$editNodeOpen=<<<EOT
<div class="editingNode${textClass}" id="editingNode${elementIDNum}">
	<div class="nodeRank">
		<button class="nodeRankUp" onClick="shift('blockElement','$elementIDNum','up')"><img src="img/icons/uparrow.png"></button>
		<button class="nodeRankDown" onClick="shift('blockElement','$elementIDNum','down')"><img src="img/icons/downarrow.png"></button>
		<button class="deleteNode" onClick="deleteElement('blockElement','$elementIDNum','editingNode${elementIDNum}')"><img src="img/icons/trash.png"></button>
	</div>
	<div class="editor">	
EOT;
if($basicType>2){$editNodeOpen.='<h3 class="htmlType">'.$class.'</h3>';}
return $editNodeOpen;
}
function editNodeClose(){
$editNodeClose=<<<EOT
	</div>
	</div><!--.editingNode-->
EOT;
return $editNodeClose;
}
function editOnly($code){
	global $editingPage;
	if($editingPage==true){
		return $code;
	}
}
function dropzoneForm($blockElementID){
global $data; $data['dropZoneID']="upload".$blockElementID;
$form=<<<EOT
	<div class="galleryImageEdit" id="new${blockElementID}">
		<form id="upload${blockElementID}" action="js/AJAX.php" class="dropzone">
			<input type="hidden" name="blockID" value="$blockElementID">
			<input type="hidden" name="dropGo" value="1">
		</form>
	</div>
EOT;
return $form;

}
function imageArticleLink($image,$ID,$htmlType,$title,$link,$editingPage){
	global $data;
	$container="";
	$typeName=singleValueSQL("tblBlockElementTypes","fldTypeName","pkTypeID='".$htmlType."'");
	if($editingPage!==true){
		$container='<a id="'.$typeName.$ID.'Link" class="'.$typeName.'Link" ';
		if($link!=="Link"){
			$container.='href="'.$link.'" ';
			if(preg_match('[.pdf|http|www|.com|.org|.gov|.ca]',$link)==true){$container.='target="_blank" ';}
		}
		if($link=="Link"){
			$container.='href="javascript:void(0)" ';
		}
		$container.='>';
	}
	$container.='<div class="'.$typeName.'Image" ';
	if($editingPage==true){$container.='imageGalleryEditor ';}
	$container.='id="'.$typeName.'Image" style="background-image:url(img/568/'.$image.');">';
	$container.=	'<div class="'.$typeName.'Title" id="'.$typeName.$ID.'Title">'.$title.'</div>';
		if($editingPage==true){
			//Display Editing Inputs
			$container.=simpleTextEditor($typeName.$ID.'Title',1,$typeName,$title);
			$container.='<span id="'.$typeName.$ID.'Link">'.$link.'</span>';
			$container.=simpleTextEditor($typeName.$ID.'Link',1,$typeName,$link);
		}
	$container.='</div>';
	if($editingPage!==true){$container.='</a><!--imageTitleLink-->';}
	return $container;
}
function nestedRankForm($blockElementID,$childID,$child){
	global $data;
	$HTMLType=singleValueSQL("tblBlockElements","fkElementType","pkBlockElementID='".$blockElementID."'");
	$contentType=singleValueSQL("tblBlockElementTypes","fldContentType","pkTypeID='".$HTMLType."'");
	$typeName=singleValueSQL("tblBlockElementTypes","fldTypeName","pkTypeID='".$HTMLType."'");
	
	$content=$child;
	if($HTMLType==4){$promote="up";$demote="down";$type="tableRow";}
	else{$promote="back";$demote="forward";$type=$typeName;}
	
	$editID="element".$blockElementID."child".$childID;
	$class=$type.'Edit';
	if($contentType==4){$class.=" imageGalleryEditor";}
	
	$form='<div class="'.$class.'" id="'.$editID.'">';
$buttons=<<<EOT
	<div class="${type}EditButtons editButtons">
		<button onClick="shift('$type','$editID','up');".'">
		<img src="img/icons/${promote}arrow.png"></button>
		<button onClick="shift('$type','$editID','down')".';">
		<img src="img/icons/${demote}arrow.png"></button>
		<button onClick="deleteElement('$type','$childID','$editID');".'">
		<img src="img/icons/trash.png"></button>
	</div>
EOT;
	if(in_array($contentType,array(4,5))){$form.=$buttons;}
	$form.=$content.'</div>';
	return $form;
}
function newElementAdditionButton($blockID){
	global $mysqli;
	$sql="SELECT pkTypeID,fldSelectName FROM tblBlockElementTypes";
	$r=$mysqli->query($sql);
	$select='<div class="selectReplace closed">';
	while($result=mysqli_fetch_assoc($r)){
		$select.='<div name="'.$blockID.'" value="'.$result['pkTypeID'].'">'.$result['fldSelectName'].'</div>';
	}
	$select.='</div>';
	
	$button='<button id="elementAdditionButton'.$blockID.'" type="button" value="'.$blockID.'" class="newBlockElement">Add New Element</button>';
	$div='<div id="elementAdditionDiv'.$blockID.'" class="elementAdditionDiv">'.$button.$select.'</div>';
	$return=editOnly($div);
	return $return;
}
function retrieveTable($blockElementID,$editingPage){
	global $mysqli;
$tableQuery=<<<EOT
SELECT pkRowID,fldPosition FROM tblTableRows 
WHERE fkBlockElementID='$blockElementID' 
ORDER BY fldPosition ASC
EOT;
	$table="";
	$query=$mysqli->query($tableQuery);
	while($result=mysqli_fetch_assoc($query)){
		$table.=retrieveTableRow($result['pkRowID'],$blockElementID,$result['fldPosition'],$editingPage);
	}
	$button='<button class="rowAddition" id="newRow'.$blockElementID.'" onClick="addTableRow('."'".$blockElementID."'".')">';
	$button.='New Row</button>';
	$table.=editOnly($button);
	$content.=$table;
	return $content;
}
function retrieveTableRow($rowID,$elementID,$position,$editing){			
	global $mysqli;
	$row='<div class="tableRow">';
	$sql="SELECT fldColumn,fldValue FROM tblTableCells ";
	$sql.="WHERE fkRowID='".$rowID."' ";
	$sql.="ORDER BY fldColumn ASC";
	$q2=$mysqli->query($sql);
	while($cells=mysqli_fetch_assoc($q2)){
		$cellID='row'.$rowID.'column'.$cells['fldColumn'];
		$row.='<div class="column'.$cells['fldColumn'].'" id="'.$cellID.'">';
		$row.=$cells['fldValue'];
		$row.='</div>';
		if($editing==true){$row.='<input type="text" class="cms column'.$cells['fldColumn'].'" id="edit'.$cellID.'" value="'.$cells['fldValue'].'">';}
	}	
	$row.='</div>'."<!--.tableRow-->\n";
	if($editing==true){return nestedRankForm($elementID,$rowID,$row);}
	if($editing!==true){return $row;}
}
function deleteTableRow($rowID){
	$condition="pkRowID='".$rowID."'";//Get Table Number
	$tableID=singleValueSQL("tblTableRows","fkBlockElementID",$condition);
	$condition="fkRowID='".$rowID."'";
	deleteQuery("tblTableCells",$condition); //Delete Cells
	$condition="pkRowID='".$rowID."'";
	deleteQuery("tblTableRows",$condition); //Delete Row
}
function simpleTextEditor($id,$basicType,$elementType,$value){
	switch($basicType){
		case "1":
		$editor='<input class="cms '.$elementType.'Edit" id="edit'.$id.'" value="'.$value.'">';
		break;
		case "6":
		$editor='<input class="cms '.$elementType.'Edit" id="edit'.$id.'" value="'.$value.'">';
		break;
		case "2":
		$editor='<textarea class="cms '.$elementType.'Edit" id="edit'.$id.'">'.$value.'</textarea>';
		break;
		
	}
	return $editor;
}
function retrieveImages($blockElementID,$editingPage,$HTMLID,$type){
	global $mysqli;$content="";
	$imageQuery="SELECT fkImageID AS ImageID,fldImageName AS ImageName ";
	$imageQuery.="FROM tblGalleryBlocks ";
	$imageQuery.="LEFT JOIN tblImages ON tblGalleryBlocks.fkImageID=tblImages.pkImageID ";
	$imageQuery.="WHERE fkBlockElementID='".$blockElementID."' ORDER BY fldRank ASC ";
	
	$query=$mysqli->query($imageQuery);
	$childCount=$query->num_rows;$childNumber=1;
	while($imageQuery=mysqli_fetch_assoc($query)){			
		$content.=formatImage($imageQuery['ImageID'],$blockElementID,$editingPage,$childNumber);
		$childNumber++;
	}
	$content.=editOnly(dropzoneForm($blockElementID));
	return array($content,$childCount);
}
function formatImage($imageID,$blockElementID,$editingPage,$childNumber){
	$content="";	
	$HTMLType=singleValueSQL("tblBlockElements","fkElementType","pkBlockElementID='".$blockElementID."'");
	$contentType=singleValueSQL("tblBlockElementTypes","fldContentType","pkTypeID='".$HTMLType."'");
	$typeName=singleValueSQL("tblBlockElementTypes","fldTypeName","pkTypeID='".$HTMLType."'");
	$image=singleValueSQL("tblImages","fldImageName","pkImageID='".$imageID."'");
	$HTMLID=$typeName.$blockElementID;
	$table="tblGalleryBlocks";
	$condition="fkBlockElementID='".$blockElementID."' AND fkImageID='".$imageID."'";
	$caption=singleValueSQL($table,"fldCaption",$condition);
	$link=singleValueSQL($table,"fldLink",$condition);
	
	switch($HTMLType){
		case 3: $size=220;
		case 5: $size=1024;
		$formattedImage='<img alt="alt" class="gallery" src="img/'.$size.'/'.$image.'">';
		break;
		case 7: 
		$size=1024;
		$formattedImage='<div style="background-image:url(img/'.$size.'/'.$image.')"></div>';
		break;
		case 8: $size=568;
		$formattedImage=imageArticleLink($image,$imageID,$HTMLType,$caption,$link,$editingPage);
		break;
	}
	
	//Live Version
	if($editingPage!==true){
		$content.=$formattedImage;
		if($HTMLType==6){$content.=blueimpLinkTags($HTMLID,$image,$childNumber);}
	}
	
	//Editing Page
	if($editingPage==true){
		if( ($HTMLType!=8) && (in_array($contentType,array(3,4))) ){$image='<div style="height:215px;background-size:cover;background-image:url(img/568/'.$image.');"></div>';}
		else if($HTMLType==8){$image=imageArticleLink($image,$imageID,$HTMLType,$caption,$link,$editingPage);}
		else{$image=$formattedImage;}
		$content.=nestedRankForm($blockElementID,$imageID,$image);	
	}
	return $content;
}

//BlueImp Markup
function blueimpPlayer($blockElementID){	
$blueimpPlayer='<div id="galleryLinks'.$blockElementID.'player" class="blueimp-gallery">';
$blueimpPlayer.= <<< FOOBAR
	<div class="slides"></div>
	<a class="prev">‹</a>
	<a class="next">›</a>
	<ol class="indicator"></ol>
	<div class="modal fade">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" aria-hidden="true">X</button>
				</div><!--.modal-header-->
				<div class="modal-body next"></div><!--modal body next-->
				<div class="modal-footer">
					<button type="button" class="btn btn-default pull-left prev">
						<i class="glyphicon glyphicon-chevron-left"></i>Previous
					</button>
					<button type="button" class="btn btn-primary next">Next
						<i class="glyphicon glyphicon-chevron-right"></i>
					</button>
				</div><!--modal footer-->
			</div><!--modal content-->
		</div><!--modal dialogue-->
	</div><!--modal fade-->
</div><!--#blueimp-gallery-->
FOOBAR;
return $blueimpPlayer;
}
function blueimpJS($elementID){
$blueimpJS=<<<EOT
<script>
document.getElementById('galleryLinks${elementID}').onclick=function(event){
	event = event || window.event;
	var target = event.target || event.srcElement,
		link = target.src ? target.parentNode : target,
		options = {index:link,event:event},
		links = this.getElementsByTagName('a');
	blueimp.Gallery(links, options);
};
</script>
EOT;
return $blueimpJS;
}
function blueimpLinkTags($elementID,$imageName,$childNumber){
	$blueimpLinkTag='<a ';
	//$blueimpLinkTag.='data-gallery="#'.$elementID.'player" ';
	if($childNumber>15){$blueimpLinkTag.='class="linkOnly" ';}
	$blueimpLinkTag.='href="img/1024/'.$imageName.'" title="'.$imageName.'">';
	if($childNumber<=15){
		//Could restrict the number of images printed.
		//As long as there's an anchor tag, the image can be swiped to 
		$blueimpLinkTag.='	<div class="gThumb">';
		$blueimpLinkTag.='		<img alt="a" src="img/320/'.$imageName.'">';
		$blueimpLinkTag.='	</div><!--.gThumb-->';
	}
	$blueimpLinkTag.='</a>';
	return $blueimpLinkTag;
}

//SQL Shortcuts
function insertQuery($table,$fields,$values){
	global $mysqli;
	$fieldList="";foreach($fields as $field){$fieldList.=$field.",";}
	$valueList="";foreach($values as $value){$valueList.="'".$value."',";}
	$valueList=rtrim($valueList,",");
	$fieldList=rtrim($fieldList,",");	
	$mysqli->query("INSERT INTO ".$table." (".$fieldList.") VALUES(".$valueList.")");
	return $mysqli->insert_id;		
}
function deleteQuery($table,$condition){
	global $mysqli;
	$mysqli->query("DELETE FROM ".$table." WHERE ".$condition);
}
function updateQuery($table,$fields,$condition){
	global $mysqli;
	$fieldset="";
	foreach($fields as $field=>$value){
		$fieldset.=$field."='".$value."',";
	}
	$updates=rtrim($fieldset,",");
	$sql="UPDATE ".$table." SET ".$updates." WHERE ".$condition;
	$mysqli->query($sql);
	return $sql;
}
function getMax($table,$field,$condition){
	global $mysqli;
	$sql="SELECT MAX(".$field.") AS MaxVal ";
	$sql.="FROM ".$table;
	if(strlen($condition)>5){$sql.=" WHERE ".$condition;}
	$q=$mysqli->query($sql);
	$result=mysqli_fetch_array($q);
	$max=$result['MaxVal'];
	return $max;
}
function singleValueSQL($table,$field,$condition){
	global $mysqli;
	$sql="SELECT ".$field." FROM ".$table." ";
	$sql.="WHERE ".$condition;
	$q=$mysqli->query($sql);
	$r=mysqli_fetch_assoc($q);
	return $r[$field];
}
function reorderAfterDelete($idField,$table,$rankField,$reorderConditional){
	global $mysqli,$data;
	$sql="SELECT ".$idField." AS Item FROM ".$table." ";
	if(strlen($reorderConditional)>0){$sql.="WHERE ".$reorderConditional." ";}
	$sql.="ORDER BY ".$rankField." ASC";
	$q=$mysqli->query($sql);
	if($q->num_rows>1){$i=1;
		while( $remaining=mysqli_fetch_assoc($q) ){
			$fields=array($rankField=>$i);
			$condition=$idField."='".$remaining['Item']."'";
			$data[]=updateQuery($table,$fields,$condition);
			$i++;
		}
	}
}
function simpleResultCount($table,$field,$condition){
	global $mysqli;
	$sql="SELECT ".$field." FROM ".$table;
	if(strlen($condition)>3){$sql.=" WHERE ".$condition;}
	$result=$mysqli->query($sql);
	$rowCount=$result->num_rows;
	return $rowCount;
}

//Security Functions
function sec_session_start() {
    $session_name = 'sec_session_id';
    //Set a custom session name
    $secure = SECURE;//Stops JavaScript being able to access session id.
    $httponly = true;// Forces sessions to only use cookies.
    ini_set('session.use_only_cookies', 1);//Gets current cookies params.
    $cookieParams = session_get_cookie_params();
    session_set_cookie_params($cookieParams["lifetime"],
        $cookieParams["path"], 
        $cookieParams["domain"], 
        $secure,
        $httponly);
    session_name($session_name);
    session_start();// Start the PHP session 
    //session_regenerate_id();//regenerate the session, delete the old one. 
}
function login($email, $password, $mysqli) {
    global $errorMessage;
    // Using prepared statements means that SQL injection is not possible. 
    if ($stmt = $mysqli->prepare("SELECT id, personname, username, password, salt 
        FROM members
       WHERE email = ?
        LIMIT 1")) {
        $stmt->bind_param('s', $email);  // Bind "$email" to parameter.
        $stmt->execute();    // Execute the prepared query.
        $stmt->store_result();
 
        // get variables from result.
        $stmt->bind_result($user_id, $personname, $username, $db_password, $salt);
        $stmt->fetch();
 
        // hash the password with the unique salt.
        $password = hash('sha512', $password . $salt);
        if ($stmt->num_rows == 1) {
            // If the user exists we check if the account is locked
            // from too many login attempts 
 
            if (checkbrute($user_id, $mysqli) == true) {
                // Account is locked 
                // Send an email to user saying their account is locked
                $errorMessage="Too many failed login attempts.";
                return false;
            } else {
                // Check if the password in the database matches
                // the password the user submitted.
                if ($db_password == $password) {
                    // Password is correct!
                    // Get the user-agent string of the user.
                    $user_browser = $_SERVER['HTTP_USER_AGENT'];
                    // XSS protection as we might print this value
                    $user_id = preg_replace("/[^0-9]+/", "", $user_id);
                    $_SESSION['user_id'] = $user_id;
                    // XSS protection as we might print this value
                    $username = preg_replace("/[^a-zA-Z0-9_\-]+/", 
                                                                "", 
                                                                $username);
                    $_SESSION['username'] = $username;
                    $_SESSION['personname'] = $personname;
                    $_SESSION['userid']=$user_id;
                    $_SESSION['login_string'] = hash('sha512', 
                              $password . $user_browser);
                    // Login successful.
                    return true;
                } else {
                    // Password is not correct
                    // We record this attempt in the database
                    $now = time();
                    $mysqli->query("INSERT INTO login_attempts(user_id, time)
                                    VALUES ('$user_id', '$now')");
                    $errorMessage="Password isn't a match.";
                    return false;
                }
            }
        } else {
            // No user exists.
            $errorMessage="This username is not registered.";
            return false;
        }
    }
}
function checkbrute($user_id, $mysqli) {
    // Get timestamp of current time 
    $now = time();
 
    // All login attempts are counted from the past 2 hours. 
    $valid_attempts = $now - (2 * 60 * 60);
 
    if ($stmt = $mysqli->prepare("SELECT time 
                             FROM login_attempts <code><pre>
                             WHERE user_id = ? 
                            AND time > '$valid_attempts'")) {
        $stmt->bind_param('i', $user_id);
 
        // Execute the prepared query. 
        $stmt->execute();
        $stmt->store_result();
 
        // If there have been more than 5 failed logins 
        if ($stmt->num_rows > 5) {
            return true;
        } else {
            return false;
        }
    }
}
function login_check($mysqli) {
    global $mysqli;
    // Check if all session variables are set 
    if(
    	isset($_SESSION['user_id'], 
        $_SESSION['username'], 
        $_SESSION['login_string'])) {
 
			$user_id = $_SESSION['user_id'];
			$login_string = $_SESSION['login_string'];
			$username = $_SESSION['username'];
 
			// Get the user-agent string of the user.
			$user_browser = $_SERVER['HTTP_USER_AGENT'];
 
        if ($stmt = $mysqli->prepare("SELECT password FROM members WHERE id = ? LIMIT 1")) {
            // Bind "$user_id" to parameter. 
            $stmt->bind_param('i', $user_id);
            $stmt->execute();   // Execute the prepared query.
            $stmt->store_result();
 
            if ($stmt->num_rows == 1) {
                // If the user exists get variables from result.
                $stmt->bind_result($password);
                $stmt->fetch();
                $login_check = hash('sha512', $password . $user_browser);
 
                if ($login_check == $login_string) {
                    // Logged In!!!! 
                    return true;
                } else {
                    // Not logged in 
                    return false;
                }
            } else {
                // Not logged in 
                return false;
            }
        } else {
            // Not logged in 
            return false;
        }
    } else {
        // Not logged in 
        return false;
    }
}
sec_session_start();
if($editingPage==true){
	if((login_check($mysqli) !== true)){
		header('Location:'.$html_root.'/login/');
	}
}

//Image Handling
$image_sizes=array("1920","1440","1280","1024","568","320","220");
function imagickAdaptive($photo_uploader,$source_folder){
	//How can resources be minimized? 
	global $image_sizes;global $mysqli;global $data;
	$inFile=$_FILES[$photo_uploader]['tmp_name'];
	$image = new Imagick();
	$image->readImage($inFile);
	$image->setImageFormat('jpg');
	
	$version = $image->getVersion();
	$profile = 'http://www.color.org/sRGB_IEC61966-2-1_no_black_scaling.icc';
	
	//SET COLOR PROFILE TO SRGB
	if ((is_array($version) === true) && (array_key_exists('versionString', $version) === true)){
		$version = preg_replace('~ImageMagick ([^-]*).*~', '$1', $version['versionString']);
		if (is_file(sprintf('/usr/share/ImageMagick-%s/config/sRGB.icm', $version)) === true){
			$profile = sprintf('/usr/share/ImageMagick-%s/config/sRGB.icm', $version);
		}
	}
	if (($srgb = file_get_contents($profile)) !== false){
		$image->profileImage('icc', $srgb);
		$image->setImageColorSpace(Imagick::COLORSPACE_SRGB);
	}
	
	//QUALITY & RESOLUTION
	$qual=70;
	$image->setImageCompression(Imagick::COMPRESSION_JPEG);
	$image->setImageCompressionQuality($qual);
	$image->setImageResolution(72,72);
	$image->resampleImage(72,72,imagick::FILTER_UNDEFINED,1);
	$image->resizeImage(1920,0,imagick::FILTER_UNDEFINED,1);
	
	//CREATE A SAFE FILE NAME
	$thisimagename=($_FILES[$photo_uploader]["name"]);
	$data['debug2']=$thisimagename;
	$extension=pathinfo($thisimagename, PATHINFO_EXTENSION);
	$toencode= ereg_replace("[^A-Za-z0-9?!]", "",$thisimagename);
	$data['debug3']=$toencode; 
	$lowercase=strtolower($toencode);
	$encoded=rawUrlEncode($lowercase).time().".".$extension;
	
	foreach($image_sizes as $size){
		$width=$size;
		$image->resizeImage($width,0,Imagick::FILTER_LANCZOS,1);
		$outFile=$source_folder.$size.'/'.$encoded;
		$image->writeImage($outFile);	
	}
	$image->destroy();
		
	//Insert into tblImages (Image library for the site)
	$sql="INSERT INTO tblImages (fldImageName) VALUES('".$encoded."')";
	$mysqli->query($sql);
	$data['encodedInsert']=$sql;
	$data['imgencodedname']=$encoded;
}
function deleteImage($image){
	global $image_sizes,$documentRoot;
	foreach($image_sizes as $imageSize){
		$pathToDelete=$documentRoot.'img/'.$imageSize.'/'.$image;
		@unlink($pathToDelete);
	}	
}
function curPageURL() {
	$pageURL = 'http';
	if ($_SERVER["HTTPS"] == "on") {$pageURL .= "s";}
	$pageURL .= "://";
	if ($_SERVER["SERVER_PORT"] != "80") {
		$pageURL .= $_SERVER["SERVER_NAME"].":".$_SERVER["SERVER_PORT"].$_SERVER["REQUEST_URI"];
	} else {
		$pageURL .= $_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"];
	}
	return $pageURL;
}

//Simple Meta
$documentRoot=$_SERVER['DOCUMENT_ROOT'].'/';
$authors_name=$adminName."(".$adminEmail.")";
$content['pageid']=0;
$site_name=$domin_title;
$today=date('F d, Y');
$condensed=str_replace(" ","",$domain_title);//HappyFarm
$noHTTP=$condensed.".com";//HappyFarm.com
$jsDomain=strtolower($condensed);//happyfarm.com
$endDomain=$condensed.".com";//
$DNR_Email="donotreply@".$noHTTP;
?>