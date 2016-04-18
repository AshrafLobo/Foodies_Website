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
if (isset($_POST['prod_id'])) {
	
	$pid = mysql_real_escape_string($_POST['prod_id']);
    $product_name = mysql_real_escape_string($_POST['product_name']);
	$price = mysql_real_escape_string($_POST['price']);
	$category = mysql_real_escape_string($_POST['cat']);
	$details = mysql_real_escape_string($_POST['details']);
	$image = mysql_real_escape_string($_POST['image']);
	// See if that product name is an identical match to another product in the system
	$sql = mysql_query("UPDATE products SET name='$product_name', price='$price', description='$details', category_id='$category', image='$image' WHERE id='$pid'");
	if ($_FILES['fileField']['tmp_name'] != "") {
	    // Place image in the folder 
	$newname = $image;
	$move='../media/products/';
	if (move_uploaded_file($_FILES['fileField']['tmp_name'], $move . $newname)) 
	{} 
	else
	{
	   echo "File was not uploaded";
	}

	}
		header("location: inventory.php"); 
    exit();
}
?>
<?php 
// Gather this product's full information for inserting automatically into the edit form below on page
if (isset($_GET['pid'])) {
	$targetID = $_GET['pid'];
    $sql = mysql_query("SELECT * FROM products WHERE id='$targetID' LIMIT 1");
    $productCount = mysql_num_rows($sql); // count the output amount
    if ($productCount > 0) {
	    while($row = mysql_fetch_array($sql)){ 
             
			 $product_name = $row["name"];
			 $ID = $row["id"];
			 $price = $row["price"];
			 $category = $row["category_id"];
			 $details = $row["description"];
			 $date_added = strftime("%b %d, %Y", strtotime($row["date"]));
			 $image = $row["image"];
        }
    } else {
	    echo "That product doesn't exist.";
		exit();
    }
}
?>
<!DOCTYPE html>
<html lang="en" class="no-js">
	<head>
		<meta charset="UTF-8" />
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<title>EDIT INVENTORY</title>
		
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
		<div align="right" id="addInventory"><a href="inventory.php#inventoryForm"><span class='fa fa-fw fa-plus'></span> Add New Inventory Item</a></div>
		<div align="left" style="margin-left:24px;">
      <h2>Edit Item</h2>
    </div>
    <hr />

				<form action="edit.php" enctype="multipart/form-data" name="myForm" id="myform" method="post" style='font-size:12pt;'>
					<table width="90%" border="0" cellspacing="0" cellpadding="6">
					
						<tr>
							<td width="20%" >Product Name</td>
							<td class='pull-right'>
								<label>
									<input name="product_name" type="text" id="product_name" size="64" value="<?php echo $product_name?>" />
									<input name="prod_id" type="hidden" id="prod_id" size="64" value="<?php echo $ID?>" />
								</label>
							</td>
						</tr>
						
						<tr>
							<td >Product Price</td>
							
							<td class='pull-right'>
								<label>
									KSH <input name="price" type="text" id="price" size="60"  value="<?php echo $price?>"/>
								</label>
							</td>
						</tr>
						
						<tr>
							<td >Category</td>
							<td class='pull-right'>
								<label>
									<select name="cat" id="category" value="<?php echo $category_id?>">
									
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
									<textarea name="details" id="details" cols="64" rows="5"><?php echo $details?></textarea>
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
									<input name="image" type="text" id="image" placeholder='like (a.jpg)' size="64" value="<?php echo $image?>"/>
								</label>
							</td>
						</tr> 
												
					</table>
					<hr/>
					<label id='buttonLabel'>
						<input type="submit" name="button" id="button" value="Edit This Item Now" style='color:#ff2800;'/>
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