<?php
session_start();
require_once('inc/connect.php');  
error_reporting(E_ERROR |  E_PARSE);
?>

<?php 
$sql = $db->query("SELECT * FROM categories ORDER BY category_id ASC"); // you can also use prepared statement.
// displaying categories
?> 

<!DOCTYPE html>
<html>
<head lang="en">
<meta charset="UTF-8">
<title>Products</title>
<!--Style sheets-->
<?php require_once('templates/dependancies.php'); ?>
<link rel="stylesheet" type="text/css" href="css/style2.css"/>

<style type="text/css">
	
	#menuButtonWrapper
	{
		width:40px;
		height:40px;
		background-color:#BD1E00;
		position:absolute;
		top:20;
		margin:80px auto;
	}
	
	#menuButtonWrapper button
	{
		text-align:center;
		width:40px;
		heigth:40px;
		border:none;
		padding:5px;
		background-color:transparent;
	}
	
	.glyphicon-chevron-right
	{
		color:#000;
		font-size:25px;
	}
	
	#left-panel ul
	{
		padding:10px;
		margin:0;
	}
	
	#menu
	{
		margin:60px auto;
		width:170px;
		height:40px;
		background-color:#BD1E00;
		border-color:#BD1E00;
		font-size:15px;		
		text-align:center;
		font:"Kalinga";
		font-weight:1;
		font-size:15px;
		padding:10px;
	}
	
	#left-panel li
	{
		list-style-type:none;
		text-align:center;
		width:170px;
		height:40px;
		padding:10px;
		border:solid thin #fff;
		margin:20px auto;
		font:"Kalinga";
		font-weight:1;
		font-size:15px;
	}
	
	#left-panel li:hover
	{
		background-color:#BD1E00;
		border-color:#BD1E00;
	}
	
	#left-panel li a
	{
		text-decoration:none;
		color:#fff;
		cursor:pointer;
	}
	
	#productList
	{
		margin:60px auto;
		width:1366px;
	}
	
	#productListWrapper
	{
		margin:0 auto;
	}
		
	#titleWrapper
	{
		position:relative;
		margin:0 200px;
		width:1200px;
		display:inline-block;
	}
	
	#title
	{
		width:200px;
		font:"Kalinga";
		font-size:12pt;
		font-weight:1pt;
		position:absolute;
		text-align:center;
		top:35px;
		left:370px;
	}
	
	#product
	{
		margin:60px auto;
		width:1200px;	
	}
	
	#productDetailsWrapper
	{
		margin:0 auto;
	}
	
	#productImage,#productInformation
	{
		width:500px;
		margin:10px 75px;
		font:"Kalinga";
		font-size:15px;
	}
	
	#toggle
	{
		
		width:100%;
		height:auto;
	}
	
	#toggle li
	{
		list-style-type:none;
		float:left;
		margin:0px 5px;
		padding:10px;
		background-color:transparent;
		border-top:solid thin #BD1E00;
		border-right:solid thin #BD1E00;
		border-left:solid thin #BD1E00;
		border-radius:5px;
		display:inline-block;
		text-align:center;
	}
	
	#toggle li:after
	{
			clear:both;
	}
	
	#toggle li a
	{
		text-decoration:none;
		color:#BD1E00;		
	}
	
	#toggle li a:hover
	{
		color:#fff;	
	}
	
	#itemName,#dateAdded,#itemPrice,#btnAddToCart
	{
		margin:10px;
		width:100%;
	}
		
	#productDetails
	{
		display:none;
	}
</style>
</head>
<body>

<!-- Centered page -->
<div id="page">
	
	<?php require_once('templates/header.php');?>
	
	<div id="menuButtonWrapper">
		<button type="button" id="left-panel-link" href="#left-panel" class="glyphicon glyphicon-chevron-right"></button> 
	</div>
	
	<div id="productList">
		<div id="productListWrapper">
		
			  <div id="titleWrapper" class="row">
			  
				<div id="results" class="col-sm-12 col-md-12 col-lg-12">   
				  <img src="images/titles/title.png" id="img_title" style='width:900px;'> 
				  <div id="title"><p style="margin:0 auto;"></p></div>
				</div>  
				
				<div class="clearfix visible-sm-block clearfix visible-md-block clearfix visible-lg-block clearfix"></div> 
					
				<div id="rowWrapperWithoutHeader" style='width:900px;'>>
				


			    </div>           
			  </div>
			
		
		</div>
	</div>
		
</div>

<!-- Left panel -->
<div id="left-panel" class="panel">
	
	<h1 id="menu">MENU</h1>
	<hr style="width:220px;color:#fff;">

	<ul id="category">
	   <?php foreach ($sql as $category) : ?>
		  <li data-id='<?php echo $category['category_id'];?>' data-name='<?php echo $category['name'];?>' > <a> <?php echo $category['name']; ?> </a> </li>
	   <?php endforeach; ?>
	</ul>
	
</div>

<!-- Load jQuery from Google CDN -->
<script src="js/jquery.min.js"></script>

<!-- Only required if you choose to use jQuery Easing animations -->
<script src="js/jquery.easing.min.js"></script>

<!-- Load Panelslider -->
<script src="js/jquery.panelslider.min.js"></script>

<script>
$(document).ready(function(){
	
	$('#left-panel-link').panelslider();
	$('#right-panel-link').panelslider({side: 'right', clickClose: false, duration: 600, easingOpen: 'easeInBack', easingClose: 'easeOutBack'});

	$('#close-panel-bt').click(function() {
	$.panelslider.close();
	});
	
    $("#category li").click(function()
	{
           var id = $(this).data('id');
		   var name = $(this).data('name');
		   
		   document.getElementById("title").innerHTML = name;
		   $.post( 
                  "get.php",
                  { name: $(this).data('id') },
                  function(data) {
                     $('#rowWrapperWithoutHeader').html(data);
                  }
               );

           
     }); 
	 
	     $("#category li:first-child").trigger('click')
	     $("#category li:first-child").click(function()
	{
           var id = $(this).data('id');
		   var name = $(this).data('name');
		   
		   document.getElementById("title").innerHTML = name;
		   $.post( 
                  "get.php",
                  { name: $(this).data('id') },
                  function(data) {
                     $('#rowWrapperWithoutHeader').html(data);
                  }
               );

           
     });
	 	 
 }); 
 
</script>
</body>
</html>
