<?
$sql="SELECT fldHTMLID,fldContentType,fldDescription,fldValue ";
$sql.="FROM tblHomepageManagement";
$r=$mysqli->query($sql);
$updateFields=array();$currentValue=array();
while($result=mysqli_fetch_array($r)){
	
	//Simple Text Elements
	if($result['fldContentType']=="1"){
		$editField='<input class="cms" id="edit'.$result['fldHTMLID'].'" ';
		$editField.='value="'.$result['fldValue'].'">';
	}
	
	//Secondary Page Navigation Elements
	if($result['fldContentType']=="2"){
		$sql2="SELECT fldPageName,fldPageLink FROM tblPageBlocks ";
		$sql2.="ORDER BY fldNavRank ASC";
		$r2=$mysqli->query($sql2);
		$editField='<select class="cms" id="edit'.$result['fldHTMLID'].'">';
		while($result2=mysqli_fetch_assoc($r2)){
			$editField.='<option name="'.$result2['fldPageLink'].'" value="'.$result2['fldPageName'].'" ';
			if($result['fldValue']==$result2['fldPageName']){
				$editField.='selected';
			}
			$editField.='>'.$result2['fldPageName'];
			$editField.='</option>'; 
		}
		$editField.='</select>';
	}
	
	//Image Files
	if($result['fldContentType']=="3"){
		$editField='<span><input class="cms" type="file" value="" id="file'.$result['fldHTMLID'].'" ';
			$editField.='name="edit'.$result['fldHTMLID'].'" ';
			$editField.='onchange="uploadImage('."'".$result['fldHTMLID']."')".'"></span>'."\n";
		$editField.= '<progress id="progressBar_'.$result['fldHTMLID'].'" value="0" max="100" style="display: none;"></progress>'."\n";
		$editField.='<input type="hidden" id="edit'.$result['fldHTMLID'].'" value="'.$result['fldValue'].'">'."\n";
	}
	if($result['fldContentType']=="4"){
		$editField='<textarea style="width:98%;font-size:20px;height:90px;padding:1%;" class="cms" id="edit'.$result['fldHTMLID'].'">';
		$editField.=$result['fldValue'];
		$editField.='</textarea>'."\n";
	}
	
	$editID=$result['fldHTMLID'];
	$updateFields[$editID]=$editField;
	$currentValue[$editID]=$result['fldValue'];
}
?>