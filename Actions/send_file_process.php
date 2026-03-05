<?php
include '../functions/log_system.php';
session_start();
$recipient = $_POST['recipient'];
$sender = $_POST['sender'];
$message = $_POST['message'];
$senderfile = $_POST['senderfile'];

	$department = $_SESSION['current_sesion'];
	$subfolder = $_SESSION['subfolder'];

	$email_sender = getenv('SMTP_EMAIL_SENDER') ?: '';
	$sender_password = getenv('SMTP_EMAIL_PASSWORD') ?: '';
	$smtp_host = getenv('SMTP_HOST') ?: 'smtp.gmail.com';
	$smtp_port = getenv('SMTP_PORT') ?: 587;
	$smtp_secure = getenv('SMTP_SECURE') ?: 'tls';
	$email_receiver = $recipient;


	$fileName = $senderfile;
	$folder = "../archived_files/".$department."/".$subfolder;			
	$targetFilePath = $folder ."/". $fileName; 

	$html ='	
		<p>Sender: '.$sender.' </p>
		<p>Message: <br> '.$message.' </p>
		<br>
		<p>Note: This file has been sent from File Arciver Application. Do Reply to this Message</p>
		<br>
		<p>Please see attachment below</p>
	';
	require '../modules/PHPMailer/PHPMailerAutoload.php';

	if ($email_sender === '' || $sender_password === '') {
		echo 'Email is not configured. Please set SMTP_EMAIL_SENDER and SMTP_EMAIL_PASSWORD.';
		doLogs($_SESSION['archiveuser'], 'SEND EMAIL', 'FAILED - SMTP CONFIG MISSING');
		exit;
	}

	$mail = new PHPMailer;
	
//	$mail->SMTPDebug = 1;                               // Enable verbose debug output
	
	$mail->isSMTP();                                      // Set mailer to use SMTP
	$mail->Host = $smtp_host;  // Specify main and backup SMTP servers
	$mail->SMTPAuth = true;                               // Enable SMTP authentication
	$mail->Username = $email_sender;                 // SMTP username
	$mail->Password = $sender_password;                           // SMTP password
	$mail->SMTPSecure = $smtp_secure;                            // Enable TLS encryption, `ssl` also accepted
	$mail->Port = (int)$smtp_port;
	
	$mail->setFrom($email_sender, 'File Archiver - Rose Bakeshop');
	$mail->addAddress($email_receiver);     // Add a recipient
	
	$mail->isHTML(true);                                  // Set email format to HTML
	
	$mail->Subject = "File Archiver - File Forward";
	$mail->Body    = $html;
	$mail->AltBody = $html;
	$mail->AddAttachment($targetFilePath);

	if(!$mail->send()) {
	    echo 'Message could not be sent.';
	    echo 'Mailer Error: ' . $mail->ErrorInfo;
	} else {
		print_r('
			<script>
				$("#sentoemail").hide();
				dialoque_message("File Sent","Email with attachment has been successfuly sent.");
				$("#ronansarboncute").prop("disabled", false);
				$("#ronansarboncute").html("Send");	
			</script>
		');
		doLogs($_SESSION['archiveuser'], $recipient." SEND EMAIL"," SUCCESSFULLY");

		
	}
