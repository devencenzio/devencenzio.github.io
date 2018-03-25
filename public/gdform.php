<?php
    $request_method = $_SERVER["REQUEST_METHOD"];
    if($request_method == "GET"){
      $query_vars = $_GET;
    } elseif ($request_method == "POST"){
      $query_vars = $_POST;
    }
    reset($query_vars);
    $t = date("m-d-Y"). "_" . date("U");

    $email_data = "Included in the request:\n";
    $file = $_SERVER['DOCUMENT_ROOT'] . "/data/gdform_" . $t;
    $fp = fopen($file,"w");
    while (list ($key, $val) = each ($query_vars)) {
     //fputs($fp,"<GDFORM_VARIABLE NAME=$key START>\n");
     fputs($fp,"$key: $val\n");
     switch ($key) {
       case 'name':
         $email_data = $email_data . "\nName: $val";
         break;
       case 'phone':
         $email_data = $email_data . "\nPhone: $val";
         break;
       case 'email':
         $email_data = $email_data . "\nEmail: $val";
         break;
       case 'message':
         $email_data = $email_data . "\n===========Message===========\n $val\n=============================";
         break;
       case 'other':
         $email_data = $email_data . "\nAdditional Details: $val";
         break;
       case 'trim':
         $email_data = $email_data . "\nInterested in TRIM";
         break;
       case 'newcon':
         $email_data = $email_data . "\nInterested in CONSTRUCTION";
         break;
       case 'woodrot':
         $email_data = $email_data . "\nInterested in WOOD ROT REPAIR";
         break;
       case 'decks':
         $email_data = $email_data . "\nInterested in DECKS";
         break;
       case 'drywall':
         $email_data = $email_data . "\nInterested in DRYWALL";
         break;
       case 'renovation':
         $email_data = $email_data . "\nInterested in RENOVATION";
         break;
       case 'stairs':
         $email_data = $email_data . "\nInterested in STAIRS";
         break;

       default:
         // nop
         break;
     }
     fputs($fp, "\n");
     //fputs($fp,"<GDFORM_VARIABLE NAME=$key END>\n");
     if ($key == "redirect") { $landing_page = $val;}
    }
    fclose($fp);

    $to = "jdevens0@gmail.com";
    $email_subject = "DevensConstruction Web Inquiry";
    $email_body = "This data is stored in /data/gdform_" . $t . " on the server.\n$email_data\n";
  	$headers = "From: DevensConstruction Website Contact Form\n";
  	$headers .= "Reply-To: ";

  	$mail_sent = mail($to,$email_subject,$email_body,$headers);
  	
  	if ($mail_sent == true) {
  		$fp = fopen($file,"a");
  		fputs($fp, "Email sent on ". date('Y-m-d H:i:s'));
  		fclose($fp);
  	}

    if ($landing_page != ""){
	header("Location: http://".$_SERVER["HTTP_HOST"]."/$landing_page");
    } else {
	header("Location: http://".$_SERVER["HTTP_HOST"]."/");
    }


?>
