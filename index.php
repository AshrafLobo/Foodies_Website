<?php require_once('inc/connect.php');
require_once('inc/connect_to_mysql.php');
error_reporting(E_ERROR |  E_PARSE);
session_start();
if (!isset($_SESSION["user"])) {
 
}
else{
// Be sure to check that this manager SESSION value is in fact in the database
$adminID = preg_replace('#[^0-9]#i', '', $_SESSION["user"]); // filter everything but numbers and letters
$admin_name = preg_replace('#[^A-Za-z0-9]#i', '', $_SESSION["username"]); // filter everything but numbers and letters
$password = preg_replace('#[^A-Za-z0-9]#i', '', $_SESSION["password"]); // filter everything but numbers and letters
// Run mySQL query to be sure that this person is an admin and that their password session var equals the database information
// Connect to the MySQL database  
$sql2 = $db->query("SELECT * FROM orderdetails WHERE userid='$adminID' AND processed='unprocessed'"); // query the person

foreach ($sql2 as $cartadd)
{
	$pid = $cartadd['productid'];
		$wasFound = false;
	$i = 0;
	// If the cart session variable is not set or cart array is empty
	if (!isset($_SESSION["cart_array"]) || count($_SESSION["cart_array"]) < 1) { 
	    // RUN IF THE CART IS EMPTY OR NOT SET
		$_SESSION["cart_array"] = array(0 => array("item_id" => $pid, "quantity" => 1));
	} else {
		// RUN IF THE CART HAS AT LEAST ONE ITEM IN IT
		foreach ($_SESSION["cart_array"] as $each_item) { 
		      $i++;
		      while (list($key, $value) = each($each_item)) {
				  if ($key == "item_id" && $value == $pid) {
					  // That item is in cart already so let's adjust its quantity using array_splice()
					  array_splice($_SESSION["cart_array"], $i-1, 1, array(array("item_id" => $pid, "quantity" => $each_item['quantity'] + 1)));
					  $wasFound = true;
				  } // close if condition
		      } // close while loop
	       } // close foreach loop
		   if ($wasFound == false) {
			   array_push($_SESSION["cart_array"], array("item_id" => $pid, "quantity" => 1));
		   }
	}
}
}
?>

<?php 
$sql = $db->query("SELECT * FROM products ORDER BY timesOrdered DESC LIMIT 6"); // you can also use prepared statement.
// displaying categories
?> 

<?php
$emailList="";
if (isset($_POST['email']))
{
	$emailVar = mysql_real_escape_string($_POST['email']);
	$sqlQ = mysql_query("SELECT * FROM users WHERE email = '$emailVar'");
	$productMatch = mysql_num_rows($sqlQ); // count the output amount
	
	if ($productMatch = 0) 
	{
		$emailList = "<p style='width:400px;background-color:#ff2800;color:#fff;'> PLEASE REGISTER FIRST</p>";
	}
	else
	{
		$emailList = "<p style='width:400px;background-color:#ff2800;color:#fff;'> YOU ARE NOW SUBSCRIBED </p>";
		$sqlQuery = mysql_query("UPDATE users SET mailinglist='YES' WHERE email = '$email'");
	}
}
else
{$emailList = "<p style='width:400px;background-color:#ff2800;color:#fff;'> YOU NEED TO ENTER YOUR EMAIL ADDRESS</p>";}
?>

<!DOCTYPE html>
<html>
<head lang="en">
    <meta charset="UTF-8"> 
		<meta name="viewport" content="width=device-width, initial-scale=1.0"> 
		<title>Foodies Homepage</title>
		<?php require_once('templates/dependancies.php'); ?>
		<link rel="stylesheet" type="text/css" href="css/style.css"/>
		<link rel="stylesheet" type="text/css" href="css/style2.css"/>

	</head>
	<body>
		<div class="containerWrapper">

			<?php require_once('templates/header.php');?>
			<div id="cbp-fbscroller" class="cbp-fbscroller">
				<nav>
					<a href="#fbsection1" class="cbp-fbcurrent">Section 1</a>
					<a href="#fbsection2">Section 2</a>
					<a href="#fbsection3">Section 3</a>
				</nav>
				<section id="fbsection1">
				<div style='width:1000px; margin:0 auto;'>
					<img src='images/logos/logo.png' id='logo' style='width:500px; margin-top:50px; margin-left:250px;'>
					<div id='our story'>
						<img src='images/titles/our_story.png' style='width:600px; margin:0px 200px;'>
						
						<div style='text-align:center;font-size:12pt;'>
							<p>Foodies is a restaurant located in Nairobi town.</p>
							<p>We started in 2015 and after a short amount of time, had already</p>
							<p>received stunning reviews.</p> 
							<p>For a once in a life time experience, try our restaurant.</p>
							<p>You won't be disappointed.</p>						
						</div>
						
					</div>
				</div>
				</section>
				<section id="fbsection2">
					<div id='bestSellers' style='margin:0 auto;'>
						
						<div id="rowWrapper" class="row">
							<div class="col-sm-12 col-md-12 col-lg-12">   
								<img src='images/titles/best_sellers.png' style='margin-top:22px;width:900px;'>  
							</div>  

							<div class="clearfix visible-sm-block clearfix visible-md-block clearfix visible-lg-block clearfix"></div> 
							
							<div id="rowWrapperWithoutHeader">
							<?php foreach ($sql as $category):?>
							
							<div id='row' class='col-sm-12 col-md-6 col-lg-4'>

								<div>
									<a href='product.php?id=<?php echo $category['id']?>'><img src='media/products/<?php echo $category['image'] ?>' id='img' width='300' height='225'></a>
									<a href='product.php?id=<?php echo $category['id']?>'><button type='button' id='moreButton' class='clickr'><span class='glyphicon glyphicon-plus-sign'></span></button></a>
								</div>

							</div>

							<div class="clearfix visible-sm-block"></div> 
							<?php endforeach; ?>

							</div>
								
						</div>
						
					</div>
				</section>
				<section id="fbsection3">
					<div id="followUsWrapper">

						<div id="follow" class="col-sm-12 col-md-12 col-lg-12" >
							<img src="images/titles/follow_us.png" style='width:900px;margin-top:22px;'>	
						</div>

						<div id="socialMediaWrapper">
							<div id="facebook" class="pull-left">FACEBOOK</div>
							<div id="twitter" class="pull-left">TWITTER</div>   
							<div id="google" class="pull-left">GOOGLE+</div>             
						</div>

						<div id="follow" class="col-sm-12 col-md-12 col-lg-12" style='width:600px; margin-top:100px;margin-left:150px; '>
							<img src="images/titles/join_our_mailing_list.png" style='width:600px;'>	
						</div>
						
						<div style='width:600px; position:absolute;top:300px;left:250px;'>
						<form name='mailingForm' action='index.php' method='post' style='width:400px;height:40px;border:2px solid #fff; padding:2.5px;'>
							<input type='email' name='email' id='email' placeholder='  JOIN US' class='pull-left'>
							<button type='submit' id='mailingListButton' class='pull-right'><span class='glyphicon glyphicon-envelope'></span></button>						
						</form>
						
						<div style='position:absolute;margin:20px auto;left:50px;'>
							<p> Enter your email addres above to receive emails</p>
							<p> about our products and promotions.</p>
						</div>
						
						</div>
										
					</div>
				</section>
			</div>

		</div>
		
		<footer>
			<?php require_once('templates/footer.php');?>
		</footer>
		<script src="js/jquery.min.js"></script>
		<!-- jquery.easing by http://gsgd.co.uk/ : http://gsgd.co.uk/sandbox/jquery/easing/ -->
		<script src="js/jquery.easing.min.js"></script>
		<!-- waypoints jQuery plugin by http://imakewebthings.com/ : http://imakewebthings.com/jquery-waypoints/ -->
		<script src="js/waypoints.min.js"></script>
		<!-- jquery-smartresize by @louis_remi : https://github.com/louisremi/jquery-smartresize -->
		<script src="js/jquery.debouncedresize.js"></script>
		<script src="js/cbpFixedScrollLayout.min.js"></script>
		<script>
			$(function() {
				cbpFixedScrollLayout.init();
			});
		</script>
	</body>
</html>
