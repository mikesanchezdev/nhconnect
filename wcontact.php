<?php require_once('includes/config.php'); ?>
<?php
include('includes/sc-includes.php');
$pagetitle = FollowUp;
$whois = $row_userinfo['user_id'];



$update = 0;


if ($row_userinfo['user_level'] == 3){
$whatgr = $row_userinfo['member_group_id'];
$query_users = "SELECT * FROM follows_group WHERE group_id = $whatgr";
$sys_gr = mysql_query($query_users, $follows) or die("Error getting group name" . mysql_error());
$row_group = mysql_fetch_assoc($sys_gr);

$groupname = $row_group['group_name'];
}



?>
<?php


$editFormAction = $_SERVER['PHP_SELF'];
if (isset($_SERVER['QUERY_STRING'])) {
  $editFormAction .= "?" . htmlentities($_SERVER['QUERY_STRING']);
}

//UPLOAD PICTURE
	$picture = $_POST['image_location'];
	$time = substr(time(),0,5);	
   if($_FILES['image'] && $_FILES['image']['size'] > 0){
	$ori_name = $_FILES['image']['name'];
	$ori_name = $time.$ori_name;
	$tmp_name = $_FILES['image']['tmp_name'];
	$src = imagecreatefromjpeg($tmp_name);
	list($width,$height)=getimagesize($tmp_name);
	$newwidth=95;
	$newheight=($height/$width)*95;
	$tmp=imagecreatetruecolor($newwidth,$newheight);
	imagecopyresampled($tmp,$src,0,0,0,0,$newwidth,$newheight,$width,$height);
	$filename = "images/". $ori_name;
	imagejpeg($tmp,$filename,100);
	$picture = $ori_name;
	imagedestroy($src);
	imagedestroy($tmp);	
}
//END UPLOAD PICTURE

//Do Google Geocode
if ($update == 0) {
if ((isset($_POST["MM_insert"])) && ($_POST["MM_insert"] == "form1")) {
   
   $street=$_POST['contact_street'];
   $city=$_POST['contact_city'];
   $zip=$_POST['contact_zip'];
   $state=$_POST['contact_state'];
   $name=$_POST['contact_first'] . " " . $_POST['contact_last'];
   $fulladdress=$_POST['contact_street'] . ", " . $_POST['contact_city'] . ", " . $_POST['contact_state'] . ", " . $_POST['contact_zip'];
   $mapaddress = urlencode("$street $city $state $zip");
   
   // Desired address
   $address = "http://maps.google.com/maps/geo?q=$mapaddress&output=xml&key=" . GKEY;
    //209.85.173.104
    //64.233.167.147
   // Retrieve the URL contents
//   $page = file_get_contents($address); // This code stopped working on godaddy 7/15/2010 ?? Had to go with fopen

// Parse the returned XML file
//   $xml = new SimpleXMLElement($page);
//
//   // Retrieve the desired XML node
//   $coordinates = $xml->Response->Placemark->Point->coordinates;
//   $coordinatesSplit = split(",", $coordinates);
   // Format: Longitude, Latitude, Altitude


   // $page = fopen($address, "r");
     //   if($page){
       // $data = fread($page, 4096);
       // $xml = new SimpleXMLElement($data);
        // Retrieve the desired XML node
      //  $coordinates = $xml->Response->Placemark->Point->coordinates;
       // $coordinatesSplit = split(",", $coordinates);
       //         }
   // fclose($page);

   $lat = $coordinatesSplit[1];
   $lng = $coordinatesSplit[0];

  $insertSQL = sprintf("INSERT INTO follows_contacts (address, name, contact_first, contact_last, contact_date, contact_user, contact_image, contact_age, contact_profile, contact_method, contact_mstatus, contact_stime, contact_street, contact_unit, contact_city, contact_state, contact_zip, contact_stage, lat, lng, contact_phone, contact_email, contact_updated, contact_wait, follow_date) VALUES (%s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s)",
                       GetSQLValueString($fulladdress, "text"),
					   GetSQLValueString($name, "text"),
					   GetSQLValueString(trim($_POST['contact_first']), "text"),
					   GetSQLValueString(trim($_POST['contact_last']), "text"),
                       GetSQLValueString(time(), "date"),
					   GetSQLValueString(trim($_POST['contact_user']), "text"),
                       GetSQLValueString($picture, "text"),
					   GetSQLValueString(trim($_POST['contact_age']), "int"),
                       GetSQLValueString(trim($_POST['contact_profile']), "text"),
                       GetSQLValueString(trim($_POST['contact_method']), "text"),
                       GetSQLValueString(trim($_POST['contact_mstatus']), "text"),
                       GetSQLValueString(trim($_POST['contact_stime']), "text"),
                       GetSQLValueString(trim($_POST['contact_street']), "text"),
					   GetSQLValueString(trim($_POST['contact_street2']), "text"),
                       GetSQLValueString(trim($_POST['contact_city']), "text"),
                       GetSQLValueString(trim($_POST['contact_state']), "text"),
                       GetSQLValueString(trim($_POST['contact_zip']), "text"),
					   GetSQLValueString(trim($_POST['contact_stage']), "text"),
                       GetSQLValueString($lat, "text"),
                       GetSQLValueString($lng, "text"),
                       GetSQLValueString(trim($_POST['contact_phone']), "text"),
                       GetSQLValueString(trim($_POST['contact_email']), "text"),
                       GetSQLValueString($_POST['contact_updated'], "int"),
					   GetSQLValueString($_POST['contact_wait'], "int"),
					   GetSQLValueString($_POST['date3'], "text"));
					   
	

    mysql_select_db($database_follows, $follows);
    $Result1 = mysql_query($insertSQL, $follows) or die(mysql_error());
    
	$lastid = mysql_insert_id($follows);
							  
	
	$insertSQL4Wait = sprintf("INSERT INTO follows_wait (group_id, contact_id, wait_updated) VALUES (%s, %s, %s)",
                       GetSQLValueString(trim($_POST['wait_group']), "text"),
					   GetSQLValueString($lastid, "text"),
					   GetSQLValueString(time(), "date"));
    
	$Result2 = mysql_query($insertSQL4Wait, $follows) or die(mysql_error());
    set_msg('Follow-Up Waiting');
	$redirect = "waitqueue.php";
	header(sprintf('Location: %s', $redirect)); die;
	
}
}


?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title><?php echo "Add Contact to wait queue"; ?></title>
<script src="includes/lib/prototype.js" type="text/javascript"></script>
<script src="includes/src/effects.js" type="text/javascript"></script>
<script src="includes/validation.js" type="text/javascript"></script>
<script src="includes/src/scriptaculous.js" type="text/javascript"></script>
<script language="javascript">
function toggleLayer(whichLayer)
{
if (document.getElementById)
{
// this is the way the standards work
var style2 = document.getElementById(whichLayer).style;
style2.display = style2.display? "":"block";
}
else if (document.all)
{
// this is the way old msie versions work
var style2 = document.all[whichLayer].style;
style2.display = style2.display? "":"block";
}
else if (document.layers)
{
// this is the way nn4 works
var style2 = document.layers[whichLayer].style;
style2.display = style2.display? "":"block";
}
}
</script>

<link rel="alternate stylesheet" type="text/css" media="all" href="calendar-blue.css" id="defaultTheme" title="winter"  />
<script type="text/javascript" src="calendar.js"></script>

<link href="includes/style.css" rel="stylesheet" type="text/css" />
<link href="includes/simplecustomer.css" rel="stylesheet" type="text/css" />
</head>

<body>
<?php include('includes/header.php'); ?>
<div class="container">
  <div class="leftcolumn">
    <h2>Add FollowUp to Wait Queue</h2>
  
    <p>&nbsp;</p>
    <form action="<?php echo $editFormAction; ?>" method="post" enctype="multipart/form-data" name="form1" id="form1">
      <table width="100%" border="0" cellspacing="0" cellpadding="0">
        <tr>
          <td width="28%">First Name<br />
            <input name="contact_first" type="text" class="required" title="First name required" id="contact_first" value="<?php echo $row_contact['contact_first']; ?>" size="25" /></td>
          <td width="72%">Last Name<br />
                <input name="contact_last" type="text" class="required" title="Last name required" id="contact_last" value="<?php echo $row_contact['contact_last']; ?>" size="25" />
            </p></td>
        </tr>
        <tr>
          <td>Phone/Cell<br /> <input name="contact_phone" type="text" id="contact_phone" value="<?php echo $row_contact['contact_phone']; ?>" size="35" /> </td>
          
        </tr>
        <tr>
          <td>Email <br />
            <input name="contact_email" type="text" class="validate-email" id="contact_email" value="<?php echo $row_contact['contact_email']; ?>" size="35" /></td>
         <td>Contacted via<br />
            <select name="contact_method" id="contact_method" class="validate-selection">
<option value="">Select</option>
                           <?php //This is the method in which the user was contacted or met
	$link1 = "SELECT * FROM category ORDER BY cat ASC";
	$res1 = mysql_query($link1);
	$cur = $row_contact['contact_method'];
	$x = 0;
	while ($row1 = mysql_fetch_row($res1))
	{	
		$cat = $row1[0];
		if ($cat == $cur && $x != 1)
		{
		echo '<option value="' . $cat . '" selected>' . $cat;
		$x = 1;		
		}
		else
		echo '<option value="' . $cat . '">' . $cat;
	}	
	?>
            </select></td>
         </tr>

<tr>
         <td width="27%" valign="top">Marital Status<br />
                          <select name="contact_mstatus" id="contact_mstatus" class="validate-selection">
							<option value="">Select</option>
							<option value="Single"   <?php if (!(strcmp("Single", $row_contact['contact_mstatus'])))   {echo "selected=\"selected\"";} ?>>Single</option>
                            <option value="Married"  <?php if (!(strcmp("Married", $row_contact['contact_mstatus'])))  {echo "selected=\"selected\"";} ?>>Married</option>
                            <option value="Divorced" <?php if (!(strcmp("Divorced", $row_contact['contact_mstatus']))) {echo "selected=\"selected\"";} ?>>Divorced</option>
                            <option value="Unknown"  <?php if (!(strcmp("Unknown", $row_contact['contact_mstatus'])))  {echo "selected=\"selected\"";} ?>>Unknown</option>
                        </select></td>
<td>Service Attended<br />
            <select name="contact_stime" id="contact_stime" class="validate-selection" title="Please select service time">
<option value="">Select</option>
                           <?php //This is the method in which the user was contacted or met
	$link1 = "SELECT * FROM follows_services ORDER BY service_name ASC";
	$res1 = mysql_query($link1);
	$cur = $row_contact['contact_stime'];
	$x = 0;
	while ($row1 = mysql_fetch_row($res1))
	{	
		$cat = $row1[3];
		$cat2 = $row1[1];
		if ($cat == $cur && $x != 1)
		{
		echo '<option value="' . $cat . '" selected>' . $cat;
		$x = 1;		
		}
		else
		echo '<option value="' . $cat . '">' . $cat;
	}	
	?>
            </select></td>                        
                        
         </tr>


        <tr>
          <td colspan="2">
        
            <table  width="100%" border="0" cellspacing="0" cellpadding="0">
              <tr>
                <td>Street<br />
                    <input name="contact_street" type="text" id="contact_street" value="<?php echo $row_contact['contact_street']; ?>" size="35" /></td></tr>
                    <tr><td>Unit/Apt<br />
                    <input name="contact_street2" type="text" id="contact_street2" value="<?php echo $row_contact['contact_unit']; ?>" size="17" /></td></tr>
              
              <tr>
                <td><table width="100%" border="0" cellspacing="0" cellpadding="0">
                    <tr>
                      <td width="39%">City<br />
                          <input name="contact_city" type="text" id="contact_city" value="<?php echo $row_contact['contact_city']; ?>" size="35" /></td>
                      <td width="27%" valign="top">State<br />
                          <select name="contact_state" id="contact_state">
<option value="CA" <?php if (!(strcmp("CA", $row_contact['contact_state']))) {echo "selected=\"selected\"";} ?>>California</option>

                            <option value="CA" <?php if (!(strcmp("CA", $row_contact['contact_state']))) {echo "selected=\"selected\"";} ?>>California</option>

                        </select></td>
                      <td width="34%">Zip<br />
                          <input name="contact_zip" type="text" id="contact_zip" value="<?php echo $row_contact['contact_zip']; ?>" size="10" /></td>
                    </tr>
                </table></td>
              </tr>
              <tr>
                <td><table width="100%" border="0" cellspacing="0" cellpadding="0">
                    <tr>
                      <td width="40%">Wait in Group<br /> 
                        <select name="wait_group" id="wait_group" class="validate-selection" title="You must select a group">
                        <option value="<?php if ($update==1) { echo $row_contact['contact_user']; } else { echo $row_userinfo['user_id']; } ?>">
						<?php if (($update==1) && ($row_contactJ2['user_name'])) { echo "Currently Assigned to: " . $row_contactJ2['user_name']; } else { echo "Select"; } ?>
                        </option>
                        
<?php if ($row_userinfo['user_level'] == 1) {

   $query_userassign = "SELECT group_id, leader_id, group_name FROM follows_group ORDER BY group_name";
   $userassign = mysql_query($query_userassign, $follows);
     if(mysql_num_rows($userassign)) {
	   while($row_userassign = mysql_fetch_row($userassign))
       {
	   print("<option value=\"$row_userassign[0]\">$row_userassign[2]</option>");
       }
     } else {
       print("<option value=\"\">No users created yet</option>");
     } 
	 }
?>   

     <?php if (($row_userinfo['user_level']==3)) { ?>
                     <option value="<?php echo $row_userinfo['member_group_id'];?>"><?php echo $groupname;?></option>
     <?php } ?>
     
</select>
                          </td>
                     
                    </tr>
                </table></td>
              </tr>
              <tr>
                <td>Image<br />
                    <input name="image" type="file" id="image" /><?php if ($row_contact['contact_image']) { ?>
                <br />
                <img src="images/<?php echo $row_contact['contact_image']; ?>" width="95" />
<?php } ?>
</td>
              </tr>
              <tr>
              <td>Age<br /><input name="contact_age" type="text" class="validate-digits" id="contact_age" value="<?php echo $row_contact['contact_age']; ?>" size="1"/></td>
              </tr>
              <tr>
                <td>Add FollowUp Date<br /><input type="text" name="date3" class="validate-date" id="sel3" size="10" value="<?php echo $row_contact['follow_date'];?>">
<input type="reset" value="Date Select"onclick="return showCalendar('sel3', 'mm/dd/y');"></td></tr>

             <tr>
             <td>
             
             FollowUp Stage<br />
            <select name="contact_stage" id="contact_stage" class="validate-selection">
<option value="">Select</option>
                           <?php //This is the followups current stage
	$link1 = "SELECT * FROM follows_stages ORDER BY stage ASC";
	$res1 = mysql_query($link1);
	$cur = $row_contact['contact_stage'];
	$x = 0;
	while ($row1 = mysql_fetch_row($res1))
	{	
		$cat = $row1[0];
		$catn = $row1[1];
		if ($cat == $cur && $x != 1)
		{
		echo '<option value="' . $cat . '" selected>' . $catn;
		$x = 1;		
		}
		else
		echo '<option value="' . $cat . '">' . $catn;
	}	
	?>
            </select>
             
             </td>
             </tr>


                <td>Background/Profile<br />
                    <textarea name="contact_profile" cols="50" rows="3" id="contact_profile"><?php echo $row_contact['contact_profile']; ?></textarea></td>
              </tr>
            </table>  
</div>          
          <p>&nbsp;</p></td>
        </tr>

        <tr>
          <td colspan="2"><p>
            <input type="submit" name="Submit2" value="Add FollowUp" />
            <input name="contact_updated" type="hidden" id="contact_updated" value="<?php echo time(); ?>" />
            <input name="contact_wait" type="hidden" id="contact_wait" value=1 />
            <input type="hidden" name="MM_insert" value="form1" />
            <input name="contact_id" type="hidden" id="contact_id" value="<?php echo $row_contact['contact_id']; ?>" />
            <input name="image_location" type="hidden" id="image_location" value="<?php echo $row_contact['contact_image']; ?>" />
          </p></td>
        </tr>
      </table>
      <p>&nbsp;</p>
      <input type="hidden" name="MM_update" value="form1">
    </form><script type="text/javascript">
						var valid2 = new Validation('form1', {useTitles:true});
	</script>
  </div>
  <?php include('includes/right-column.php'); ?>
  <br clear="all" />
</div>

<?php include('includes/footer.php'); ?>

</body>
</html>
