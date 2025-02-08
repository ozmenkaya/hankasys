<?php 

$to      = 'tekin.polat.dpu@gmail.com';
$subject = 'the subject';
$message = 'hello';
$headers = 'From: tekinpolat2121@gmail.com' . "\r\n" .
    'Reply-To: tekinpolat2121@gmail.com' . "\r\n" .
    'X-Mailer: PHP/' . phpversion();

mail($to, $subject, $message, $headers);