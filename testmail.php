<html>
<head><title>the pag test mail</title></head>
<body>
 <?php
$to      = 'skanhero@yahoo.fr,yawyaw@protonmail.com,shrifcom@outlook.com';
$subject = 'the subject';
$message = 'hello this is test message';
$headers = 'From: marfed5@holly.dreamhost.com' . "\r\n" .
    'Reply-To: marfed5@holly.dreamhost.com' . "\r\n" .
    'X-Mailer: PHP/' . phpversion();

$send = mail($to, $subject, $message, $headers);

if($send)
    echo "<h1>Done send mail ..!</h1>";
    else
        echo "<h1>Error send mail ..!</h1>";
?>   
</body>
</html>

