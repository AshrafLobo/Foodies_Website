<?php 
session_start();
error_reporting(E_ERROR |  E_PARSE);
if (!isset($_SESSION["user"])) {
    header("location: login.php"); 
    exit();
}
// Be sure to check that this manager SESSION value is in fact in the database
$adminID = preg_replace('#[^0-9]#i', '', $_SESSION["user"]); // filter everything but numbers and letters
$admin_name = preg_replace('#[^A-Za-z0-9]#i', '', $_SESSION["username"]); // filter everything but numbers and letters
$password = preg_replace('#[^A-Za-z0-9]#i', '', $_SESSION["password"]); // filter everything but numbers and letters
// Run mySQL query to be sure that this person is an admin and that their password session var equals the database information
// Connect to the MySQL database  
include_once 'inc/connect_to_mysql.php';
$sql = mysql_query("SELECT * FROM users WHERE user_id='$adminID' AND username='$admin_name' AND password='$password' LIMIT 1"); // query the person
$userRow=mysql_fetch_array($sql);
// ------- MAKE SURE PERSON EXISTS IN DATABASE ---------
$existCount = mysql_num_rows($sql); // count the row nums
if ($existCount == 0) { // evaluate the count
	 echo "Your login session data is not on record in the database.";
     exit();
}
?>
<?php require_once('inc/connect_to_mysql.php'); ?>
<?php 
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
//       Section 1 (if user attempts to add something to the cart from the product page)
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
if (isset($_POST['pid'])) {
    $pid = $_POST['pid'];
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
	header("location:cart.php"); 
    exit();
}
?>
<?php 
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
//       Section 2 (if user chooses to empty their shopping cart)
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
if (isset($_GET['cmd']) && $_GET['cmd'] == "emptycart") {
    unset($_SESSION["cart_array"]);
}
?>
<?php 
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
//       Section 3 (if user chooses to adjust item quantity)
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
if (isset($_POST['item_to_adjust']) && $_POST['item_to_adjust'] != "") {
    // execute some code
	$item_to_adjust = $_POST['item_to_adjust'];
	$quantity = $_POST['quantity'];
	$quantity = preg_replace('#[^0-9]#i', '', $quantity); // filter everything but numbers
	if ($quantity >= 100) { $quantity = 99; }
	if ($quantity < 1) { $quantity = 1; }
	if ($quantity == "") { $quantity = 1; }
	$i = 0;
	foreach ($_SESSION["cart_array"] as $each_item) { 
		      $i++;
		      while (list($key, $value) = each($each_item)) {
				  if ($key == "item_id" && $value == $item_to_adjust) {
					  // That item is in cart already so let's adjust its quantity using array_splice()
					  array_splice($_SESSION["cart_array"], $i-1, 1, array(array("item_id" => $item_to_adjust, "quantity" => $quantity)));
				  } // close if condition
		      } // close while loop
	} // close foreach loop
}
?>
<?php 
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
//       Section 4 (if user wants to remove an item from cart)
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
if (isset($_POST['index_to_remove']) && $_POST['index_to_remove'] != "") {
    // Access the array and run code to remove that array index
 	$key_to_remove = $_POST['index_to_remove'];
	if (count($_SESSION["cart_array"]) <= 1) {
		unset($_SESSION["cart_array"]);
	} else {
		unset($_SESSION["cart_array"]["$key_to_remove"]);
		sort($_SESSION["cart_array"]);
	}
}
?>
<?php 
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
//       Section 5  (render the cart for the user to view on the page)
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
$cartOutput = "";
$cartTotal = "";
$pp_checkout_btn = '';
$product_id_array = '';
if (!isset($_SESSION["cart_array"]) || count($_SESSION["cart_array"]) < 1) {
    $cartOutput = "<h2 align='center'>Your shopping cart is empty</h2>";
} else {
	// Start the For Each loop
	$i = 0; 
    foreach ($_SESSION["cart_array"] as $each_item) { 
		$item_id = $each_item['item_id'];
		$sql = mysql_query("SELECT * FROM products WHERE id='$item_id' LIMIT 1");
		while ($row = mysql_fetch_array($sql)) {
			$product_name = $row["name"];
			$price = $row["price"];
			$details = $row["description"];
			$image = $row["image"];
		}
		$qnty = $each_item['quantity'];
		$pricetotal = $price * $each_item['quantity'];
		$cartTotal = $pricetotal + $cartTotal;
		// Dynamic Checkout Btn Assembly
		$x = $i + 1;
		$pp_checkout_btn .= '<input type="hidden" name="item_name_' . $x . '" value="' . $product_name . '">
        <input type="hidden" name="amount_' . $x . '" value="' . $price . '">
        <input type="hidden" name="quantity_' . $x . '" value="' . $each_item['quantity'] . '">  ';
		// Create the product array variable
		$product_id_array .= "$item_id-".$each_item['quantity'].","; 
		// Dynamic table row assembly
		$cartOutput .= "<tr>";
		$cartOutput .= '<td><a href="product.php?id=' . $item_id . '">' . $product_name . '</a><br /><img src="media/products/' .$image .'" width="150px" height="112px" alt="' . $product_name. '" width="40" height="52" border="1" /></td>';
		$cartOutput .= '<td style="font-size:10pt;">' . $details . '</td>';
		$cartOutput .= '<td>' . $price . 'KSH</td>';
		$cartOutput .= '<td><form action="cart.php" method="post">
		<input name="quantity" type="text" value="' . $each_item['quantity'] . '" size="1" maxlength="2"  class="pull-left"/>
		<button class="pull-right" id="adjustBtn" name="adjustBtn' . $item_id . '" type="submit"  />Change</button>
		<input name="item_to_adjust" type="hidden" value="' . $item_id . '" />
		</form></td>';
		//$cartOutput .= '<td>' . $each_item['quantity'] . '</td>';
		$cartOutput .= '<td>' . $pricetotal . '</td>';
		$cartOutput .= '<td><form action="cart.php" method="post"><button id="deleteBtn" name="deleteBtn' . $item_id . '" type="submit"><span class="glyphicon glyphicon-remove"></span></button><input name="index_to_remove" type="hidden" value="' . $i . '" /></form></td>';
		$cartOutput .= '</tr>';
		$i++;
		
		if (!isset($_SESSION["user"])) {
		header("location: login.php"); 
		exit();
		}
		// Be sure to check that this manager SESSION value is in fact in the database
		$adminID = preg_replace('#[^0-9]#i', '', $_SESSION["user"]); // filter everything but numbers and letters
		$datenow = date("Y-m-d");
		$sqlQ = mysql_query("SELECT * FROM orderdetails WHERE productid='$item_id' AND userid='$adminID'");
		$productMatch = mysql_num_rows($sqlQ); // count the output amount
		
		if ($productMatch > 0) 
		{

		}
		else{
		// Add this product into the database now
		$sqlQuery ="INSERT INTO orderdetails (userid, price, quantity, productid, productname, date) VALUES('$adminID','$pricetotal',$qnty,'$item_id','$product_name','$datenow')";
		mysql_query($sqlQuery);}
    } 
	$prc = $cartTotal;
	$cartTotal = "<div style='font-size:15pt; float:right; width:250px; margin:12px auto;color:#fff;border:2px solid #fff; padding:10px;' text-align:center;>Cart Total : ".$cartTotal." KSH</div>";
    // Finish the Paypal Checkout Btn
	/*<input type="hidden" name="notify_url" value="https://www.yoursite.com/storescripts/my_ipn.php">
	<input type="hidden" name="return" value="https://www.yoursite.com/checkout_complete.php">
	<input type="hidden" name="rm" value="2">
	<input type="hidden" name="cbt" value="Return to The Store">
	<input type="hidden" name="cancel_return" value="https://www.yoursite.com/paypal_cancel.php">
	<input type="hidden" name="lc" value="US">
	<input type="hidden" name="currency_code" value="USD">
	<input type="image" src="http://www.paypal.com/en_US/i/btn/x-click-but01.gif" name="submit" alt="Make payments with PayPal - its fast, free and secure!">
	</form>';*/
	
	$product_name = mysql_real_escape_string($_POST['product_name']);
	$price = mysql_real_escape_string($_POST['price']);
	$category = mysql_real_escape_string($_POST['category']);
	$details = mysql_real_escape_string($_POST['details']);
	$image = mysql_real_escape_string($_POST['image']);
		
}
?>

<?php
$dynamicList="";
if (isset($_POST['contactdetails']))
{
$dynamicList = '
<p>THANK YOU FOR YOUR PURCHASE</p>
<p>YOUR ORDER IS BEING PROCESSED AND WILL ARRIVE WITHIN THE HOUR</p>
';

	$user_name = mysql_real_escape_string($_POST['username']);
	$locationDel = mysql_real_escape_string($_POST['location']);
	$latDel = mysql_real_escape_string($_POST['lat']);
	$lonDel = mysql_real_escape_string($_POST['lon']);
	$contactdetails = mysql_real_escape_string($_POST['contactdetails']);
	$userid = mysql_real_escape_string($_POST['userid']);
	$tot = mysql_real_escape_string($_POST['pricedetails']);
	$now = date("Y-m-d");
	
	$sqlQuery2 = mysql_query("INSERT INTO `orders` (`userid`, `username`, `location`, `lat`, `lon`, `contacts`, `total`, `date`) VALUES ( '$userid', '$user_name', '$locationDel', '$latDel', '$lonDel', '$contactdetails', '$tot', '$now') ");
	
	$sqlQuery3 = mysql_query("UPDATE orderdetails SET processed='processed' WHERE userid = '$userid'");
	unset($_SESSION["cart_array"]);
}
else
{$dynamicList ='

';}
?>

<!DOCTYPE html>
<html>
<head lang="en">
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Your Cart</title>
<link rel="stylesheet" href="css/style2.css" type="text/css" media="screen" />

<?php require_once('templates/dependancies.php');?>
<style>
table
{
	margin:0 auto;
	border-style:none;
	width:800px;
}

table tr td
{
	border-style:none;
	text-align:center;
	font:'Kalinga';
	color:#fff;
	font-size:12pt;
}

table tr
{
	border-bottom:2px solid #fff;
	min-height:250px;
}

table td
{
	padding:2.5px;
}

table tr:first-child
{
	border-top:2px solid #fff;
	min-height:0px;
}

#ourLocation input,#ourCart input
{
	color:#fff;
	font:'Kalinga';
	font-size:10pt;
	background-color:transparent;
	border:2px solid #fff;
	padding:2.5px;
	text-align:center;
}

#adjustBtn
{
	border:2px solid #fff;
	background-color:transparent;
	padding:2.5px;
	width:70px;
	height:30px;
}

#adjustBtn:hover
{
	border-color:#ff2800;
	background-color:#ff2800;
}

#deleteBtn
{
	border-style:none;
	background-color:transparent;
	font-size:14pt;
}

#navigation a,#emptyCart
{   padding:5px 10px;
	height:30px;
	text-align:center;
	text-decoration:none;
	border-color:#ff2800;
	background-color:#ff2800;
	color:#fff;
	font:'Kalinga';
	font-size:10pt;
}

#navigation a:hover,#emptyCart:hover
{
	border: 2px solid #fff;
	background-color:transparent;
	color:#fff;
}

#icon_nav li:first-child
{
	margin-left:73px;
}

#icon_nav li:last-child
{
	margin-left:725px;
}

#icon_nav li
{
	position:absolute;
	margin-left:398px;
	top:24px;
	list-style-type:none;
	font-size:22px;
}

#icon_nav .glyphicon-shopping-cart
{
	font-size:22px;
	color:#fff;
}

#icon_nav a:hover
{
	text-decoration:none;
	color:#555454;
}

#ourLocation, #ourCheckout
{
	visibility:hidden;
}

#ourCheckout input
{
	width:500px;
	font:Kalinga;
	font-size:10pt;
	text-align:center;
	background-color:transparent;
	border:2px solid #fff;
	border-radius:5px;
}

#checkoutform div
{
	margin-bottom:10px;
}

a:hover
{
	text-decoration:none;
	color:#ff2800;
}

#checkoutWrapper button
{
	background-color:transparent;
	width:100%;
	border-style:none;
	text-align:center;
	height:30px;
	border:2px solid #fff;
}

#checkoutWrapper button:hover
{
	background-color:#ff2800;
	border-color:#ff2800;
}

</style>
</head>
<body>
<div align="center" id="mainWrapper">
<?php require_once('templates/header.php')?>
  <div id="pageContent" style='margin-top:40px;display:inline-block;'>
  
    <div style="margin:0px auto; width:900px;">
	
	
	<div style='width:900px;position:relative;margin-bottom:20px;'>
		<img src='images/titles/checkout1.png' style='width:700px; text-align:left;' id='checkout_image'>
		<ul id='icon_nav'>
			<li id='cartLi'><a href='#' onclick='displayCart()' class='glyphicon glyphicon-shopping-cart pull-left'></a></li>
			<li id='locationLi'><a href='#' onclick='displayLocation()' class='glyphicon glyphicon-map-marker pull-left'></a></li>
			<li id='checkoutLi'><a href='#' onclick='displayCheckout()' class='glyphicon glyphicon-ok pull-left'></a></li>
		</ul>
	</div>
	
    <br />
	<div> <?php echo $dynamicList; ?> </div>
	<div id='ourCart'>
	
    <table border="1" cellspacing="0" cellpadding="20">
      <tr>
        <td width="15%"><strong>Product</strong></td>
        <td width="40%"><strong>Food Description</strong></td>
        <td width="15%"><strong>Unit Price</strong></td>
        <td width="20%" ><strong>Quantity</strong></td>
        <td width="9%" ><strong>Total</strong></td>
        <td width="9%" ><strong>Remove</strong></td>
      </tr>
     <?php echo $cartOutput; ?>
     <!-- <tr>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
      </tr> -->
    </table>
    <?php echo $cartTotal; ?>
	<br style='clear:both;'/>
	<br/>
	<a href="cart.php?cmd=emptycart" id='emptyCart' class='pull-left'>EMPTY YOUR CART</a>
		<div style='width:200px;' class='pull-right' id='navigation'>
			<a href="#" id='next' onclick='displayLocation()'>NEXT</a>
		</div>
	</div>
		
	<div class="form-horizontal" style="width:700px; height:400px;font:Kalinga;color#fff;font-size:10pt; margin:0 auto;" id='ourLocation'>
		<h2>PICK YOUR LOCATION</h2>
		<hr style="width:100%; color:#fff;margin-top:10px;">
		<div class="form-group" style="width:500px;float:left;">
			<label class="col-sm-2 control-label pull-left" id="locationLabel" style="margin-right:10px;">Location:</label>
			<div class="col-sm-10 pull-left"><input type="text" class="form-control" id="us3-address" style="background-color:transparent;"/></div>
		</div>
		<div id="us3" style="width: 700px; height: 250px;"></div>
		<div class="clearfix">&nbsp;</div>
		<div class="m-t-small">
			<label class="p-r-small col-sm-1 control-label pull-left">Lat.:</label>

			<div class="col-sm-3 pull-left"><input type="text" class="form-control" style="width: 150px;background-color:transparent;" id="us3-lat"/></div>
			<label class="p-r-small col-sm-2 control-label pull-left">Long.:</label>

			<div class="col-sm-3 pull-left"><input type="text" class="form-control" style="width: 150px;background-color:transparent;" id="us3-lon"/></div>
		</div>
		
		<div style='width:140px;' class='pull-right' id='navigation'>
			<a href="#" id='previous' class='pull-left' onclick='displayCart()'>BACK</a>
			<a href="#" id='next' class='pull-right' onclick='displayCheckout()'>NEXT</a>
		</div>
		
		<div class="clearfix"></div>
		<script>
		$('#us3').locationpicker({
			location: {latitude: -1.3, longitude: 36.8},
			radius: 50,
			inputBinding: {
				latitudeInput: $('#us3-lat'),
				longitudeInput: $('#us3-lon'),
				radiusInput: $('#us3-radius'),
				locationNameInput: $('#us3-address')
			},
			enableAutocomplete: true,
			onchanged: function (currentLocation, radius, isMarkerDropped) {
				// Uncomment line below to show alert on each Location Changed event
				//alert("Location changed. New location (" + currentLocation.latitude + ", " + currentLocation.longitude + ")");
			}
		});</script>
	</div>
	
	<div id='ourCheckout' style="width:700px;">
		<div id="confirm">
			<h2>CONFIRMATION YOUR PURCHASE DETAILS</h2>
			<hr style="width:100%;color:#fff;margin-top:10px;">
			<form id='checkoutform' action="cart.php" method="post">
				<div><input type="text" name="username" value="<?php echo $admin_name; ?>" id="usernameCheckout"/></div>
				<div><input type="text" name="location" id="locationCheckout"/></div>
				<div><input type="text" name="contactdetails" placeholder="ENTER CONTACT DETAILS"/></div>
				<div><input type="text" name="pricedetails" value="<?php echo $prc;?>"/></div>
				<div><input type="hidden" name="userid" value="<?php echo $adminID;?>"/></div>
				<div><input type="hidden" name="lat" id="latCheckout" /></div>
				<div><input type="hidden" name="lon" id="lonCheckout"/></div>
				<div id="checkoutWrapper" style='width:150px;'><button type="submit" name="checkout" id="checkoutButton" style="font:Kalinga;color:#fff;margin:0 auto;" onclick='displaySuccessful'>CHECKOUT</button></div>
			</form>
			<div style='width:200px;' class='pull-right' id='navigation'>
				<a href="#" id='previous' onclick='displayLocation()'>BACK</a>
			</div>
		</div>		
	</div>		
    </div>
   <br />
  </div>

</div>
<script>
$('#somecomponent').locationpicker();
</script>

<script>
	function displayCart() {
		var ix = $("#pageContent").index();
		
		$('#ourCart').toggle( ix === 1 );
		$('#ourLocation').toggle( ix === 0 );
		$('#ourCheckout').toggle( ix === 0 );
		
		var image = document.getElementById('checkout_image');
		image.src='images/titles/checkout1.png';
		
	};

	function displayLocation() {
		document.getElementById('ourLocation').style.visibility = "visible";
		var ix = $("#pageContent").index();
		
		$('#ourCart').toggle( ix === 0 );
		$('#ourLocation').toggle( ix === 1 );
		$('#ourCheckout').toggle( ix === 0 );	
		
		var image = document.getElementById('checkout_image');
		image.src='images/titles/checkout2.png';	
	};
	
	function displayCheckout() {
		document.getElementById('ourCheckout').style.visibility = "visible";
		var ix = $("#pageContent").index();
		
		$('#ourCart').toggle( ix === 0 );
		$('#ourLocation').toggle( ix === 0 );
		$('#ourCheckout').toggle( ix === 1 );
		
		var locationVar = document.getElementById('us3-address').value;
		var latVar = document.getElementById('us3-lat').value;
		var lonVar = document.getElementById('us3-lon').value;
		
		document.getElementById('locationCheckout').value = locationVar;
		document.getElementById('latCheckout').value = latVar;
		document.getElementById('lonCheckout').value = lonVar;
		
		var image = document.getElementById('checkout_image');
		image.src='images/titles/checkout3.png';
	};	
	
	function displaySuccessful() {
	
		document.getElementById('successful').style.display = "block";
			
	};
</script>
</body>
</html>