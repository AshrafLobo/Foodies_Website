<?php 
session_start(); // Start session first thing in script
error_reporting(E_ERROR |  E_PARSE);?>

<?php require_once('inc/connect_to_mysql.php'); ?>

<?php 
// Check to see the URL variable is set and that it exists in the database
if (isset($_GET['id'])) {
	// Connect to the MySQL database  
	$id = preg_replace('#[^0-9]#i', '', $_GET['id']); 
	// Use this var to check to see if this ID exists, if yes then get the product 
	// details, if no then exit this script and give message why
	$sql = mysql_query("SELECT * FROM products WHERE id='$id' LIMIT 1");
	$productCount = mysql_num_rows($sql); // count the output amount
    if ($productCount > 0) {
		// get all the product details
		while($row = mysql_fetch_array($sql)){ 
			 $product_name = $row["name"];
			 $product_id = $row["id"];
			 $price = $row["price"];
			 $details = $row["description"];
			 $times = $row["timesOrdered"];
			 $category = $row["category_id"];
			 $date_added = strftime("%b %d, %Y", strtotime($row["date"]));
			 $product_image = $row["image"]; 
			 $product_ingredients = $row["Ingredients"];
		 
         }
		 
	} else {
		echo "That item does not exist.";
	    exit();
	}
		
} else {
	echo "Data to render this page is missing.";
	exit();
}
require_once('inc/connect.php');
$sql2 = $db->query("SELECT * FROM products WHERE category_id='".$category."' ORDER BY timesOrdered DESC LIMIT 3");

$sql3 = $db->query("SELECT * FROM categories WHERE category_id='".$category."' LIMIT 1");
foreach ($sql3 as $cats) 
{$category_name =$cats['name'] ;}

// displaying categories

mysql_close();

?>
<!DOCTYPE html>
<html>
<head lang="en">
<meta charset="UTF-8">
<title><?php echo $product_name; ?></title>
<?php require_once('templates/dependancies.php'); ?>
<link href='css/style2.css' rel='stylesheet' type='text/css'>
<style>
	#bodyWrapper
	{
		padding:0 40px;
	}
	
	#productDetailsWrapper,#similarProducts
	{
		width:1200px;
		margin:10px auto;
	}
	
	#productImage
	{
		margin-left:200px;
	}
	
	#image
	{
		margin-top:80px;
	}
	
	#toggle
	{
		width:380px;
		font:'Kalinga';
		font-size:15px;
	}
	
	#toggle li 
	{
		float:left;
		list-style-type:none;
		margin:10px;
	}
	
	#toggle li a
	{
		color:#fff;
		text-decoration:none;
	}
	
	#toggle li a:focus
	{
		border-style:none;
	}
	
	#toggle li a:hover
	{
		color:#ff2800;
		text-decoration:none;
		border-bottom:2px solid #ff2800;
	}
	
	#purchaseDetails
	{
		display:none;
	}
	
	#productInformation
	{
		width:400px;
		margin:60px 30px;
	}
	
	#purchaseDetails,#productDetails
	{
		font:'Gadugi';
		font-size:16px;
		margin:0px auto;
		width:400px;
		height:350px;
	}
	
	#purchaseDetails select
	{
		width:50px;
		margin-left:70px;
		margin-top:10px;
	}
	
	#purchaseDetails h4
	{
		width:100px;
		float:left;
		text-align:right;
	}
	
	#purchaseDetails div
	{
		clear:both;
	}
	
	#dateAdded,#itemPrice,#categoryName
	{
		border-style:none;
		background-color:transparent;
		text-align:center;
		width:200px;
		margin:10px auto;
		float:left;
	}
	
	#btnAddToCart
	{
		width:300px;
		height:50px;
		padding:10px;
		margin:70px auto;
		background-color:transparent;
		border:2px solid #fff;
		color:#fff;
		font:"Kalinga";
		font-size:18px;
	}
	
	#btnAddToCart:hover
	{
		background-color:#ff2800;
		border:solid thin #ff2800;
		color:#fff;
	}
</style>
</head>
<body>


<?php require_once('templates/header.php');?>
<div id="bodyWrapper">
<div id="page">

	<div id="productDetailsWrapper">

		<div id='productImage' class='pull-left' style='width:400px;overflow:hidden;'>

			<img src='media/products/<?php echo $product_image; ?>'  id='image'>

		</div>

		<div id='productInformation' class='pull-left'>

			<div>
				<ul id='toggle'>
					<li><a href='#' onclick='displayPurchaseDetails()' class='glyphicon glyphicon-piggy-bank'> Purchase Details</a></li>
					<li><a href='#' onclick='displayProductDetails()' class='glyphicon glyphicon-list'> Product Details</a></li>
				</ul>
				<hr style="width:400px;color:#000;">
			</div>

			<div id='purchaseDetails'>
				<div>
					<div><h2><?php echo $product_name; ?></h2></div> 
					<div><h4>Date Added</h4><input type='text' id='dateAdded' value="14/9/15" readonly="readonly"></div>
					<div><h4>Price</h4><input type='text' value='<?php echo "KSH ".$price; ?>' id='itemPrice' readonly="readonly"></div>
					<div><h4>Category</h4><input type='text' value='<?php echo $category_name; ?>' id='categoryName' readonly="readonly"></div>	
					<form id="form1" name="form1" method="post" action='cart.php'>
						<input type="hidden" name="pid" id="pid" value='<?php echo $product_id; ?>' />
						<button type="submit" name="button" id="btnAddToCart"><span class='glyphicon glyphicon-shopping-cart'></span> ADD TO CART<button/>
					</form>
				</div>					
			</div>
			
			<div id='productDetails'>
				<div><h2><?php echo $product_name; ?></h2></div> 
				<div id='description'>
                    <h2>Description</h2>
                    <p><?php echo $details; ?></p>
				</div>

				<h2>Ingredients</h2>
				<div>
					<h6>
						<?php echo $product_ingredients;?>
					</h6>
				</div>

			</div>

		</div>

	</div>

	<div id="similarProducts">
		<div id="rowWrapper" class="row">
		
			<div class="col-sm-12 col-md-12 col-lg-12" id='similar' style='margin-top:30px;'>   
				<img src="images/titles/similar_products.png" style='width:900px;'>    
			</div>  
			
			<div class="clearfix visible-sm-block clearfix visible-md-block clearfix visible-lg-block clearfix"></div>
			
			<div id='similarWrapper'>
				<?php foreach ($sql2 as $cat):?>
					<div id='row' class='col-sm-12 col-md-6 col-lg-4'>
						<div>
							<a href='product.php?id=<?php echo $cat['id']?>'><img src='media/products/<?php echo $cat['image'] ?>' id='img' width='300' height='225'></a>
							<button type='button' id='moreButton' class='clickr'><a href='product.php?id='<?php echo $cat['id']?>''><span class='glyphicon glyphicon-plus-sign'></span></a></button>
						</div>

					</div>

					<div class="clearfix visible-sm-block"></div> 
				<?php endforeach; ?>
			</div>
			
		</div>
	</div>
	
</div>

</div>
<!-- Load jQuery from Google CDN -->
<script src="js/jquery.min.js"></script>

<!-- Only required if you choose to use jQuery Easing animations -->
<script src="js/jquery.easing.min.js"></script>

<!-- Load Panelslider -->
<script src="js/jquery.panelslider.min.js"></script>

<script>
	function displayPurchaseDetails() {
		var ix = $("#page").index();
		
		$('#purchaseDetails').toggle( ix === 0 );
		$('#productDetails').toggle( ix === 1 );
	};

	function displayProductDetails() {
		var ix = $("#page").index();
		
		$('#purchaseDetails').toggle( ix === 1 );
		$('#productDetails').toggle( ix === 0 );
	};
</script>

</body>
</html>