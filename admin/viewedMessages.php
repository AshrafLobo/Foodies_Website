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
$sql2 = mysql_query("SELECT * FROM messages WHERE viewed='NO'");
$productCount1 = mysql_num_rows($sql2); // count the output amount
?>

<?php 
$sql4 = mysql_query("SELECT * FROM messages");
$productCount2 = mysql_num_rows($sql4); // count the output amount
?>

<?php 
// This block grabs the whole list for viewing
$product_list3 = "";

$page4=$_GET["page4"];

if($page4=="" || $page4=="1")
{
	$page5=0;
}

else
{
	$page5=($page4*8)-8;
}

$sql5 = mysql_query("SELECT * FROM messages WHERE viewed='YES' ORDER BY date DESC LIMIT $page5,8");
$sql6 = mysql_query("SELECT * FROM messages WHERE viewed='YES' ORDER BY date");

$cou3 = mysql_num_rows($sql6);
$productCount3 = mysql_num_rows($sql5); // count the output amount
if ($productCount3 > 0) {
while($row = mysql_fetch_array($sql5))
{ 
	$id2 = $row["messageid"];
	$usern2= $row["username"];
	$msg2 = $row["message"];
	$date_added2 = strftime("%b %d, %Y", strtotime($row["date"]));
	$product_list3 .= "
      <tr>
		<td align='left'>$usern2</td>
		<td align='left'>$msg2</td>
		<td align='left'>$date_added2</td>
		<td align='left' style='font-size:12pt;'><a href='message.php?deleteid=$id2' class='glyphicon glyphicon-remove'></a><br /></td>
      </tr>
";
}

} 
else 
{
	$product_list3 = "You have no messages yet";
}

$a3=$cou3/8;
$a3=ceil($a3);
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

#userLi span
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
				<header class="codrops-header" style='width:900px;' >
				<div id='pageContent'>
				<div style='<?php echo $styleDel;?>'><?php echo $question; ?><?php echo $del; ?><?php echo $yes; ?><?php echo $del; ?><?php echo $yes2; ?><?php echo $no; ?></div>
				<div style='800px; margin-left:50px;'>
					<a href="message.php" onclick='displayNewMessages()'>
						<div style='float:left;width:200px;height:100px;margin-right:100px;text-align:center;background-color:#ff2800;padding:5px;position:relative;'>
							<span class='glyphicon glyphicon-comment' style='font-size:28px;text-align:center;margin-top:25px;'></span>
							<div>NEW MESSAGES</div>
							<span class='badge' style='position:absolute;top:10px;right:20px;'><?php echo $productCount1; ?></span>
						</div>
					</a>
					<a href="allMessages.php" >					
						<div style='float:left;width:200px;height:100px;margin-right:100px;text-align:center;background-color:#ff2800;padding:5px;position:relative;'>
							<span class='glyphicon glyphicon-inbox' style='font-size:28px;text-align:center;margin-top:25px;'></span>
							<div>ALL MESSAGES</div>
							<span class='badge' style='position:absolute;top:10px;right:20px;color:#fff;font:Kalinga;'><?php echo $productCount2; ?></span>
						</div>
					</a>
					<a href="viewedMessages.php" >
						<div style='float:left;width:200px;height:100px;text-align:center;background-color:#ff2800;padding:5px;position:relative;'>
							<span class='glyphicon glyphicon-envelope' style='font-size:28px;text-align:center;margin-top:25px;'></span>
							<div>VIEWED MESSAGES</div>
							<span class='badge' style='position:absolute;top:10px;right:20px;'><?php echo $productCount3; ?></span>
						</div>
					</a>
				</div>
				<br>
				<hr style='width:800px;color:#fff;margin:0 auto;'>
				<br>
				
					<div style='font-size:15px;font:Kalinga;width:900px;margin:0 auto;' id='viewedMessages'>
					<h3 class='pull-left'>VIEWED MESSAGES</h3>
						<table class='table'>
							<thead>
							  <tr>
								<th>Username</th>
								<th>Message</th>
								<th>Date Sent</th>
								<th></th>		
							  </tr>
							</thead>
							<tbody>
								<?php echo $product_list3; ?>							
							</tbody>
						</table>
						<?php for($b=1;$b<=$a3;$b++){ ?>
							<span style="border:2px #000 solid; color:#ff2800;text-align:center; margin:0 2.5px;padding:5px;"><a href="message.php?page4=<?php echo $b;?>" style="color:#ff2800;"><?php echo $b." ";?></a></span>
						<?php } ?>						
					</div>
							
					</div>
				</header>
				<!-- Related demos -->
				<section class="related">

				</section>
			</div><!-- /main -->
		</div><!-- /container -->
		<script src="js/classie.js"></script>
		<!-- Load jQuery from Google CDN -->
<script src="js/jquery.min.js"></script>

<!-- Only required if you choose to use jQuery Easing animations -->
<script src="js/jquery.easing.min.js"></script>

<!-- Load Panelslider -->
<script src="js/jquery.panelslider.min.js"></script>
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