<?php 
include('../global_functions.php');
sec_session_start();
///////////////////////////////////////////////////
////////        REGISTER A NEW USER        ////////
///////////////////////////////////////////////////
if( ($_GET['register']=="1") && (isset($_POST['personname'],$_POST['username'], $_POST['email'], $_POST['p'])) ){
    $error_msg = "";
    // Sanitize and validate the data passed in
    $personname = filter_input(INPUT_POST, 'personname', FILTER_SANITIZE_STRING);
    $username = filter_input(INPUT_POST, 'username', FILTER_SANITIZE_STRING);
    $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
    $email = filter_var($email, FILTER_VALIDATE_EMAIL);
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error_msg .= 'The email address you entered is not valid';
    }
 
    $password = filter_input(INPUT_POST, 'p', FILTER_SANITIZE_STRING);
    if (strlen($password) != 128) {
        // The hashed pwd should be 128 characters long.
        // If it's not, something really odd has happened
        $error_msg .= 'Invalid password configuration.';
    }
 
    //Does this email address already have an account?
    $prep_stmt = "SELECT id FROM members WHERE email = ? LIMIT 1";
    $stmt = $mysqli->prepare($prep_stmt);
    if ($stmt) {
        $stmt->bind_param('s', $email);
        $stmt->execute();
        $stmt->store_result();
        if ($stmt->num_rows == 1) {
            $error_msg .= 'A user with this email address already exists.';
        }
    } 
    else {$error_msg .= 'Database error';}
 
    if (empty($error_msg)) {
        // Create a random salt
        $random_salt = hash('sha512', uniqid(openssl_random_pseudo_bytes(16), TRUE));
        $password = hash('sha512', $password . $random_salt);
 
        // Insert the new user into the database 
        if ($insert_stmt = $mysqli->prepare("INSERT INTO members (personname,username, email, password, salt) VALUES (?,?, ?, ?, ?)")) {
            $insert_stmt->bind_param('sssss',$personname,$username, $email, $password, $random_salt);
            // Execute the prepared query.
            if (! $insert_stmt->execute()) {}
            //Send a confirmation e-mail
			$send=array();
			$send['message']="<html><head><title>Glad To Have You With Us</title></head><body>";
			$send['message'].='<h2 style="color:#B72E2E;font-style:italic;">Log-In To '.$domain_title.'</h2>';
			$send['message'].='<p><b><a href="'.$html_root.'/login/">Log-in to your account</a></b></p>';
			$send['message'].="</body></html>";
			$send['message'].=date(DATE_RFC2822)." end of message.";
			$headers = "MIME-Version: 1.0" . "\r\n";
			$headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
			$headers .= 'From: <'.$admin_email.'>' . "\r\n";
		
			//mail($recipient,$subject,$message,$headers)
			$success=mail($admin_email,"Welcome To ".$domain_title,$send['message'],$headers);
			$success=mail($email,"Welcome To ".$domain_title,$send['message'],$headers);
        }
        header('Location:'.$html_root.'/login/?new=1');
    }
}

////////////////////////////////////////////////////////
////////        LOGOUT & DESTROY SESSION        ////////
////////////////////////////////////////////////////////
if($_GET['logout']=="1"){
	// Unset all session values 
	$_SESSION = array();
	// get session parameters 
	$params = session_get_cookie_params();
	// Delete the actual cookie. 
	setcookie(session_name(),
			'', time() - 42000, 
			$params["path"], 
			$params["domain"], 
			$params["secure"], 
			$params["httponly"]);
	// Destroy session 
	session_destroy();
	header('Location:'.$html_root.'/login/');
}

//////////////////////////////////////////////
////////        RESET PASSWORD        ////////
//////////////////////////////////////////////
if(isset($_GET['r'])){
	//IF $_GET['r'] is invalid, auto-reroute to non-$_GET
	$validCode="SELECT * FROM tblPasswordReset WHERE fldVerificationCode='".$_GET['r']."'";
	$validCodeCheck=$mysqli->query($validCode);
	$validCodeResult=$validCodeCheck->num_rows;
	if($validCodeResult<1){
		header('Location:'.$html_root.'/login/');
	}
}
if(isset($_POST['email'],$_POST['p'],$_GET['r'])){
	//Dual hash password and create new salt
	//Get memberID
	//Does the userID and Verification Code Match?
	//Update password
	//Delete verification code	
	
	//IS THIS CODE PAIRED WITH THIS USER?
	$sql="SELECT id FROM members WHERE email='".$_POST['email']."'";
	$r=$mysqli->query($sql);
	$check=mysqli_num_rows($r);
	if($check>0){		
		$result=mysqli_fetch_assoc($r);
		$memberID=$result['id'];
		
		//Is the verification code valid?
		$vc="SELECT * FROM tblPasswordReset WHERE ";
		$vc.="fkMemberID='".$memberID."' AND ";
		$vc.="fldVerificationCode='".$_GET['r']."'";
		$r2=$mysqli->query($vc);
		$check2=mysqli_num_rows($r2);
		
		if($check2>0){	
			$password = filter_input(INPUT_POST, 'p', FILTER_SANITIZE_STRING);
			if (strlen($password) != 128) {
				// The hashed pwd should be 128 characters long.
				// If it's not, something really odd has happened
				$error_msg .= 'Invalid password configuration.';
			}
			if (empty($error_msg)) {
				//Update Password
				$random_salt = hash('sha512', uniqid(openssl_random_pseudo_bytes(16), TRUE));
				$password = hash('sha512', $password . $random_salt);
				$q2="UPDATE members SET ";
				$q2.="password='".$password."', ";
				$q2.="salt='".$random_salt."' ";
				$q2.="WHERE id='".$memberID."'";
				
				$mysqli->query($q2);
				
				//Delete Verification Code
				$q3="DELETE FROM tblPasswordReset WHERE ";
				$q3.="fkMemberID='".$memberID."'";
				$mysqli->query($q3);
			}
		}
	}
	else{$error="";}
}

//////////////////////////////////////////
////////        LOGIN USER        ////////
//////////////////////////////////////////
if(isset($_POST['email'], $_POST['p'])) {
    $email = $_POST['email'];
    $password = $_POST['p']; // The hashed password.
 
    if (login($email, $password, $mysqli) == true) {
        header('Location:'.$html_root.'/editable.php');
    } 
    else {
        header('Location:'.$html_root.'/login/?error=1');
    }
} 

////////////////////////////////////////////////////////////
////////        IF ALREADY LOGGED IN --> CMS        ////////
////////////////////////////////////////////////////////////
if (login_check($mysqli) == true) {
	$logged = 'in';
	header('Location:'.$html_root.'/editable.php');
} 
else {$logged = 'out';}
$loginPage=true;
include('../headnav.php');

$formAction="";
$legendText="Administrator Log-In";
if($_GET['register']=="1"){
	$formAction=$root.'login/?register=1';
	$legendText="Sign up";
}
	
	print'<div id="loginformImage" style="background-image: url('."'".$html_root.'/'.$imageDirectory.'/1024/'.$socialMediaThumbIMG."'".');">'; 
		print'<form class="ui-corner-all" id="loginform" action="" method="post" name="login_form">'; 			
			print'<fieldset>';
				print'<legend>'.$legendText.'</legend>';
				
				//Error Messages
				if (isset($_GET['error'])) {echo '<p style="width:90%; margin:0 auto 0 auto;font-weight:bold;" class="error">Error Logging In!</p>';}
			
				//New user registered
				if($_GET['new']=="1"){
					print '<p style="width:90%; margin:0 auto 0 auto;font-weight:bold;">Go ahead and log-in!';
					//print ' We also just e-mailed you to confirm your account details.';
					print'</p>'; 
				}
				if($_GET['register']=="1"){
					$uid=rand(5,3);
					print'<label for="personname">Your Name</label>
					<input class="lgtext ui-corner-all" type="text" name="personname" id="personname" />
					<input type="hidden" name="username" id="username" value="'.$uid.'"/>';
				}		
				
				//EMAIL INPUT            
				print '<label for="email">Your e-mail</label>';
				print '<input style="background-color:rgb(137,206,222);" type="text" class="lgtext ui-corner-all" name="email" id="email"/>';
				
				//PASSWORD INPUT
				print '<label id="passwordLabel" for="password">';
				if(isset($_GET['r'])){print 'New ';}
				print 'Password';
				print '</label>';
				print '<input style="background-color:rgb(137,206,222);" id="passwordField" type="password" class="lgtext ui-corner-all" name="password" id="password"/>';
				
				//FORGOT PASSWORD
				if(!isset($_GET['r'])){
					print '<p id="retrievalInstructions" style="display:none;font-weight:bold">To retrieve your password, ';
					print 'enter your e-mail address above, and press "Send Reminder." If you know your ';
					print 'password, log-in by clicking <a id="rememberPassword" href="'.$html_root.'/login/'.'" style="display:none;">HERE</a></p>';
					print '<a id="forgotPassword" href="#">Forgot Password?</a>';
				}
				?>
				<script>
					$("#forgotPassword").click(function(){
						//Replace the password field with retrieval instructions
						//Change form action and button text
						$("#passwordLabel,#passwordField,#forgotPassword").hide();
						$("#rememberPassword,#retrievalInstructions").show();
						$("#loginButton").attr("onclick",'null');
						$("#loginButton").text('Send Reminder');
						$("#loginform").submit(function (e){
							e.preventDefault();
							//RETRIEVAL AJAX CALL
							var userEmail=$("#email").val();
							var postFormData=new FormData();
							postFormData.append("lostEmail",userEmail);
							$.ajax({
								xhr: function(){
								   var xhr = new window.XMLHttpRequest();
								   return xhr;
								 },
								url : "AJAX.php",
								type: "POST",
								data : postFormData,
								dataType : 'json',
								processData: false,
								contentType: false,
								success: function(data, textStatus, jqXHR){
									$("#retrievalInstructions").text(data[0]);
									if(data[1]=="success"){
										$("#loginButton").hide();
									}
								}
							});
						});
					});
				</script>
				<?
				if($_GET['register']=="1"){
					print'<label for="confirmpwd">Confirm Password</label> 
					<input class="lgtext ui-corner-all" type="password" name="confirmpwd" id="confirmpwd" />';			
				}
			
			print'<button id="loginButton" class="loginbutton lgtext" ';
			if($_GET['register']=="1"):
				print'value="Register"'; 
				print'onclick="return regformhash(this.form, this.form.username, this.form.email,';
				print 'this.form.password, this.form.confirmpwd);">Sign Up</button>';
			elseif(isset($_GET['r'])):
				print 'value="Reset"';
				print'onclick="formhash(this.form, this.form.password);">Reset Password</button>';
			else:
				print' value="Login" ';
				print'onclick="formhash(this.form, this.form.password);">Enter Publishing Area</button>';	
			endif;
			print'</fieldset>';	
		?>
		</form><!--#LOGINFORM-->
	</div><!--HOMEPAGELEAD-->
</body>
</html>