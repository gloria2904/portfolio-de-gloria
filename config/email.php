<?php
// Configuration de l'email avec PHPMailer

// Informations du serveur SMTP
define('SMTP_HOST', 'smtp.gmail.com');        // Serveur SMTP (Gmail, Outlook, etc.)
define('SMTP_PORT', 587);                      // Port (587 pour TLS, 465 pour SSL)
define('SMTP_SECURE', 'tls');                  // Encryption: 'tls' ou 'ssl'
define('SMTP_AUTH', true);                     // Activer l'authentification SMTP

// Identifiants de connexion
define('SMTP_USERNAME', 'votre.email@gmail.com');  // ← CHANGEZ ICI
define('SMTP_PASSWORD', 'votre_mot_de_passe_app'); // ← CHANGEZ ICI

// Informations de l'expéditeur
define('EMAIL_FROM', 'votre.email@gmail.com');     // ← CHANGEZ ICI
define('EMAIL_FROM_NAME', 'Portfolio Gloria');

// Email de destination (où vous recevez les messages)
define('EMAIL_TO', 'gloria@example.com');          // ← CHANGEZ ICI
define('EMAIL_TO_NAME', 'Gloria');

// Options
define('EMAIL_REPLY_TO_VISITOR', true);
define('EMAIL_CHARSET', 'UTF-8');
?>