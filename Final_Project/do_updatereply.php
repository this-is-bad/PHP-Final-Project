<?php
include 'connect.php';
doDB();

//check for required fields from the form
if ((!$_POST['subject_owner']) || (!$_POST['subject_title']) || (!$_POST['reply_text'])) {
	header("Location: lightcycleupdatereply.php?subject_id=".$_POST['subject_id']&?reply_id=".$_POST['reply_id']");
	exit;
}

//create safe values for input into the database
$clean_subject_owner = mysqli_real_escape_string($mysqli, $_POST['subject_owner']);
$clean_subject_title = mysqli_real_escape_string($mysqli, $_POST['subject_title']);
$clean_reply_text = mysqli_real_escape_string($mysqli, $_POST['reply_text']);
$clean_subject_id = mysqli_real_escape_string($mysqli, $_POST['subject_id']);
$clean_reply_id = mysqli_real_escape_string($mysqli, $_POST['reply_id']);

//create and issue the first query
$update_subject_sql = "UPDATE subjects SET subject_title = '".$clean_subject_title."' WHERE subject_id = '".$clean_subject_id."'";

$update_subject_res = mysqli_query($mysqli, $update_subject_sql) or die(mysqli_error($mysqli));

if ($update_subject_res === TRUE) {
    //create and issue the second query
    $update_reply_sql = "UPDATE replies  SET reply_text  WHERE subject_id = '".$clean_subject_id."' AND reply_id = '".$clean_reply_id."'"; 

    $update_reply_res = mysqli_query($mysqli, $update_reply_sql) or die(mysqli_error($mysqli));

    if ($update_reply_res === TRUE) {
        echo "The record has been updated.";
} else {
    printf("The record could not be updated: %s\n", mysqli_error($mysqli));
}
} else {
    printf("The record could not be updated: %s\n", mysqli_error($mysqli));
}

//close connection to MySQL
mysqli_close($mysqli);

//create nice message for user
$display_block = "<p>The <strong>".$_POST["subject_title"]."</strong> subject has been updated.</p>";

function test_input($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset=utf-8>
<title>Subject Updated</title>
<link href="css/lightcycle_discussion.css" rel="stylesheet" type="text/css" />
</head>
<body>
	<h1>Subject Updated</h1>
	<?php echo $display_block; ?>
	<formclass="col-8 col-m-12">
		<input type="button" name="menu" id="menu" value="Return to Menu" onclick="location.href='lightcyclemenu.html'">
	</form>
</body>
</html>