<?php
session_start();
include_once 'inc/connect_to_mysql.php';

if(isset($_SESSION['user'])!="")
{
	header("Location: index.php");
}

if(isset($_POST['btn-login']))
{
	$email = mysql_real_escape_string($_POST['email']);
	$upass = mysql_real_escape_string($_POST['pass']);
	$res=mysql_query("SELECT * FROM users WHERE email='$email'");
	$row=mysql_fetch_array($res);
	
	if($row['password']==md5($upass))
	{
		$_SESSION['user'] = $row['user_id'];
		$_SESSION['username'] = $row['username'];
		$_SESSION['password'] = $row['password'];
		header("Location: index.php");
		
	}
	else
	{
		?>
        <script>alert('wrong details');</script>
        <?php
	}
	
}
?>
<!DOCTYPE html>
<html>
<head lang="en">
<meta charset="UTF-8">
<title>Login</title>
<?php require_once('templates/dependancies.php');?>
<link rel="stylesheet" href="css/style3.css" type="text/css" />
<link rel="stylesheet" href="css/style2.css" type="text/css" />

</head>
<body>
<center>
<div id="login-form">
<form method="post">
<h3 style='font:"Kalinga";color#fff;margin-right:200px;'>LOGIN</h3>
<hr style='color:#fff;width:400px;margin-left:80px;'>

<table align="center" width="500px" border="0" style='background-color:transparent;border-style:none;'>
<tr>
<td><span class='glyphicon glyphicon-envelope pull-left' style='font-size:24px; margin-top:18px;margin-left:50px;color:#ff2800;'></span> <input type="text" class='pull-right' name="email" placeholder="Your Email" required/></td>
</tr>
<tr>
<td><span class='glyphicon glyphicon-lock pull-left' style='font-size:24px; margin-top:18px;margin-left:50px;color:#ff2800;'></span><input type="password" name="pass" placeholder="Your Password" required class='pull-right'/></td>
</tr>

<tr>
<td><button type="submit" name="btn-login">Sign In</button>
<div style='margin-top:7px;margin-left:100px;'><a href="register.php">Sign Up Here</a></div>
</td>
</tr>
<tr>

</tr>
</table>
</form>
</div>
</center>
</body>
</html>