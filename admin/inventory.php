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
	$question = 'Do you really want to delete product with ID of '; 
	$yes = '? <a href="inventory.php?yesdelete=';
	$yes2 ='"> Yes</a> ';
	$no = '| <a href="inventory.php">No</a>';
	$styleDel ='font:Kalinga;font-size:16px;color:#fff;background-color:#ff2800;width:100%;padding:5px;';
}

if (isset($_GET['yesdelete'])) 
{
	// remove item from system and delete its picture
	// delete from database
	$id_to_delete = $_GET['yesdelete'];
	$sql = mysql_query("DELETE FROM products WHERE id='$id_to_delete' LIMIT 1") or die (mysql_error());
	
	// unlink the image from server
	// Remove The Pic -------------------------------------------
	$pictodelete = ("../media/products/".$image."");
	
	if (file_exists($pictodelete)) 
	{
		unlink($pictodelete);
	}
	
	header("location: inventory.php"); 
	exit();
}
?>

<?php 
// Parse the form data and add inventory item to the system
if (isset($_POST['product_name']))
{

	$product_name = mysql_real_escape_string($_POST['product_name']);
	$price = mysql_real_escape_string($_POST['price']);
	$category = mysql_real_escape_string($_POST['category']);
	$details = mysql_real_escape_string($_POST['details']);
	$image = mysql_real_escape_string($_POST['image']);
	$in = mysql_real_escape_string($_POST['ingredients']);
	// See if that product name is an identical match to another product in the system
	$sql = mysql_query("SELECT id FROM products WHERE name='$product_name' LIMIT 1");
	$productMatch = mysql_num_rows($sql); // count the output amount
	
	if ($productMatch > 0) 
	{
		echo 'Sorry you tried to place a duplicate "Product Name" into the system, <a href="inventory_list.php">click here</a>';
		exit();
	}
	
	$pid = mysql_insert_id();
	
	// Add this product into the database now
	$sql = mysql_query("INSERT INTO products (name, price, description, category_id, date ,image,Ingredients) 
	VALUES('$product_name','$price','$details','$category',now(),'$image','$in')") or die (mysql_error());
	
	// Place image in the folder
	$newname = $image;
	$move='../media/products/';
	if (move_uploaded_file($_FILES['fileField']['tmp_name'], $move . $newname)) 
	{} 
	else 
	{
		echo "File was not uploaded";
	}
	
	header("location: inventory.php"); 
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

$sql = mysql_query("SELECT * FROM products ORDER BY date DESC LIMIT $page1,8");
$sql2 = mysql_query("SELECT * FROM products ORDER BY date DESC");

$cou = mysql_num_rows($sql2);
$productCount = mysql_num_rows($sql); // count the output amount
if ($productCount > 0) {
while($row = mysql_fetch_array($sql))
{ 
	$id = $row["id"];
	$product_name = $row["name"];
	$category = $row["category_id"];
	$price = $row["price"];
	$date_added = strftime("%b %d, %Y", strtotime($row["date"]));
	
	
	$product_list .= "
      <tr>
		<td align='left'>$product_name</td>
		<td align='left'>$category</td>
		<td align='left'>KSH $price</td>
		<td align='left'>$date_added</td>
		<td align='left' style='font-size:12pt;'><a href='edit.php?pid=$id' class='glyphicon glyphicon-edit' ></a> <a href='inventory.php?deleteid=$id' class='glyphicon glyphicon-remove'></a><br /></td>
      </tr>
";
}

} 
else 
{
	$product_list = "You have no products listed in your store yet";
}

$a=$cou/8;
$a=ceil($a);
?>

<!DOCTYPE html>
<html lang="en" class="no-js">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>INVENTORY</title>

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

<body class="theme-1" >

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
	<div style='<?php echo $styleDel;?>'><?php echo $question; ?><?php echo $del; ?><?php echo $yes; ?><?php echo $del; ?><?php echo $yes2; ?><?php echo $no; ?></div>
				
		<header class="codrops-header">
		<div style='<?php echo $styleDel;?>'><?php echo $question; ?><?php echo $del; ?><?php echo $yes; ?><?php echo $del; ?><?php echo $yes2; ?><?php echo $no; ?></div>
				
			<div id="pageContent"><br />
				<div align="right" id="addInventory"><a href="inventory.php#inventoryForm" onclick="displayAddView()"><span class='fa fa-fw fa-plus'></span> Click me</a></div>
	
				<div  style="margin-left:24px;" id='tableView'>
					<h2 align='left'>Inventory list</h2>
					
					<div style='font-size:15px;font:Kalinga;'>
						<table class='table'>
							<thead>
							  <tr>
								<th>Product Name</th>
								<th>Category</th>
								<th>Price</th>
								<th>Date Added</th>
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
				
				
				<div id="addView">

				<h3>
					Add New Inventory Item Form;
				</h3>
				<hr/>				
				<form action="inventory.php" enctype="multipart/form-data" name="myForm" id="myform" method="post" style='font-size:12pt;'>
					<table width="90%" border="0" cellspacing="0" cellpadding="6">
					
						<tr>
							<td width="20%" >Product Name</td>
							<td class='pull-right'>
								<label>
									<input name="product_name" type="text" id="product_name" size="64" />
								</label>
							</td>
						</tr>
						
						<tr>
							<td >Product Price</td>
							
							<td class='pull-right'>
								<label>
									KSH <input name="price" type="text" id="price" size="60" />
								</label>
							</td>
						</tr>
						
						<tr>
							<td >Category</td>
							<td class='pull-right'>
								<label>
									<select name="category" id="category">
									
									<option value="1">Burger</option>
									<option value="2">Pizza</option>	
									<option value="3">Dessert</option>
									<option value="4">Meat Dish</option>	
									<option value="5">Vegeterian Dish</option>
									</select>
								</label>
							</td>
						</tr>
						
						<tr>
							<td >Product Details</td>
							
							<td class='pull-right'>
								<label>
									<textarea name="details" id="details" cols="64" rows="5"></textarea>
								</label>
							</td>
						</tr>
						
						<tr>
							<td >Product Image</td>
							<td class='pull-right'>
								<label>
									<input type="file" name="fileField" id="fileField" size='51'/>
								</label>
							</td>
						</tr> 

						<tr>
							<td >Enter image name</td>
							<td class='pull-right'>
								<label>
									<input name="image" type="text"  placeholder='like (a.jpg)' id="image" size="64" />
								</label>
							</td>
						</tr> 
						
												<tr>
						<td >Ingredients</td>
							<td class='pull-right'>
								<label>
									<textarea name="ingredients" id="ingredients" cols="64" rows="5"></textarea>
								</label>
							</td>
						</tr> 
												
					</table>
					<hr/>
					<label id='buttonLabel'>
						<input type="submit" name="button" id="button" value="Add This Item Now" style='color:#ff2800;'/>
					</label>					
				</form>
				
				<br />
				<br />
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

<!-- Load jQuery from Google CDN -->
<script src="js/jquery.min.js"></script>

<!-- Only required if you choose to use jQuery Easing animations -->
<script src="js/jquery.easing.min.js"></script>

<!-- Load Panelslider -->
<script src="js/jquery.panelslider.min.js"></script>

<script>
	function displayAddView() {
		var ix = $("#pageContent").index();
		
		$('#addView').toggle( ix === 0 );
		$('#tableView').toggle( ix === 1 );
		
		document.getElementById("addInventory").innerHTML ="<a href='#' onclick='displayTableView()'><span class='fa fa-fw fa-chevron-left'></span> Add New Inventory Item </a>";
	};

	function displayTableView() {
		var ix = $("#pageContent").index();
		
		$('#addView').toggle( ix === 1 );
		$('#tableView').toggle( ix === 0 );
		
		document.getElementById("addInventory").innerHTML ="<a href='inventory.php#inventoryForm' onclick='displayAddView()'><span class='fa fa-fw fa-plus'></span> Go Back To Table</a>";
	};
</script>
</body>
</html>