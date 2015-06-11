<?
	include('global_functions.php');
	$data=array();
	/*/////////////////////////////////////////////
	• What is the member ID attached to this email?
	• If that email doesn't even match the database,
	• Send a JSON error back.
	• Create a temporary verification code at append via $_GET
	• Also add verification code to tblPasswordReset fldVerificationCode
	• Send e-mail with link and instructions
	• JSON Response
	*///////////////////////////////////////////////
	
	$lostEmail=$_POST['lostEmail'];
	$sql="SELECT id,salt FROM members WHERE email='".$lostEmail."'";
	$r=$mysqli->query($sql);
	$check=mysqli_num_rows($r);
	if($check>0){
		$result=mysqli_fetch_assoc($r);
		$salt=$result['salt'];
		$memberID=$result['id'];
		$v=md5($salt);
		
		//DELETE ANY UNUSED VERIFICATION CODE 
		$clear="DELETE FROM tblPasswordReset WHERE fkMemberID='".$memberID."'";
		$mysqli->query($clear);
		
		//INSERT NEW TEMPROARY VERIFICATIONCODE
		$resetLink=$html_root.'/login/?r='.$v;
		$q2="INSERT INTO tblPasswordReset (fkMemberID,fldVerificationCode) ";
		$q2.="VALUES('".$memberID."','".$v."')";
		$mysqli->query($q2);
		
		//EMAIL USER WITH LINK
		$subject="Reset Your Password On ".$noHTTP;
		$message="Use this link to reset your password: ";
		$message.='<a href="'.$resetLink.'">'.$resetLink.'</a>';
		$sender="donotreply@".$noHTTP;
		$headers = "MIME-Version: 1.0" . "\r\n";
		$headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
		$headers .= 'From: <'.$sender.'' . "\r\n";
		$inform=mail($lostEmail,$subject,$message,$headers);
		$data[]='A reminder has been sent to your e-mail address.';
		$data[]='success';
	}
	else{$data[]="No account is associated with this account. Could you have registered with a different e-mail address?";}
	
	//Echo JSON encoded value
	header('Content-Type: application/json');
	echo json_encode($data);
?>