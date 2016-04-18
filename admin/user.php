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
// Delete Item Question to Admin, and Delete Product if they choose
$del ="";
$question = ""; 
$yes = "";
$yes2 ="";
$no = "";
$styleDel = "";

if (isset($_GET['deleteid'])) 
{
	$del =$_GET['deleteid'];
	$question = 'Do you really want to delete user with ID of '; 
	$yes = '? <a href="user.php?yesdelete=';
	$yes2 ='"> Yes</a> ';
	$no = '| <a href="user.php">No</a>';
	$styleDel ='font:Kalinga;font-size:16px;color:#fff;background-color:#ff2800;width:100%;padding:5px;';
}

if (isset($_GET['yesdelete'])) 
{
	// remove item from system and delete its picture
	// delete from database
	$id_to_delete = $_GET['yesdelete'];
	$sql = mysql_query("DELETE FROM users WHERE user_id='$id_to_delete' LIMIT 1") or die (mysql_error());
	
	header("location: user.php"); 
	exit();
}
?>

<?php 
// This block grabs the whole list for viewing
$product_list = "";

$page=$_GET["page"];

if($page=="" || $page=="1")
{
	$page1=0;
}

else
{
	$page1=($page*8)-8;
}

$sql = mysql_query("SELECT * FROM users ORDER BY user_id ASC LIMIT $page1,8");
$sql2 = mysql_query("SELECT * FROM users ORDER BY user_id ASC");

$cou = mysql_num_rows($sql2);
$productCount = mysql_num_rows($sql); // count the output amount
if ($productCount > 0) {
while($row = mysql_fetch_array($sql))
{ 
	$id = $row["user_id"];
	$name = $row["username"];
	$email = $row["email"];
	$admin = $row["admin"];	
	$product_list .= "
      <tr>
		<td align='left'>$id</td>	  
		<td align='left'>$name</td>
		<td align='left'>$email</td>
		<td align='left'>$admin</td>		
		<td align='left' style='font-size:12pt;'><a href='edit_user.php?id=$id' class='glyphicon glyphicon-edit' ></a> <a href='user.php?deleteid=$id' class='glyphicon glyphicon-remove'></a><br /></td>
      </tr>
";
}

} 
else 
{
	$product_list = "You have no users listed in your store yet";
}

$a=$cou/8;
$a=ceil($a);
?>

<!DOCTYPE html>
<html lang="en" class="no-js">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>USER LIST</title>

<?php require_once('templates/dependancies.php');?>
<style>
#addView
{
	display:none;
}

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
	
		<header class="codrops-header">
			<div id="pageContent"><br />
				<div style='<?php echo $styleDel;?>'><?php echo $question; ?><?php echo $del; ?><?php echo $yes; ?><?php echo $del; ?><?php echo $yes2; ?><?php echo $no; ?></div>
				<div  style="margin-left:24px;" id='tableView'>
					<h2 align='left'>User List</h2>
					
					<div style='font-size:15px;font:Kalinga;'>
						<table class='table'>
							<thead>
							  <tr>
								<th>User ID</th>
								<th>Username</th>
								<th>Email</th>
								<th>Privileges</th>
								<th></th>		
							  </tr>
							</thead>
							<tbody>
								<?php echo $product_list; ?>							
							</tbody>
						</table>
						<?php for($b=1;$b<=$a;$b++){ ?>
							<span style="border:2px #000 solid; color:#ff2800;text-align:center; margin:0 2.5px;padding:5px;"><a href="inventory.php?page=<?php echo $b;?>" style="color:#ff2800;"><?php echo $b." ";?></a></span>
						<?php } ?>						
					</div>
					<hr />
				</div>
				
			</div>
		</header>

	</div><!-- /main -->
	
</div><!-- /container -->

<script src="js/classie.js"></script>

<script>
(function() {

function SVGMenu( el, options ) 
{
	this.el = el;
	this.init();
}

SVGMenu.prototype.init = function() 
{
	this.trigger = this.el.querySelector( 'button.menu__handle' );
	this.shapeEl = this.el.querySelector( 'div.morph-shape' );

	var s = Snap( this.shapeEl.querySelector( 'svg' ) );
	this.pathEl = s.select( 'path' );
	this.paths = 
	{
		reset : this.pathEl.attr( 'd' ),
		open : this.shapeEl.getAttribute( 'data-morph-open' ),
		close : this.shapeEl.getAttribute( 'data-morph-close' )
	};

	this.isOpen = false;

	this.initEvents();
};

SVGMenu.prototype.initEvents = function() 
{
	this.trigger.addEventListener( 'click', this.toggle.bind(this) );
};

SVGMenu.prototype.toggle = function() 
{
var self = this;

if( this.isOpen ) 
{
	classie.remove( self.el, 'menu--anim' );
	setTimeout( function() { classie.remove( self.el, 'menu--open' );	}, 250 );
}
else 
{
	classie.add( self.el, 'menu--anim' );
	setTimeout( function() { classie.add( self.el, 'menu--open' );	}, 250 );
}

this.pathEl.stop().animate( { 'path' : this.isOpen ? this.paths.close : this.paths.open }, 350, mina.easeout, function() 
{
	self.pathEl.stop().animate( { 'path' : self.paths.reset }, 800, mina.elastic );
} 

);

this.isOpen = !this.isOpen;
};

new SVGMenu( document.getElementById( 'menu' ) );

})();
</script>

</body>
</html>