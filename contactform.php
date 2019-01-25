<?
// #### CONFIGURE FROM: ADDRESS ##############################################

//$from_address = "";
$from_address = $_POST['r_E_mail'];

// #### ACTIVATE REQUIRED FIELDS? ############################################

$required_on = "yes";

// If you have set $required_on to "yes" above, you can make fields required
// by beginning their name with "r_". For example, if you want to require
// a user to enter their name, use the following HTML:
// <input type='text' name='r_Name'>
//
// If a user fails to enter a required field, they will be taken to a page
// where a message such as "You have not completed all the required fields."
// will be displayed. Please specify the URL to this file below:

$required_errorpage = "error.htm";

// #### OVERRIDE REQUIRED VARIABLES? #########################################

$override = "yes";

// If override is set to "yes", the hidden variables on your HTML
// email form named "rec_mailto", "rec_subject", and "rec_thanks" will be
// overridden and can therefore be removed from the form.

// Enter the email address(es) to send the email to.
// ARMAR UN CASE PARA DISTRIBUIR LOS MAILS SEGUN ELIJAN SECTOR... y siempre con copia a Webmaster.

$incoming_mailto = "webmaster@123.com, info@123.com";
$incoming_mailto_bcc = "info@hotmail.com";

// Enter the email subject.
$incoming_subject = "Comentarios desde 123.com";

// Enter the thank you page URL.
$incoming_thanks = "gracias.htm";

// #### BAN IP ADDRESSES? ####################################################

$ban_ip_on = "no";

// If you have set $ban_ip_on to "yes" above, please enter a list of the
// IP addresses you would like to ban, seperated only by commas.
// An example has been provided below:
$ban_ip_list = "111.222.33.55,11.33.777.99";


// #### ACTIVATE DOMAIN SECURITY? ############################################
$secure_domain_on = "yes";


// #### ACTIVATE AUTO-RESPONSE? ##############################################
$autorespond_on = "yes";



// MAKE SURE DYNAFORM IS NOT BEING LOADED FROM THE URL
if($_SERVER['REQUEST_METHOD'] == "GET") {
echo "
<html>
<head><title>Formulario Instalado.</title></head>
<body>
<font style='font-family: verdana, arial; font-size: 9pt;'>
<b>El formulario está instalado, aunque no se supone que lo llame directamente.</b></font><br>
</body></html>
";
exit();
}

// SET VARIABLES
$incoming_fields = array_keys($_POST);
$incoming_values = array_values($_POST);

if($override == "no") {
$incoming_mailto = $_POST['rec_mailto'];
$incoming_subject = $_POST['rec_subject'];
$incoming_thanks = $_POST['rec_thanks'];
}

$incoming_mailto_cc = $_POST['opt_mailto_cc'];
$incoming_mailto_bcc = $_POST['opt_mailto_bcc'];
$form_url = $_SERVER['HTTP_REFERER'];

// MAKE SURE FORM IS BEING RUN FROM THE RIGHT DOMAIN
if($secure_domain_on == "yes") {
$form_url_array = parse_url($form_url);
$form_domain = $form_url_array[host];
if($form_domain != $_SERVER[HTTP_HOST]) {
echo "<h2>ERROR</h2>
Usted está intentando usar un formulario en un dominio que no le corresponde!.
<br><br>";
$error = "yes";
}
}

// CHECK IF MAILTO IS SET
if($incoming_mailto == "") {
echo "<h2>ERROR - Faltan Destinatario</h2>
El formulario en <a href='$form_url'>$form_url</a> no funciona porque olvidó incluir la dirección
\"<b>rec_mailto</b>\" en el formulario. Este campo especifica a quien se le debe enviar el mail
de aviso.
<br><br>
Debería verse asi:<br>
&#060;input type=\"hidden\" name=\"rec_mailto\" value=\"tucorreo@tusitio.com\"&#062;
<br><br>
<br><br>
";
$error = "yes";
}

// CHECK IF SUBJECT IS SET
if($incoming_subject == "") {
echo "<h2>ERROR - Falta Tema</h2>
El formulario en <a href='$form_url'>$form_url</a> no funciona porque olvido especificar
\"<b>rec_subject</b>\" en el formulario. Este campo especifica el Tema del correo que será
enviado.
<br><br>
Deberia verse así:<br>
&#060;input type=\"hidden\" name=\"rec_subject\" value=\"Nuevo Correo DynaForm\"&#062;
<br><br>
<br><br>
";
$error = "yes";
}

// CHECK IF THANKS IS SET
if($incoming_thanks == "") {
echo "<h2>ERROR - Falta Gracias</h2>
El formulario en <a href='$form_url'>$form_url</a> no funciona porque no incluyó
\"<b>rec_thanks</b>\" en el formulario. Este campo especifica la página que se mostrará
al usuario una vez que envíe el formulario.
<br><br>
Deberia verse asi:<br>
&#060;input type=\"hidden\" name=\"rec_thanks\" value=\"thanks.html\"&#062;
<br><br>
<br><br>
";
$error = "yes";
}

// CHECK IF IP ADDRESS IS BANNED
if($ban_ip_on == "yes") {

if(strstr($ban_ip_list, $_SERVER[REMOTE_ADDR])) {
echo "<h2>ERROR - Banned IP</h2>
No puede usar este formulario, su IP fue limitado por el administrador.<br>
";
$error = "yes";
}
}


if($error == "yes") {
exit();
}

// SET EMAIL INTRODUCTION
$message .= "Mensaje desde 123.com\n IP Remitente:";
$message .= $_SERVER[REMOTE_ADDR];
$message .= "\n\n";

// LOAD EMAIL CONTENTS 
for ($i = 0; $i < count($incoming_fields); $i++) { 
if($incoming_fields[$i] != "rec_mailto") {
if($incoming_fields[$i] != "rec_subject") {
if($incoming_fields[$i] != "rec_thanks") {
if($incoming_fields[$i] != "opt_mailto_cc") {
if($incoming_fields[$i] != "opt_mailto_bcc") {

// CHECK FOR REQUIRED FIELDS IF ACTIVATED
if($required_on == "yes") {
$sub = substr($incoming_fields[$i], 0, 2);
if($sub == "r_") {
if($incoming_values[$i] == "" OR !isset($incoming_values[$i]) OR $incoming_values[$i] == " ") {
header("Location: $required_errorpage");
exit();
}}}

// ADD FIELD TO OUTGOING MESSAGE
$message .= "$incoming_fields[$i]:\n$incoming_values[$i]\n\n";
}}}}}}

// SET EMAIL FOOTER
$message .= "\n\nFIN DEL MENSAJE.\n www.123.com";

// CLEAR HEADERS
$headers = "";

// ADD FROM ADDRESS
if($from_address != "") {
$headers .= "From: $from_address\r\n";
}


// CHECK FOR CC OR BCC
if($incoming_mailto_cc != "") {
$headers .= "Cc: $incoming_mailto_cc\r\n";
}
if($incoming_mailto_bcc != "") {
$headers .= "Bcc: $incoming_mailto_bcc\r\n";
}

// SEND EMAIL
mail($incoming_mailto, $incoming_subject, $message, $headers);

// SEND AUTO-RESPONSE IF ACTIVATED
if($autorespond_on == "yes") {
$autorespond_mailto = $from_address;
$autorespond_headers = "From: webmaster@123.com";
$autorespond_subject = "Gracias por su mensaje";
$autorespond_message = "Hemos recibido el siguiente mensaje, y le responderemos a la brevedad \n";
$autorespond_message .= $message;
mail($autorespond_mailto, $autorespond_subject, $autorespond_message, $autorespond_headers);
}

// FORWARD TO THANK YOU PAGE
header("Location: $incoming_thanks"); 


?>