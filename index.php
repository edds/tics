<?php
if(!empty($_POST['submit'])){
	$upload = substr(md5('t'.time()), 0, 5);

	move_uploaded_file($_FILES['file']['tmp_name'], './timetables/'.$upload);
	
	header("Location: http://me.e26.co.uk/tics/$upload.ics");
} 
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN"
	"http://www.w3.org/TR/html4/strict.dtd">

<title>tics - Timetable to ICS</title>
<style>
body {
	font: 14px/22px verdana;
	color: #666;
	padding: 100px 0 0;
	text-align: center;
}
h1 {
	font: 40px palatino,georgia;
	padding: 0;
	margin: 0;
}
span {
	font-family: georgia;
	font-style: italic;
	color: #999;
}
form {
	padding: 10px;
}
div {
	width: 350px;
	margin: 0 auto;
}
.steps {
	text-align: left;
}
button {
	height: 23px;
}
img {
	border: 2px solid #999;
}
.copy {
	font-size: 11px;
	padding-top: 70px;
}
</style>
<div>
<h1>Timetable to ICS</h1>
<span>Convert your timetable in to an ics file you can subscribe to in two easy steps.</span>
<div class="steps">
<p><strong>Step 1:</strong> Download your semester timetable from <a href="https://www.adminservices.soton.ac.uk/adminweb/jsp/timetables/timetablesController.jsp?">the university timetable website</a></p>
<p><img src="download.gif" alt="using the download button"></p>
<p><strong>Step 2:</strong> Upload it here:</p>
</div>
<form action="" method="post" enctype="multipart/form-data">
	<input type="hidden" name="submit" value="submit">
	<input type="file" name="file" /><br><br>
	<button type="submit">Send</button>
</form>

<p class="copy">&copy; 2008 <a href="http://e26.co.uk">Edd Sowden</a></p>
</div>
