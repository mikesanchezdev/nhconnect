<?php require_once('includes/config.php'); 

mysql_select_db($database_follows, $follows);
$pagetitle = "Password Request";
session_start();
//SET SUCCESS NOTICES
function set_msg($msg) 
	{
	$_SESSION['msg'] = $msg;
	}

function display_msg() {
	echo $_SESSION['msg'];
	unset($_SESSION['msg']);
}

$dis = none;
if (isset($_SESSION['msg'])) {
$dis = block;
}
//


if ($_POST['email']) {
mysql_select_db($database_follows, $follows);
$query_passwordcheck = "SELECT * FROM follows_users WHERE user_email = '".$_POST['email']."'";
$passwordcheck = mysql_query($query_passwordcheck, $follows) or die(mysql_error());
$row_passwordcheck = mysql_fetch_assoc($passwordcheck);
$totalRows_passwordcheck = mysql_num_rows($passwordcheck);

if ($totalRows_passwordcheck==1) { 
	$redirect = "login.php";
	set_msg('Your password has been sent.');

//SEND EMAIL WITH PASSWORD
$password = $row_passwordcheck['user_password'];
$emailfrom = $row_passwordcheck['user_email'];
$name = MINISTRY . " FollowUp Application";
$subject = "Your Password";
$message = "Your password is $password.";
$emailto = $row_passwordcheck['user_email'];

$smail=mail($emailto, $subject, $message, 
	"From: $name <$emailfrom>\n" .
	"MIME-Version: 1.0\n" .
	"Content-type: text/html; charset=iso-8859-1") .
	header(sprintf('Location: %s', $redirect)); die;	
//END SEND EMAIL
}

if ($totalRows_passwordcheck < 1) {
set_msg('That email address was not found in the database.');
header('Location: password.php'); die;
}
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title><?php echo $pagetitle; ?></title>
<script src="includes/lib/prototype.js" type="text/javascript"></script>
<script src="includes/src/effects.js" type="text/javascript"></script>
<script src="includes/validation.js" type="text/javascript"></script>
<script src="includes/src/scriptaculous.js" type="text/javascript"></script>
<link href="includes/style.css" rel="stylesheet" type="text/css" />
<link href="includes/simplecustomer.css" rel="stylesheet" type="text/css" />
</head>

<body>
<div class="logincontainer">
  <h1><?php echo MINISTRY;?> - Simple Follow-Up</h1>
 <span class="notices" style="display:<?php echo $dis; ?>">
    <?php display_msg(); ?>
  </span>  
  <form id="form1" name="form1" method="post" action="">Enter your email address below and your password will be sent to you immediately. <br />
       <br />
       <input name="email" class="required validate-email" type="text" size="35" title="You must enter your email address." />
       <br />
       <input type="submit" name="Submit" value="Send Password" />
       <a href="password.php"></a>  </p>
     <p>&nbsp;</p>
     <p><a href="login.php">Go back    </a></p>
  </form>
					<script type="text/javascript">
						var valid2 = new Validation('form1', {useTitles:true});
					</script>
</div>
</body>
</html>