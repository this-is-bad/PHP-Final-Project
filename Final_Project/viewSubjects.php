<?php
// Start the session
session_start();
?>
<?php

    //check for required info from the query string
    if (!isset($_GET['file_type'])) {
        header("Location: lightcycleCreateSubjectList.php");
        exit;
    }

    //create safe values for use
    $safe_file_type = stripslashes(strtolower($_GET["file_type"]));

    $display_block = "";

    if ($safe_file_type == "json") {
        $subjects = file_get_contents("subjects.json");
        $subjectList = json_decode($subjects);
    }

    if ($safe_file_type == "xml") {
        $subjectList = simplexml_load_file("subjects.xml") or die("Error: Cannot create object");
    }

    foreach($subjectList->subject as $subj){
        $subject_id = $subj->subject_id;
        $subject_title = $subj->subject_title;
        $subject_create_time = $subj->subject_create_time;
        $subject_owner = $subj->subject_owner;
        
        // $formatted_create_time = date_format(date('2019-04-11 22:01:45'), "m/d/y g:i:s e"); 
		//add to display
		$display_block .= <<<END_OF_TEXT
		<div class="col-8 col-m-12">
            <p>
                <span class="yellowy">ID:</span>           $subject_id<br />
                <span class="yellowy">Title:</span>         $subject_title<br />
                <span class="yellowy">Owner:</span>         $subject_owner<br />
                <span class="yellowy">Create Time:</span>   $subject_create_time<br />
                <hr />
            </p>
		</div>
END_OF_TEXT;
}

//  echo date_format(date('2019-04-11 22:01:45'), "m/d/y g:i:s e"); 
// echo date('2019-04-11 22:01:45'); 
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<title>List of topics in the light cycle forum</title>
<link href="css/lightcycle.css" rel="stylesheet" type="text/css" />
</head>
<body>
    <h1>List of topics in light cycle</h1>
    <h3>Source file is <?php echo $safe_file_type ?></h3>
    <?php echo $display_block; ?>
</body>
</html>
