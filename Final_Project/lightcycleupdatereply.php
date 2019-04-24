<?php
include 'connect.php';
doDB();

//check to see if showing the form or adding the post
if (!$_POST) {
   // showing the form; check for required item in query string
   if (!isset($_GET['reply_id'])) {

      echo ($_GET['reply_id']);
      // header("Location: lightcyclesubjectlist.php");
      exit;
   }

   //create safe values for use
   $safe_reply_id = mysqli_real_escape_string($mysqli, $_GET['reply_id']);

   //still have to verify topic and post
   $verify_sql = "SELECT s.subject_id, s.subject_title, reply_text, reply_owner, reply_create_time, DATE_ADD(now(), INTERVAL -10 MINUTE) AS update_time_limit, ISNULL(replies_exist, 'N') AS replies_exist
                  FROM replies AS r
                  JOIN subjects AS s
                  ON r.subject_id = s.subject_id 
                  OUTER APPLY (SELECT 'Y' AS replies_exist FROM replies r2 WHERE r2.subject_id = r.subject_id AND r2.reply_id > r.reply_id ) rep
                  WHERE r.reply_id = ".$safe_reply_id;

   $verify_res = mysqli_query($mysqli, $verify_sql)
                 or die(mysqli_error($mysqli));
    
   if (mysqli_num_rows($verify_res) < 1) {
      //this post or topic does not exist
      header("Location: lightcyclesubjectlist.php");
      exit;
   } else {
      //get the topic id and title
      while($reply_info = mysqli_fetch_array($verify_res)) {
         $subject_id = $reply_info['subject_id'];
         $subject_title = stripslashes($reply_info['subject_title']);
         $reply_text = stripslashes($reply_info['reply_text']);
         $reply_owner = stripslashes($reply_info['reply_owner']);
         $reply_create_time = stripslashes($reply_info['reply_create_time']);
         $update_time_limit = stripslashes($reply_info['update_time_limit']);
         $replies_exist = $reply_info['replies_exist'];
      }
      

      if ($reply_create_time > $update_time_limit and $replies_exist != "Y" ) {

         $button_block .= <<<END_OF_TEXT
         <input type="hidden" name="subject_id" value="<?php echo $subject_id; ?>">
         <input type="hidden" name="reply_id" value="<?php echo $reply_id; ?>">
         <button type="submit" name="submit" value="submit">Update Post</button>
END_OF_TEXT;

      }
      else {

         $button_block .= <<<END_OF_TEXT
            <span>This post cannot be modified.  The 10 minute time limit has been exceeded or additional replies have been made.</span>
            <input type="button" name="menu" id="menu" value="Return to Menu" onclick="location.href='lightcyclemenu.html'">
END_OF_TEXT;
       
      }
   }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset=utf-8>
<title>Update Your Reply in <?php echo $subject_title; ?></title>
<link href="css/lightcycle_discussion.css" rel="stylesheet" type="text/css" />
</head>
<body>
   <h1>Post Your Reply in <?php echo $subject_title; ?></h1>
   <form class="col-12 col-m-12" method="post" action="do_updatereply.php">
      <p>
         <label for="reply_owner">Your Email Address:</label>
         <br/>
         <input type="email" id="reply_owner" name="reply_owner" size="40" maxlength="150" required="required">
      </p>
      <p>
         <label for="reply_text">Post Text:</label>
         <br/>
         <textarea class="col-12 col-m-12" id="reply_text" name="reply_text" rows="8" cols="30" required="required"></textarea>
      </p>
      <?php echo $button_block; ?>
   </form>
</body>
</html>