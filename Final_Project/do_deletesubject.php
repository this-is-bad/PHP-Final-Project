<?php
// Start the session
session_start();
?>
<?php
include 'connect.php';
doDB();
$saved_id = $_SESSION['subject_id'];
//perform deletion from forum_topics
$del_subject_sql = "DELETE FROM subjects WHERE subject_id = $saved_id;";
$del_subject_res = mysqli_query($mysqli, $del_subject_sql) or die(mysqli_error($mysqli));
// perform deletion from forum_posts
$del_reply_sql = "DELETE FROM replies WHERE subject_id = $saved_id;";
$del_reply_res = mysqli_query($mysqli, $del_reply_sql) or die(mysqli_error($mysqli));

$display_block = "<hr><h2><em>Your topic has been deleted.</em></h2>";
$display_block .= "<p><a href='lightcyclemenu.html'>Return to Menu</a></p><hr>";

//close connection
mysqli_close($mysqli);
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset=utf-8>
<title>Deletion Confirmation</title>
<link href="css/lightcycle_discussion.css" rel="stylesheet" type="text/css" />
</head>
<body>
	<?php echo $display_block; ?>
</body>
</html>
