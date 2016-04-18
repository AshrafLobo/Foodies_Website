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
// Parse the form data and add inventory item to the system
if (isset($_POST['uID'])) {
	
	$uid = mysql_real_escape_string($_POST['uID']);
    $uname = mysql_real_escape_string($_POST['usernme']);
	$uemail = mysql_real_escape_string($_POST['email']);
	$uadmin = mysql_real_escape_string($_POST['admin']);
	$usubscribed = mysql_real_escape_string($_POST['subscribed']);

	// See if that product name is an identical match to another product in the system
	$sql = mysql_query("UPDATE users SET username='$uname', email='$uemail', admin='$uadmin', mailinglist='$usubscribed' WHERE user_id='$uid'");

		header("location: user.php"); 
    exit();
}
?>

<?php 
// Gather this product's full information for inserting automatically into the edit form below on page
if (isset($_GET['id'])) {
	$targetID = $_GET['id'];
	$adminId = $_SESSION["user"];

	


    $sql = mysql_query("SELECT * FROM users WHERE user_id='$targetID' LIMIT 1");
    $productCount = mysql_num_rows($sql); // count the output amount
    if ($productCount > 0) {
	    while($row = mysql_fetch_array($sql)){ 
             
			 $ID = $row["user_id"];
			 $name = $row["username"];
			 $email = $row["email"];
			 $admn = $row["admin"];
			 $subscribed =$row["mailinglist"];
			 
        }
	
    } else {
	    echo "That user doesn't exist.";
		exit();
    }
}
?>
<!DOCTYPE html>
<html lang="en" class="no-js">
	<head>
		<meta charset="UTF-8" />
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<title>EDIT USER</title>
		
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
	color:#ff2800;
	border-style:none;
}

#buttonLabel
{
	border:2px #ff2800 solid;
	width:200px;
	height:30px;
	float:right;
}

#buttonLabel:hover
{
	background-color:#3f3f3f;
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
					  <div id="pageContent"><br />
		<div align="right" id="addInventory"><a href="user.php#inventoryForm"><span class='fa fa-fw fa-chevron-left'></span> Go Back To User List</a></div>
		<div align="left" style="margin-left:24px;">
      <h2>Edit User</h2>
    </div>
    <hr />

				<form action="edit_user.php" enctype="multipart/form-data" name="myForm" id="myform" method="post" style='font-size:12pt;'>
					<table width="90%" border="0" cellspacing="0" cellpadding="6">
					
						<tr>
							<td width="20%" >Username</td>
							<td class='pull-right'>
								<label>
									<input name="usernme" type="text" id="usernme" size="64" value="<?php echo $name?>" />
									<input name="uID" type="hidden" id="uID" size="64" value="<?php echo $ID;?>" />
								</label>
							</td>
						</tr>
						
						<tr>
							<td width="20%" >Email</td>
							<td class='pull-right'>
								<label>
									<input name="email" type="text" id="email" size="64" value="<?php echo $email?>" />
								</label>
							</td>
						</tr>
						
						
						<tr>
							<td >Subscribed</td>
							<td class='pull-right'>
								<label>
									<input name="subscribed" type="text" id="subscribed" size="64" value="<?php echo $subscribed;?>" />
								</label>
							</td>
						</tr> 
						
						<tr>
							<td >Privileges</td>
							<td class='pull-right'>
								<label>
									<input type="Text" name="admin" id="admin" value='<?php echo $admn;?>'/>
								</label>
							</td>
						</tr>												
					</table>
					<hr/>
					<label id='buttonLabel'>
						<input type="submit" name="button" id="button" value="Edit This User Now" style='color:#ff2800;'/>
					</label>					
				</form>
    <br />
  <br />
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