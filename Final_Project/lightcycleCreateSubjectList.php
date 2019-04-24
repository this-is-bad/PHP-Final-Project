<?php
// Start the session
session_start();
?>
<?php
    include 'connect.php';
    doDB();

    //if connection fails, stop script execution
    if (mysqli_connect_errno()) {
        printf("Connect failed: %s\n", mysqli_connect_error());
        exit();
    }

    $get_subject = "SELECT * FROM subjects";
    $get_subject_res = mysqli_query($mysqli, $get_subject) or die(mysqli_error($mysqli));

    // store the entire response
    $response = array();

    // the array that will hold the titles and links
    $posts = array();

    $xml = "<subjectList>";
    while ($r = mysqli_fetch_array($get_subject_res)) {
        // create the XML
        $xml .= "<subject>";
        $xml .= "<subject_id>".$r['subject_id']."</subject_id>";
        $xml .= "<subject_title>".$r['subject_title']."</subject_title>";
        $xml .= "<subject_create_time>".$r['subject_create_time']."</subject_create_time>";
        $xml .= "<subject_owner>".$r['subject_owner']."</subject_owner>";
        $xml .= "</subject>";
        
        // create the JSON
        $subject_id = $r['subject_id'];
        $subject_title = $r['subject_title'];
        $subject_create_time = $r['subject_create_time'];
        $subject_owner = $r['subject_owner'];

        $posts[] = array('subject_id'=> $subject_id, 'subject_title'=> $subject_title, 'subject_create_time'=> $subject_create_time, 'subject_owner'=> $subject_owner);
    }

    $xml .= "</subjectList>";
    $sxe = new SimpleXMLElement($xml);
    $sxe->asXML("subjects.xml");

    // the posts arrary goes into the response
    $response['subject'] = $posts;

    //creates the JSON file
    $fp = fopen('subjects.json', 'w');
    fwrite($fp, json_encode($response));
    fclose($fp);
    $display_block = <<<END_OF_TEXT
    <!DOCTYPE html>
<html lang="en">
<head>
<meta charset=utf-8>
<title>Files Created</title>
<link href="css/lightcycle_discussion.css" rel="stylesheet" type="text/css" />
</head>
<body>
	<h1>Files Created</h1>
    <h2>subjects.xml has been created</h2>
    <h2>subjects.json has been created</h2>
    <p>
        <a href='viewSubjects.php?file_type=xml'>[View XML Subject List]</a>
        <a href='viewSubjects.php?file_type=json'>[View JSON Subject List]</a>
    </p>
	<form>
		<input type="button" name="menu" id="menu" value="Return to Menu" onclick="location.href='lightcyclemenu.html'">
	</form>
</body>
</html>
END_OF_TEXT;

echo $display_block;
    //close connection to MySQL
    mysqli_close($mysqli);
?>