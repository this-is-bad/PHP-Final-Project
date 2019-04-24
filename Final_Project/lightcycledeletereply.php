<?php
// Start the session
session_start();
?>
<?php
include 'connect.php';
doDB();

	//haven't seen the selection form, so show it
	$display_block = "<h1>Deletion Confirmation</h1>";
	$saved_id = $_SESSION['subject_id'];
	//get parts of records
	$get_list_sql = "SELECT * FROM subjects WHERE subject_id = $saved_id;";
	$get_list_res = mysqli_query($mysqli, $get_list_sql) or die(mysqli_error($mysqli));

	if (mysqli_num_rows($get_list_res) < 1) {
		//no records
		$display_block .= "<p><em>There was an error retrieving the topic!</em></p>";

	} else {
		//has a record, so display results for confirmation
		$rec = mysqli_fetch_array($get_list_res);
		$display_title = stripslashes($rec['subject_title']);
		$display_block .= "<p>Topic title: ".$display_title."</p>";
		$display_block .= "<p><a href='do_deletesubject.php'>Confirm Deletion</a></p>";
		$display_block .= "<p><a href='lightcyclemenu.html'>Cancel Deletion and Return to Menu</a></p>";
	}
	//free result
	mysqli_free_result($get_list_res);

?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset=utf-8>
<title>Delete Posting</title>
<link href="css/lightcycle_discussion.css" rel="stylesheet" type="text/css" />
</head>
<body>
	<?php echo $display_block; ?>
</body>
</html>
