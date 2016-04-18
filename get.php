<?php require_once('inc/connect.php'); ?>
<?php
error_reporting(E_ERROR | E_WARNING | E_PARSE);

if( $_REQUEST["name"] )
{
$name = $_REQUEST['name'];
$sql = $db->query("SELECT * FROM products WHERE category_id = '".$name."' ORDER BY category_id ASC"); 

	foreach ($sql as $category)
	{
		$image = $category['image'];
		$id = $category['id'];

		echo "<div id='row' class='col-sm-12 col-md-6 col-lg-4'";
		echo "<div>";
		echo "<div class='crop'><a href='product.php?id=".$id."'><img src='media/products/".$image."' id='img' width='300'></a></div>";
		echo "<a href='product.php?id=".$id."'><button type='button' id='moreButton' class='clickr' data-productid='".$id."'><span class='glyphicon glyphicon-plus-sign'></span></button></a>";
		echo "</div>";
		echo "</div>";
		echo "<div class='clearfix visible-sm-block'></div>";
		
	}
}


