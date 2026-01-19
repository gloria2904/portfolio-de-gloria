<?php
header('Content-Type: application/json');
require_once '../config/database.php';
require_once '../config/email.php';

// Charger PHPMailer (installation manuelle)
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require '../vendor/PHPMailer/src/Exception.php';
require '../vendor/PHPMailer/src/PHPMailer.php';
require '../vendor/PHPMailer/src/SMTP.php';

// Vérifier que c'est une requête POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Méthode non autorisée']);
    exit;
}

// Récupérer les données JSON
$data = json_decode(file_get_contents('php://input'), true);

// Validation des données
$name = isset($data['name']) ? trim($data['name']) : '';
$email = isset($data['email']) ? trim($data['email']) : '';
$message = isset($data['message']) ? trim($data['message']) : '';

$errors = [];

if (empty($name)) {
    $errors[] = 'Le nom est requis';
} elseif (strlen($name) < 2) {
    $errors[] = 'Le nom doit contenir au moins 2 caractères';
}

if (empty($email)) {
    $errors[] = 'L\'email est requis';
} elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errors[] = 'L\'email n\'est pas valide';
}

if (empty($message)) {
    $errors[] = 'Le message est requis';
} elseif (strlen($message) < 10) {
    $errors[] = 'Le message doit contenir au moins 10 caractères';
}

// Si erreurs, retourner
if (!empty($errors)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'errors' => $errors]);
    exit;
}

try {
    // 1. ENREGISTRER DANS LA BASE DE DONNÉES
    $pdo = getConnection();
    
    $sql = "INSERT INTO contact_messages (name, email, message) VALUES (:name, :email, :message)";
    $stmt = $pdo->prepare($sql);
    
    $result = $stmt->execute([
        ':name' => htmlspecialchars($name, ENT_QUOTES, 'UTF-8'),
        ':email' => htmlspecialchars($email, ENT_QUOTES, 'UTF-8'),
        ':message' => htmlspecialchars($message, ENT_QUOTES, 'UTF-8')
    ]);
    
    if (!$result) {
        throw new Exception('Erreur lors de l\'enregistrement en base de données');
    }
    
    // 2. ENVOYER L'EMAIL AVEC PHPMAILER
    $mail = new PHPMailer(true);
    
    try {
        // Configuration du serveur SMTP
        $mail->isSMTP();
        $mail->Host       = SMTP_HOST;
        $mail->SMTPAuth   = SMTP_AUTH;
        $mail->Username   = SMTP_USERNAME;
        $mail->Password   = SMTP_PASSWORD;
        $mail->SMTPSecure = SMTP_SECURE;
        $mail->Port       = SMTP_PORT;
        $mail->CharSet    = EMAIL_CHARSET;
        
        // Désactiver la vérification SSL (utile en développement local)
        $mail->SMTPOptions = array(
            'ssl' => array(
                'verify_peer' => false,
                'verify_peer_name' => false,
                'allow_self_signed' => true
            )
        );
        
        // Expéditeur et destinataire
        $mail->setFrom(EMAIL_FROM, EMAIL_FROM_NAME);
        $mail->addAddress(EMAIL_TO, EMAIL_TO_NAME);
        
        // Permettre de répondre directement au visiteur
        if (EMAIL_REPLY_TO_VISITOR) {
            $mail->addReplyTo($email, $name);
        }
        
        // Contenu de l'email
        $mail->isHTML(true);
        $mail->Subject = "Nouveau message de contact - Portfolio";
        
        // Template HTML de l'email
        $mail->Body = "
        <!DOCTYPE html>
        <html>
        <head>
            <style>
                body { font-family: Arial, sans-serif; background-color: #f4f4f4; padding: 20px; }
                .container { background-color: white; padding: 30px; border-radius: 10px; max-width: 600px; margin: 0 auto; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
                .header { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 20px; border-radius: 8px 8px 0 0; margin: -30px -30px 20px -30px; }
                .header h1 { margin: 0; font-size: 24px; }
                .field { margin-bottom: 20px; }
                .label { font-weight: bold; color: #555; margin-bottom: 5px; }
                .value { background: #f9f9f9; padding: 12px; border-radius: 5px; border-left: 3px solid #667eea; }
                .footer { margin-top: 30px; padding-top: 20px; border-top: 1px solid #eee; color: #999; font-size: 12px; text-align: center; }
            </style>
        </head>
        <body>
            <div class='container'>
                <div class='header'>
                    <h1>📬 Nouveau message de contact</h1>
                </div>
                
                <div class='field'>
                    <div class='label'>👤 Nom :</div>
                    <div class='value'>" . htmlspecialchars($name) . "</div>
                </div>
                
                <div class='field'>
                    <div class='label'>📧 Email :</div>
                    <div class='value'>" . htmlspecialchars($email) . "</div>
                </div>
                
                <div class='field'>
                    <div class='label'>💬 Message :</div>
                    <div class='value'>" . nl2br(htmlspecialchars($message)) . "</div>
                </div>
                
                <div class='footer'>
                    Message reçu le " . date('d/m/Y à H:i') . " depuis votre portfolio
                </div>
            </div>
        </body>
        </html>
        ";
        
        // Version texte (alternative)
        $mail->AltBody = "Nouveau message de contact\n\n" .
                        "Nom: $name\n" .
                        "Email: $email\n\n" .
                        "Message:\n$message\n\n" .
                        "Reçu le " . date('d/m/Y à H:i');
        
        // Envoyer l'email
        $mail->send();
        
        echo json_encode([
            'success' => true,
            'message' => 'Votre message a été envoyé avec succès !'
        ]);
        
    } catch (Exception $e) {
        // Email non envoyé mais sauvegardé en BDD
        error_log("Erreur PHPMailer: " . $mail->ErrorInfo);
        echo json_encode([
            'success' => true,
            'message' => 'Votre message a été enregistré mais l\'email n\'a pas pu être envoyé. Erreur: ' . $mail->ErrorInfo,
            'email_error' => true
        ]);
    }
    
} catch (Exception $e) {
    error_log("Erreur submit contact: " . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Une erreur est survenue. Veuillez réessayer plus tard.'
    ]);
}
?>