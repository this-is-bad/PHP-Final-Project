<?php
include 'connect.php';
doDB();

//check to see if showing the form or adding the post
if (!$_POST) {
   // showing the form; check for required item in query string
   if (!isset($_GET['reply_id'])) {
      header("Location: lightcyclesubjectlist.php");
      exit;
   }

   //create safe values for use
   $safe_reply_id = mysqli_real_escape_string($mysqli, $_GET['reply_id']);

   //still have to verify topic and post
   $verify_sql = "SELECT s.subject_id, s.subject_title 
                  FROM replies AS r
                  LEFT JOIN subjects AS s
                  ON r.subject_id = s.subject_id 
                  WHERE r.reply_id = '".$safe_reply_id."'";

   $verify_res = mysqli_query($mysqli, $verify_sql)
                 or die(mysqli_error($mysqli));

   if (mysqli_num_rows($verify_res) < 1) {
      //this post or topic does not exist
      header("Location: lightcyclesubjectlist.php");
      exit;
   } else {
      //get the topic id and title
      while($subject_info = mysqli_fetch_array($verify_res)) {
         $subject_id = $subject_info['subject_id'];
         $subject_title = stripslashes($subject_info['subject_title']);
      }
?>
      <!DOCTYPE html>
      <html lang="en">
      <head>
      <meta charset=utf-8>
      <title>Post Your Reply in <?php echo $subject_title; ?></title>
      <link href="css/lightcycle_discussion.css" rel="stylesheet" type="text/css" />
      </head>
      <body>
         <h1 class="moreResponsiveH">
            Post Your Reply in 
            <br />
            <?php echo $subject_title; ?>
         </h1>
         <form class="col-12 col-m-12" method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
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
            <input type="hidden" name="subject_id" value="<?php echo $subject_id; ?>">
            <button type="submit" name="submit" value="submit">Add Post</button>
         </form>
      </body>
      </html>
<?php
      //free result
      mysqli_free_result($verify_res);

      //close connection to MySQL
      mysqli_close($mysqli);
   }

} else if ($_POST) {
      //check for required items from form
      if ((!$_POST['subject_id']) || (!$_POST['reply_text']) ||
          (!$_POST['reply_owner'])) {
          header("Location: lightcyclesubjectlist.php");
          exit;
      }

      //create safe values for use
      $safe_subject_id = mysqli_real_escape_string($mysqli, $_POST['subject_id']);
      $safe_reply_text = mysqli_real_escape_string($mysqli, $_POST['reply_text']);
      $safe_reply_owner = mysqli_real_escape_string($mysqli, $_POST['reply_owner']);

      //add the post
      $add_reply_sql = "INSERT INTO replies (subject_id, reply_text, reply_create_time, reply_owner) 
                       VALUES ('".$safe_subject_id."', '".$safe_reply_text."', now(),'".$safe_reply_owner."')";
      $add_reply_res = mysqli_query($mysqli, $add_reply_sql)
                      or die(mysqli_error($mysqli));

      //close connection to MySQL
      mysqli_close($mysqli);

      //redirect user to topic
      header("Location: lightcycleshowsubject.php?subject_id=".$_POST['subject_id']);
      exit;
}
?>

