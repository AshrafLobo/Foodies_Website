<?php
$emailList="";
if (isset($_POST['uname']))
{
	$uVar = mysql_real_escape_string($_POST['uname']);
	$messageVar = mysql_real_escape_string($_POST['message']);
	$dateVar =  date("Y-m-d");
	
	$sqlQuery2 = mysql_query("INSERT INTO `messages` (`username`, `message`, `date`) VALUES ( '$uVar', '$messageVar', '$dateVar') ");
	$emailList = '<p style="width:400px;background-color:#ff2800;color:#fff;">THANK YOU FOR YOUR FEEDBACK</p>';
}
else
{$emailList = "";}
?>
<div class="feedback">
	<a id="feedback_button">Feedback</a>

	<form class="form" action='index.php' method='post'>			
		<p style='color:#3f3f3f'>SAY HELLO</p>
		<p style='color:#3f3f3f'>WE'D LOVE TO HEAR FROM YOU</p>
		<hr style='border:0.5px solid #3f3f3f;width:100%;'>
        <input type='text' name='uname' id='uname' placeholder='ENTER TEMPORARY NAME' style='width:100%;'/>
		<textarea id="feedback_text" name='message'></textarea>
        <input type="checkbox" value='Select to prove you are not a bot' id='checkbox'>
		<input type="submit" value="Send" id="submit_form" disabled='true' />
	</form>
</div>

<div id='m_popup'>
	 <?php echo $emailList;?> 
</div>

<div id='catalog'>
	<div id='downloadWrapper'><a href="#" style='font:Kalinga;font-size:12px;' id='download'>VIEW CATALOGUE</a></div>
</div>

<script>
$( document ).ready(function() {
    $("#m_popup").delay(3000).fadeOut("slow");
});

$('#checkbox').change(function(){
    document.getElementById('submit_form').disabled = false;
});
</script>