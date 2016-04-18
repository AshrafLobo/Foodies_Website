<?php
session_start();
if(isset($_SESSION['user'])!="")
{
	header("Location:index.php");
}
include_once 'inc/connect_to_mysql.php';

if(isset($_POST['btn-signup']))
{
	$uname = mysql_real_escape_string($_POST['uname']);
	$email = mysql_real_escape_string($_POST['email']);
	$upass = md5(mysql_real_escape_string($_POST['pass']));
	
	if(mysql_query("INSERT INTO users(username,email,password) VALUES('$uname','$email','$upass')"))
	{
		?>
        <script>alert('successfully registered ');</script>
        <?php
	}
	else
	{
		?>
        <script>alert('error while registering you...');</script>
        <?php
	}
}
?>
<!DOCTYPE html>
<html>
<head lang="en">
<meta charset="UTF-8">
<title>Register</title>
<link rel="stylesheet" href="css/style3.css" type="text/css" />
<link rel="stylesheet" href="css/style2.css" type="text/css" />
<!--Base url-->
</head>
<body>
<div style="width:1366px;margin-top:30px;">

<div style="float:left;width:700px; margin-left:50px;margin-top:30px;">
	<img src='images/titles/terms_and_conditions.png' style="width:700px;">
	<div style='font:"Kalinga";font-size:12pt;color:#fff;background-color:#ff2800;padding:10px;border:2px solid #fff;'>
		
<p>In General

Foodies (owns and operate this Website.  This document governs your relationship with Foodies.</p>
<p> Access to and use of this Website and the products and services available through this Website (collectively, the "Services") 
are subject to the following terms, 
conditions and notices (the "Terms of Service"). By using the Services, you are agreeing to all of the Terms of Service, as may be updated 
by us from time to time. You should check this page regularly to take notice of any changes we may have made to the Terms of Service.</p>


<p>Access to this Website is permitted on a temporary basis, and we reserve the right to withdraw 
or amend the Services without notice. We will not be liable if for any reason this Website is unavailable at any time or for any period. 
From time to time, we may restrict access to some parts or all of this Website.</p>

<p>
This Website may contain links to other websites (the "Linked Sites"), which are 
not operated by [Your Online Store URL]. [Your Online Store URL] has no control over the Linked Sites
 and accepts no responsibility for them or for any loss or damage that may arise from your use of them. 
 Your use of the Linked Sites will be subject to the terms of use and service contained within each such site.</p>

<p>
Privacy Policy

Our privacy policy, which sets out how we will use your information, can be found at foodies.com. 
By using this Website, you consent to the processing described therein and warrant that all data provided by you is accurate.</p>


<p>Prohibitions
You must not misuse this Website. You will not: commit or encourage a criminal offense; transmit or distribute
 a virus, trojan, worm, logic bomb or any other material which is malicious, technologically harmful, in breach of confidence or in any way
 offensive or obscene; hack into any aspect of the Service; corrupt data; cause annoyance to other users; infringe upon the rights of any other person's 
 proprietary rights; send any unsolicited advertising or promotional material, commonly referred to as "spam"; or attempt to affect the performance or 
 functionality of any computer facilities of or accessed through this Website. Breaching this provision would constitute a criminal offense and 
 Foodies will report any such breach to the 
</p>
</div>
</div>
<div  style="float:left;margin-left:75px;margin-top:150px;width:300px;">
<h3 style='font:Kalinga;color:#fff;margin-left:80px;'>REGISTER</h3>
<hr style='300px;color:#fff;margin-left:80px;'>
<form method="post" >
<table width="300px" border="0" style='border-style:none;'>
<tr>
<td><input type="text" name="uname" placeholder="User Name" required style='color:#fff;'/></td>
</tr>
<tr>
<td><input type="email" name="email" placeholder="Your Email" required style='color:#fff;'/></td>
</tr>
<tr>
<td><input type="password" name="pass" placeholder="Your Password" required style='color:#fff;'/></td>
</tr>
<tr>
<td><button type="submit" name="btn-signup">Sign Me Up</button></td>
</tr>
<tr>
<td><a href="login.php">Sign In Here</a></td>
</tr>
</table>
</form>
</div>
</div>
</body>
</html>