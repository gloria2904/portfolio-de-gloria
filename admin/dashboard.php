<?php
require_once '../config/database.php';
secureSession();

// Vérifier si l'admin est connecté
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: login.php');
    exit;
}

$pdo = getConnection();

// Gestion des actions (marquer comme lu, supprimer)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action']) && isset($_POST['message_id'])) {
        $messageId = (int)$_POST['message_id'];
        
        if ($_POST['action'] === 'mark_read') {
            $sql = "UPDATE contact_messages SET is_read = 1 WHERE id = :id";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([':id' => $messageId]);
        } elseif ($_POST['action'] === 'delete') {
            $sql = "DELETE FROM contact_messages WHERE id = :id";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([':id' => $messageId]);
        }
        
        header('Location: dashboard.php');
        exit;
    }
}

// Récupérer les statistiques
$sqlStats = "SELECT 
    COUNT(*) as total,
    SUM(CASE WHEN is_read = 0 THEN 1 ELSE 0 END) as unread,
    SUM(CASE WHEN is_read = 1 THEN 1 ELSE 0 END) as read
FROM contact_messages";
$stats = $pdo->query($sqlStats)->fetch();

// Récupérer tous les messages
$sqlMessages = "SELECT * FROM contact_messages ORDER BY created_at DESC";
$messages = $pdo->query($sqlMessages)->fetchAll();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin - Portfolio Gloria</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #f5f7fa;
            color: #333;
        }
        
        .header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 20px 40px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        
        .header h1 {
            font-size: 24px;
        }
        
        .header-right {
            display: flex;
            gap: 20px;
            align-items: center;
        }
        
        .username {
            font-size: 14px;
            opacity: 0.9;
        }
        
        .logout-btn {
            background: rgba(255,255,255,0.2);
            color: white;
            padding: 8px 16px;
            border: 1px solid rgba(255,255,255,0.3);
            border-radius: 6px;
            text-decoration: none;
            font-size: 14px;
            transition: all 0.3s;
        }
        
        .logout-btn:hover {
            background: rgba(255,255,255,0.3);
        }
        
        .container {
            max-width: 1200px;
            margin: 40px auto;
            padding: 0 20px;
        }
        
        .stats {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 40px;
        }
        
        .stat-card {
            background: white;
            padding: 25px;
            border-radius: 12px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
            border-left: 4px solid #667eea;
        }
        
        .stat-card.unread {
            border-left-color: #f59e0b;
        }
        
        .stat-card.read {
            border-left-color: #10b981;
        }
        
        .stat-number {
            font-size: 36px;
            font-weight: bold;
            color: #667eea;
            margin-bottom: 5px;
        }
        
        .stat-card.unread .stat-number {
            color: #f59e0b;
        }
        
        .stat-card.read .stat-number {
            color: #10b981;
        }
        
        .stat-label {
            color: #666;
            font-size: 14px;
        }
        
        .messages-section h2 {
            margin-bottom: 20px;
            font-size: 22px;
        }
        
        .message-card {
            background: white;
            padding: 20px;
            border-radius: 12px;
            margin-bottom: 15px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
            transition: transform 0.2s;
        }
        
        .message-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        }
        
        .message-card.unread {
            border-left: 4px solid #f59e0b;
            background: #fffbeb;
        }
        
        .message-header {
            display: flex;
            justify-content: space-between;
            align-items: start;
            margin-bottom: 15px;
        }
        
        .message-info h3 {
            font-size: 18px;
            margin-bottom: 5px;
        }
        
        .message-email {
            color: #667eea;
            font-size: 14px;
            margin-bottom: 5px;
        }
        
        .message-date {
            color: #999;
            font-size: 13px;
        }
        
        .message-actions {
            display: flex;
            gap: 10px;
        }
        
        .btn {
            padding: 6px 12px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-size: 13px;
            transition: all 0.2s;
        }
        
        .btn-read {
            background: #10b981;
            color: white;
        }
        
        .btn-read:hover {
            background: #059669;
        }
        
        .btn-delete {
            background: #ef4444;
            color: white;
        }
        
        .btn-delete:hover {
            background: #dc2626;
        }
        
        .message-content {
            background: #f9fafb;
            padding: 15px;
            border-radius: 8px;
            line-height: 1.6;
            color: #555;
        }
        
        .badge {
            display: inline-block;
            padding: 4px 10px;
            border-radius: 12px;
            font-size: 12px;
            font-weight: 600;
        }
        
        .badge-unread {
            background: #fef3c7;
            color: #92400e;
        }
        
        .badge-read {
            background: #d1fae5;
            color: #065f46;
        }
        
        .empty-state {
            text-align: center;
            padding: 60px 20px;
            color: #999;
        }
        
        .empty-state svg {
            width: 80px;
            height: 80px;
            margin-bottom: 20px;
            opacity: 0.3;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>📊 Dashboard Admin</h1>
        <div class="header-right">
            <span class="username">👤 <?php echo htmlspecialchars($_SESSION['admin_username']); ?></span>
            <a href="logout.php" class="logout-btn">Déconnexion</a>
        </div>
    </div>
    
    <div class="container">
        <div class="stats">
            <div class="stat-card">
                <div class="stat-number"><?php echo $stats['total']; ?></div>
                <div class="stat-label">Total des messages</div>
            </div>
            
            <div class="stat-card unread">
                <div class="stat-number"><?php echo $stats['unread']; ?></div>
                <div class="stat-label">Messages non lus</div>
            </div>
            
            <div class="stat-card read">
                <div class="stat-number"><?php echo $stats['read']; ?></div>
                <div class="stat-label">Messages lus</div>
            </div>
        </div>
        
        <div class="messages-section">
            <h2>📬 Messages de contact</h2>
            
            <?php if (empty($messages)): ?>
                <div class="empty-state">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                    </svg>
                    <p>Aucun message pour le moment</p>
                </div>
            <?php else: ?>
                <?php foreach ($messages as $msg): ?>
                    <div class="message-card <?php echo $msg['is_read'] ? '' : 'unread'; ?>">
                        <div class="message-header">
                            <div class="message-info">
                                <h3><?php echo htmlspecialchars($msg['name']); ?>
                                    <span class="badge <?php echo $msg['is_read'] ? 'badge-read' : 'badge-unread'; ?>">
                                        <?php echo $msg['is_read'] ? 'Lu' : 'Non lu'; ?>
                                    </span>
                                </h3>
                                <div class="message-email"><?php echo htmlspecialchars($msg['email']); ?></div>
                                <div class="message-date">
                                    <?php echo date('d/m/Y à H:i', strtotime($msg['created_at'])); ?>
                                </div>
                            </div>
                            
                            <div class="message-actions">
                                <?php if (!$msg['is_read']): ?>
                                    <form method="POST" style="display: inline;">
                                        <input type="hidden" name="message_id" value="<?php echo $msg['id']; ?>">
                                        <input type="hidden" name="action" value="mark_read">
                                        <button type="submit" class="btn btn-read">✓ Marquer lu</button>
                                    </form>
                                <?php endif; ?>
                                
                                <form method="POST" style="display: inline;" onsubmit="return confirm('Supprimer ce message ?');">
                                    <input type="hidden" name="message_id" value="<?php echo $msg['id']; ?>">
                                    <input type="hidden" name="action" value="delete">
                                    <button type="submit" class="btn btn-delete">🗑 Supprimer</button>
                                </form>
                            </div>
                        </div>
                        
                        <div class="message-content">
                            <?php echo nl2br(htmlspecialchars($msg['message'])); ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>s