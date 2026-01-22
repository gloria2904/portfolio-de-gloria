<?php
// Configuration de l'email avec PHPMailer

// Informations du serveur SMTP
define('SMTP_HOST', 'smtp.gmail.com');        // Serveur SMTP (Gmail, Outlook, etc.)
define('SMTP_PORT', 587);                      // Port (587 pour TLS, 465 pour SSL)
define('SMTP_SECURE', 'tls');                  // Encryption: 'tls' ou 'ssl'
define('SMTP_AUTH', true);                     // Activer l'authentification SMTP

// Identifiants de connexion
define('SMTP_USERNAME', 'gwedgloria@gmail.com');  
define('SMTP_PASSWORD', '1505@mimi'); 

// Informations de l'expéditeur
define('EMAIL_FROM', 'gwedgloria@gmail.com');     
define('EMAIL_FROM_NAME', 'Portfolio Gloria');

// Email de destination (où vous recevez les messages)
define('EMAIL_TO', 'gwedgloria@gmail.com');          
define('EMAIL_TO_NAME', 'Gloria');

// Options
define('EMAIL_REPLY_TO_VISITOR', true);
define('EMAIL_CHARSET', 'UTF-8');
?>