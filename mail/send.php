<?php


class Email{
	
	public function __construct(){
       
    }

	public function send($to, $subject, $message) {

		$headers = 	'From: noreply@gestirefacile.it' . "\r\n" .
					'Reply-To: info@gestirefacile.it' . "\r\n" .
					'X-Mailer: PHP/' . phpversion(). "\r\n" .
					'Content-Type: text/html; charset=ISO-8859-1\r\n';
					
		mail($to, $subject, $message, $headers);
	}
	
	
}
/*$email = new Email();
$to      = 'pasqualetarantino@gmail.com';
$subject = 'MyRent';
$message = 'Hello World!';
$email->send($to, $subject, $message);*/


/*

error_reporting(E_ALL);

// Genera un boundary
$mail_boundary = "=_NextPart_" . md5(uniqid(time()));

$to = "pasqualetarantino@gmail.com";
$subject = "Testing e-mail";
$sender = "postmaster@gestirefacile.it";


$headers = "From: $sender\n";
$headers .= "MIME-Version: 1.0\n";
$headers .= "Content-Type: multipart/alternative;\n\tboundary=\"$mail_boundary\"\n";
$headers .= "X-Mailer: PHP " . phpversion();
 
// Corpi del messaggio nei due formati testo e HTML
$text_msg = "messaggio in formato testo";
$html_msg = "<b>messaggio</b> in formato <p><a href='http://www.aruba.it'>html</a><br><img src=\"http://hosting.aruba.it/image_top/top_01.gif\" border=\"0\"></p>";
 
// Costruisci il corpo del messaggio da inviare
$msg = "This is a multi-part message in MIME format.\n\n";
$msg .= "--$mail_boundary\n";
$msg .= "Content-Type: text/plain; charset=\"iso-8859-1\"\n";
$msg .= "Content-Transfer-Encoding: 8bit\n\n";
$msg .= "Questa è una e-Mail di test inviata dal servizio Hosting di Aruba.it per la verifica del corretto funzionamento di PHP mail()function.

Aruba.it";  // aggiungi il messaggio in formato text
 
$msg .= "\n--$mail_boundary\n";
$msg .= "Content-Type: text/html; charset=\"iso-8859-1\"\n";
$msg .= "Content-Transfer-Encoding: 8bit\n\n";
$msg .= "Questa è una e-Mail di test inviata dal servizio Hosting di Aruba.it per la verifica del corretto funzionamento di PHP mail()function.

Aruba.it";  // aggiungi il messaggio in formato HTML
 
// Boundary di terminazione multipart/alternative
$msg .= "\n--$mail_boundary--\n";
 
// Imposta il Return-Path (funziona solo su hosting Windows)
ini_set("sendmail_from", $sender);
 
// Invia il messaggio, il quinto parametro "-f$sender" imposta il Return-Path su hosting Linux
if (mail($to, $subject, $msg, $headers, "-f$sender")) { 
    echo "Mail inviata correttamente!<br><br>Questo di seguito è il codice sorgente usato per l'invio della mail:<br><br>";
    highlight_file($_SERVER["SCRIPT_FILENAME"]);
    unlink($_SERVER["SCRIPT_FILENAME"]);
} else { 
    echo "<br><br>Recapito e-Mail fallito!";
}

*/

?>