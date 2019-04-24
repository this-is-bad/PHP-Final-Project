<?php
// Start the session
session_start();
?>
<?php
include 'connect.php';
doDB();
if (!$_POST) {
	//haven't seen the selection form, so show it
	$display_block = "<h1>Edit Post</h1>";
	$saved_id = $_SESSION['subject_id'];
	$saved_title = $_SESSION['subject_title'];
	$saved_reply_text = $_SESSION['reply_text'];
	//get record from subjects table
	$get_subject_sql = "SELECT * FROM subjects WHERE subject_id = $saved_id;";
	$get_subject_res = mysqli_query($mysqli, $get_subject_sql) or die(mysqli_error($mysqli));
	// get record from topic posting table
	$get_reply_sql = "SELECT * FROM replies WHERE subject_id = $saved_id;";
	$get_reply_res = mysqli_query($mysqli, $get_reply_sql) or die(mysqli_error($mysqli));

	if (mysqli_num_rows($get_subject_res) < 1) {
		//no records
		$display_block .= "<p><em>There was an error retrieving your topic!</em></p>";
	} else {
		//topic record exists, so display subject and reply information for editing
		$rec = mysqli_fetch_array($get_subject_res);
		$display_id = stripslashes($rec['subject_id']);
        $display_title = stripslashes($rec['subject_title']);
		$display_block .= "<form class='col-12 col-m-12' method='post' action='".$_SERVER['PHP_SELF']."'>";
		$display_block .="<p>Topic Title:<br /><input type='text' id='subject_title' name='subject_title' size='40' maxlength='150' required='required' value='".$display_title."'></p>";
		$subjectRec = mysqli_fetch_array($get_reply_res);
		$display_reply = stripslashes($subjectRec['reply_text']);
		$display_block .="<p class='col-12 col-m-12'>Post Text:<br /><textarea class='col-12 col-m-12' style='vertical-align:text-top;' id='reply_text' name='reply_text' rows='8' cols='30'>".$display_reply."</textarea></p>";
		$display_block .= "<button type='submit' id='change' name='change' value='change'>Change entry</button></p>";
		$display_block .="</form>";
	}
	//free result
	mysqli_free_result($get_reply_res);
	mysqli_free_result($get_subject_res);
}
// posted form, so tables should update
else
{
    $subject_create_time_sql = "SELECT subject_create_time  
                  FROM subjects 
                  WHERE subject_id = " .$_SESSION['subject_id'];

    $get_create_time_res = mysqli_query($mysqli, $subject_create_time_sql) or die(mysqli_error($mysqli));
    
    $date_arr =  mysqli_fetch_array($get_create_time_res);

    $subject_create_time = stripslashes($date_arr['subject_create_time']);

    $edit_timeout_sql = "SELECT 1 AS error
                  FROM subjects 
                  WHERE subject_id = " .$_SESSION['subject_id']. "
                  AND DATE_ADD(now(), INTERVAL -10 MINUTE) > '".$subject_create_time."';";

    $replies_exist_sql = "SELECT 1 AS error
                    FROM replies
                    WHERE subject_id = " .$_SESSION['subject_id']. "
                    AND reply_id > " .$_SESSION['reply_id'];

    $get_edit_timeout_res = mysqli_query($mysqli, $edit_timeout_sql) or die(mysqli_error($mysqli));

    $get_replies_exist_res = mysqli_query($mysqli, $replies_exist_sql) or die(mysqli_error($mysqli));

    // operation timed out or a reply has been made
    if (mysqli_num_rows($get_edit_timeout_res) > 0 or mysqli_num_rows($get_replies_exist_res) > 0) {

        $edit_timeout = (mysqli_num_rows($get_edit_timeout_res) > 0 ? "The 10 minute time limit has been exceeded." : "" ); 
        
        $replies_exist =(mysqli_num_rows($get_replies_exist_res) > 0 ? "Replies have been made to this topic." : "" ); 
             
        
        $display_block = "<p>Unable to modify post.<br />".(is_null($edit_timeout) ? '' : $edit_timeout)."<br />".(is_null($replies_exist) ? '' : $replies_exist)."</p>";

    } else {    

        $clean_subject_title = mysqli_real_escape_string($mysqli, $_POST['subject_title']);
        $clean_reply_text = mysqli_real_escape_string($mysqli, $_POST['reply_text']);

        //create and issue the subjects update
        $update_topic_sql = "UPDATE subjects SET subject_title = '".$clean_subject_title ."' WHERE subject_id = ".$_SESSION['subject_id'];
        $update_topic_res = mysqli_query($mysqli, $update_topic_sql) or die(mysqli_error($mysqli));

        //create and issue the replies update
        $update_reply_sql = "UPDATE replies SET reply_text='" .$clean_reply_text."' WHERE subject_id = ".$_SESSION['subject_id']." AND reply_id = ".$_SESSION['reply_id'];
        $update_reply_res = mysqli_query($mysqli, $update_reply_sql) or die(mysqli_error($mysqli));

        //free result
        mysqli_free_result($get_create_time_res);
        mysqli_free_result($get_edit_timeout_res);
        mysqli_free_result($get_replies_exist_res);

        //close connection to MySQL
        mysqli_close($mysqli);

        //create nice message for user
        $display_block ="<h2>Your posting has been modified...</h2>";
        $display_block.="<p>The topic title has been modified to: <strong><em>".$clean_subject_title."</em></strong><br>";
        $display_block.="The topic text has been modified to: <strong><em>".$clean_reply_text."</em></strong></p>";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset=utf-8>
<title>Edit Posting</title>
<link href="css/lightcycle_discussion.css" rel="stylesheet" type="text/css" />
</head>
<body>
<?php echo $display_block; ?>
<input type="button" name="menu" id="menu" value="Return to Menu" onclick="location.href='lightcyclemenu.html'">
</body>
</html>
