<?php
 $number = count($_SESSION['cart_array']);
?>
<?php
error_reporting(E_ERROR |  E_PARSE);
include_once 'inc/connect_to_mysql.php';
$res = mysql_query("SELECT * FROM users WHERE user_id=".$_SESSION['user']);
$userRow=mysql_fetch_array($res);
?>
<header class="clearfix">
<nav id='mainNavWrapper'>
	<ul>
		<li class="active"><a href="index.php"><span class="glyphicon glyphicon-home"></span> Home</a></li>
		<li><a href="products.php"><span class="glyphicon glyphicon-cutlery"></span> Menu</a></li>
		<?php 
		if (!isset($_SESSION['user']) || empty($_SESSION['user'])){  ?>
		<li style='border-left:1px solid #fff;padding-left:0.5em;'><a href='login.php'><span class="glyphicon glyphicon-log-in pull-left"></span>LOGIN</a></li>
		<li><a href='register.php'><span class="glyphicon glyphicon-user pull-left"></span>REGISTER</a></li>
		<?php }
			else {  ?>
		<li style='border-left:1px solid #fff;padding-left:0.5em;'><?php echo $userRow['username']; ?></li>
		<li><a href='logout.php?logout'><span class="glyphicon glyphicon-log-out pull-left"></span>LOGOUT</a></li>
		<?php
		   $line = $userRow['admin'];
		   $new = "";
		   $new2= "";
		   $new_id = "";


		   // perform a case-Insensitive search for the word "Vi"
		   
		   if (preg_match("/\badmin\b/i", $line, $match)){
			  $new = "<span id='adminButton'><a href='admin/index.php?id=";
			  $new2="' class='glyphicon glyphicon-menu-hamburger pull-left' style='text-align:center;'></a></span>";
			  $new_id = $userRow['user_id'];}
		?>
		<li id='adminLi'><?php echo $new;?><?php echo $new_id?><?php echo $new2;?></li>
		<?php 
		}
		?>
	</ul>
</nav>

<div style='float:right;width:300px;margin-top:15px; margin-right:40px;'>

	<div class='pull-left' style='position:relative;'>
		<a href='cart.php'><span class="glyphicon glyphicon-shopping-cart"></span></a>
		<?php if (!isset($_SESSION['cart_array']) || empty($_SESSION['cart_array'])){  ?>
		
		<?php }
			else {  ?>
		<span class="badge" style='text-align:center;position:absolute;top:0;right:0;border-radius:50%;width:18px; height:18px; padding:5px;background-color:#fff; color:#ff2800;font-size:8pt;'><?php echo $number?></span>
				<?php 
		}
		?>
	</div>

	<div style="position:relative;">
	<div id="search" class="pull-left">
		<form role="form" method="post">
		  <div class="form-group">
			<input type="text" class="form-control" id="keyword" placeholder="Enter searchword">
		  </div>
		</form>
	</div>
	
	<ul style='position:absolute;left:-20px;top:40px;'>
	<li class="dropdown" style='list-style-type:none;'>
		<a href="#" data-toggle="dropdown" class="dropdown-toggle" id="contentLi"></a>
		<ul class="dropdown-menu" id="content">

		</ul>
	</li>
	</ul>
	</div>


	<script type="text/javascript">
	$(document).ready(function() {
		$('#keyword').on('input', function() {
			var searchKeyword = $(this).val();
			if (searchKeyword.length >= 3) {
				document.getElementById("contentLi").click();
				
				$.post('search.php', { keywords: searchKeyword }, function(data) {
					$('ul#content').empty()
					$.each(data, function() {
						$('ul#content').append('<li><a href="product.php?id=' + this.id + '">' + this.name + '</a></li>');
					});
				}, "json");
			}
		});
	});
	</script>
</div>
</header>