<?php 
session_start();
error_reporting(E_ERROR |  E_PARSE);
if (!isset($_SESSION["user"])) {
    header("location: ../login.php"); 
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

<?php 
// This block grabs the whole list for viewing
$product_list1 = "";

$page=$_GET["page"];

if($page=="" || $page=="1")
{
	$page1=0;
}

else
{
	$page1=($page*8)-8;
}

$sql = mysql_query("SELECT * FROM orders WHERE delivered='NO' ORDER BY date ASC LIMIT $page1,8");
$sql2 = mysql_query("SELECT * FROM orders WHERE delivered='NO' ORDER BY date ASC");

$cou1 = mysql_num_rows($sql2);
$productCount1 = mysql_num_rows($sql); // count the output amount
if ($productCount1 > 0) {
while($row = mysql_fetch_array($sql))
{ 
	$id = $row["orderID"];
	$uid = $row["userid"];
	$usern= $row["username"];
	$loc = $row["location"];
	$lat = $row["lat"];	
	$lon = $row["lon"];
	$contacts = $row["contacts"];
	$total = $row["total"];

	$date_added = strftime("%b %d, %Y", strtotime($row["date"]));
	$product_list1 .= "
      <tr>
		<td align='left'>$usern</td>
		<td align='left'>$loc</td>
		<td align='left'>$lat</td>
		<td align='left'>$lon</td>
		<td align='left'>$contacts</td>	
		<td align='left'>$total</td>	
		<td align='left'>$date_added</td>		
		<td align='left' style='font-size:12pt;'><a href='order.php?okid=$id' class='glyphicon glyphicon-ok' ></a> <br /></td>
      </tr>
";
}

} 
else 
{
	$product_list1 = "You have no new orders";
}

$a1=$cou1/8;
$a1=ceil($a1);

$pg=$_GET["pg"];

if($pg=="" || $pg=="1")
{
	$pg1=0;
}

else
{
	$pg1=($pg*8)-8;
}

$sqlQ1 = mysql_query("SELECT * FROM orderdetails WHERE userid='$uid' ORDER BY date ASC LIMIT $pg1,8");
$sqlQ = mysql_query("SELECT * FROM orderdetails WHERE userid='$uid' ORDER BY date ASC");
$cou2 = mysql_num_rows($sqlQ);
$productCnt = mysql_num_rows($sqlQ); // count the output amount
if ($productCnt > 0) {
while($row = mysql_fetch_array($sqlQ1))
{ 
	$id = $row["productid"];
	$pname= $row["productname"];
	$qnty= $row["quantity"];
	$prc = $row["price"];

	$product_lst .= "
      <tr>
		<td align='left'>$pname</td>
		<td align='left'>$qnty</td>
		<td align='left'>$prc</td>		
      </tr>
";
}

} 
else 
{
	$product_lst = "You have no new orders";
}
$a2=$cou2/8;
$a2=ceil($a2);
?>

<?php 
$sql4 = mysql_query("SELECT * FROM orders WHERE delivered='YES'");
$productCount2 = mysql_num_rows($sql4); // count the output amount
?>

<?php
if (isset($_GET['okid'])) 
{
	$okid =$_GET['okid'];
	$sqlQ2 = mysql_query("UPDATE orders SET delivered='YES' WHERE orderID = '$okid'") or die (mysql_error());
}
?>
<!DOCTYPE html>
<html lang="en" class="no-js">
	<head>
		<meta charset="UTF-8" />
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<title>FOODIES ADMIN</title>
		
		<?php require_once('templates/dependancies.php');?>
		<style>

.menu__inner
{
	font-size:15px;
}

.menu__inner a:active span
{
	color:#fff;
}

.menu__inner a:hover
{
	text-decoration:none;
}

#userLi
{
	width:150px;
	margin:0 auto;
	border-bottom:2px solid #ff2800;
	height:30px;
}

#userLi i
{
	width:20px;
}

.codrops-header
{
	width:900px;
	margin:0 auto;
}

#addInventory
{
	margin-right:32px;
	font-size:10pt;
	font:Kalinga;
}

#button
{
	background-color:transparent;
	padding:5px;
	color:#fff;
	border-style:none;
}

#buttonLabel
{
	border:2px #fff solid;
	width:200px;
	height:30px;
	float:right;
}

#buttonLabel:hover
{
	background-color:#ff2800;
	border-style:none;
}

#myform input, #myform select, #myform textarea
{
	color:#000;
	font:'Kalinga';
	font-size:15px;
}

a:hover
{
	text-decoration:none;
}
</style>
	</head>
	<body class="theme-1">
		<div class="container">
			<nav id="menu" class="menu">
				<button class="menu__handle"><span>Menu</span></button>
				<?php require_once("templates/nav.php");?>
				<div class="morph-shape" data-morph-open="M300-10c0,0,295,164,295,410c0,232-295,410-295,410" data-morph-close="M300-10C300-10,5,154,5,400c0,232,295,410,295,410">
					<svg width="100%" height="100%" viewBox="0 0 600 800" preserveAspectRatio="none">
						<path fill="none" d="M300-10c0,0,0,164,0,410c0,232,0,410,0,410"/>
					</svg>
				</div>
			</nav>
			<div class="main">
				<header class="codrops-header" style='width:900px;'>
					<div style='800px; margin-left:50px;'>
					<a href="order.php" onclick='displayNewMessages()'>
						<div style='float:left;width:300px;height:100px;margin-right:100px;text-align:center;background-color:#ff2800;padding:5px;position:relative;'>
							<span class='fa fa-fw fa-shopping-cart' style='font-size:28px;text-align:center;margin-top:25px;'></span>
							<div>NEW ORDERS</div>
							<span class='badge' style='position:absolute;top:10px;right:20px;'><?php echo $productCount1; ?></span>
						</div>
					</a>
					<a href="delivered.php" >					
						<div style='float:right;width:300px;height:100px;margin-right:100px;text-align:center;background-color:#ff2800;padding:5px;position:relative;'>
							<span class='fa fa-fw fa-truck' style='font-size:28px;text-align:center;margin-top:25px;'></span>
							<div>DELIVERED ORDERS</div>
							<span class='badge' style='position:absolute;top:10px;right:20px;color:#fff;font:Kalinga;'><?php echo $productCount2; ?></span>
						</div>
					</a>
					</div>
					
					<br>
					<hr style='width:800px;color:#fff;margin:0 auto;'>
					<br>
				
					<div style='font-size:15px;font:Kalinga;width:900px;margin:0 auto;' id='newMessages'>
					<h3 class='pull-left'>NEW ORDERS-DELIVERY DETAILS</h3>
						<table class='table'>
							<thead>
							  <tr>
								<th>Username</th>
								<th>Location</th>
								<th>Latitude</th>
								<th>Longitude</th>
								<th>Contacts</th>
								<th>Amount Due</th>
								<th>Date</th>
								<th></th>		
							  </tr>
							</thead>
							<tbody>
								<?php echo $product_list1; ?>							
							</tbody>
						</table>
						<?php for($b=1;$b<=$a1;$b++){ ?>
							<span style="border:2px #000 solid; color:#ff2800; text-align:center; margin:0 2.5px;padding:5px;"><a href="order.php?page=<?php echo $b;?>" style="color:#ff2800;"><?php echo $b." ";?></a></span>
						<?php } ?>						
					</div>
					
					<br>
					<hr style='width:800px;color:#fff;margin:0 auto;'>
					<br>
				
					<div style='font-size:15px;font:Kalinga;width:900px;margin:0 auto;' id='newOrders'>
					<h3 class='pull-left'>NEW ORDERS-ORDER DETAILS</h3>
						<table class='table'>
							<thead>
							  <tr>
								<th>Product Name</th>
								<th>Quantity</th>
								<th>Price</th>		
							  </tr>
							</thead>
							<tbody>
								<?php echo $product_lst; ?>							
							</tbody>
						</table>
						<?php for($b1=1;$b1<=$a2;$b1++){ ?>
							<span style="border:2px #000 solid; color:#ff2800;text-align:center; margin:0 2.5px;padding:5px;"><a href="order.php?pg=<?php echo $b1;?>" style="color:#ff2800;"><?php echo $b1." ";?></a></span>
						<?php } ?>						
					</div>
				</header>
				<!-- Related demos -->
				<section class="related">

				</section>
			</div><!-- /main -->
		</div><!-- /container -->
		<script src="js/classie.js"></script>
		<script>
			(function() {

				function SVGMenu( el, options ) {
					this.el = el;
					this.init();
				}

				SVGMenu.prototype.init = function() {
					this.trigger = this.el.querySelector( 'button.menu__handle' );
					this.shapeEl = this.el.querySelector( 'div.morph-shape' );

					var s = Snap( this.shapeEl.querySelector( 'svg' ) );
					this.pathEl = s.select( 'path' );
					this.paths = {
						reset : this.pathEl.attr( 'd' ),
						open : this.shapeEl.getAttribute( 'data-morph-open' ),
						close : this.shapeEl.getAttribute( 'data-morph-close' )
					};

					this.isOpen = false;

					this.initEvents();
				};

				SVGMenu.prototype.initEvents = function() {
					this.trigger.addEventListener( 'click', this.toggle.bind(this) );
				};

				SVGMenu.prototype.toggle = function() {
					var self = this;

					if( this.isOpen ) {
						classie.remove( self.el, 'menu--anim' );
						setTimeout( function() { classie.remove( self.el, 'menu--open' );	}, 250 );
					}
					else {
						classie.add( self.el, 'menu--anim' );
						setTimeout( function() { classie.add( self.el, 'menu--open' );	}, 250 );
					}
					this.pathEl.stop().animate( { 'path' : this.isOpen ? this.paths.close : this.paths.open }, 350, mina.easeout, function() {
						self.pathEl.stop().animate( { 'path' : self.paths.reset }, 800, mina.elastic );
					} );
					
					this.isOpen = !this.isOpen;
				};

				new SVGMenu( document.getElementById( 'menu' ) );

			})();
		</script>
	</body>
</html>