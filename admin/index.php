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
					<img src='images/logos/logo.png'>
					<br>
					<br>
					<hr style="color:#3f3f3f;width:100%;margin:0 auto;border:3px solid #3f3f3f;">
					<br>
					<br>
					<div style="margin:0 auto;width:950px;">
					<a href="delivered.php" onclick='displayNewMessages()'>
						<div style='float:left;width:300px;height:200px;margin-right:20px;text-align:center;background-color:#3f3f3f;padding:5px;position:relative;'>
							<span class='fa fa-fw fa-money' style='font-size:70px;color:#fff;text-align:center;margin-top:25px;'></span>
							<div style='color:#fff;font-size:18pt;'>TOTAL PROFIT</div>
							<span style='color:#fff;font-size:18pt;'><?php $sql = mysql_query("SELECT SUM(total) AS ttl FROM orders");$row= mysql_fetch_array($sql);$sum= $row['ttl'];	echo $sum;?></span>
						</div>
					</a>
					<a href="delivered.php" >					
						<div style='float:left;width:300px;height:200px;margin-right:20px;text-align:center;background-color:#3f3f3f;padding:5px;position:relative;'>
							<span class='fa fa-fw fa-shopping-cart' style='font-size:70px;color:#fff;text-align:center;margin-top:25px;'></span>
							<div style='color:#fff;font-size:18pt;'>TOTAL SALES</div>
							<span style='color:#fff;font-size:18pt;'><?php $sql1 = mysql_query("SELECT COUNT(orderID) AS als FROM orders");$row= mysql_fetch_array($sql1);$sales= $row['als'];	echo $sales;?></span>
						</div>
					</a>
					<a href="user.php" >					
						<div style='float:left;width:300px;height:200px;text-align:center;background-color:#3f3f3f;padding:5px;position:relative;'>
							<span class='fa fa-fw fa-users' style='font-size:70px;color:#fff;text-align:center;margin-top:25px;'></span>
							<div style='color:#fff;font-size:18pt;'>USERS</div>
							<span style='color:#fff;font-size:18pt;'><?php $sql2 = mysql_query("SELECT COUNT(user_id) AS usr FROM users");$row= mysql_fetch_array($sql2);$sales= $row['usr'];	echo $sales;?></span>
						</div>
					</a>					
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