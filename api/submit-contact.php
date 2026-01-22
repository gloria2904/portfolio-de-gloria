<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');

require_once '../config/database.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Méthode non autorisée']);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);

$name = isset($data['name']) ? trim($data['name']) : '';
$email = isset($data['email']) ? trim($data['email']) : '';
$message = isset($data['message']) ? trim($data['message']) : '';

if (empty($name) || empty($email) || empty($message)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Tous les champs sont requis']);
    exit;
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Email invalide']);
    exit;
}

try {
    $pdo = getConnection();
    
    $sql = "INSERT INTO contact_messages (name, email, message, is_read, created_at) 
            VALUES (:name, :email, :message, 0, NOW())";
    $stmt = $pdo->prepare($sql);
    
    $result = $stmt->execute([
        ':name' => htmlspecialchars($name, ENT_QUOTES, 'UTF-8'),
        ':email' => htmlspecialchars($email, ENT_QUOTES, 'UTF-8'),
        ':message' => htmlspecialchars($message, ENT_QUOTES, 'UTF-8')
    ]);
    
    if ($result) {
        echo json_encode([
            'success' => true,
            'message' => 'Message enregistré avec succès ! Je vous répondrai bientôt.'
        ]);
    } else {
        throw new Exception('Erreur lors de l\'enregistrement');
    }
    
} catch (Exception $e) {
    error_log("Erreur: " . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Erreur serveur: ' . $e->getMessage()
    ]);
}
?>