<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

// Database configuration
define('DB_HOST', 'localhost');
define('DB_NAME', 'telegram_verify');
define('DB_USER', 'root');
define('DB_PASS', '');

// Connect to database
function getDB() {
    try {
        $pdo = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PASS);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        return $pdo;
    } catch(PDOException $e) {
        die("Connection failed: " . $e->getMessage());
    }
}

// Create table if not exists
function createTable() {
    $pdo = getDB();
    $sql = "CREATE TABLE IF NOT EXISTS verify_config (
        id INT PRIMARY KEY AUTO_INCREMENT,
        config_key VARCHAR(50) UNIQUE,
        config_value TEXT,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    )";
    $pdo->exec($sql);
}

// Get configuration
function getConfig() {
    createTable();
    $pdo = getDB();
    $stmt = $pdo->query("SELECT config_key, config_value FROM verify_config");
    $config = [];
    
    while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $config[$row['config_key']] = json_decode($row['config_value'], true);
    }
    
    return $config;
}

// Save configuration
function saveConfig($key, $value) {
    createTable();
    $pdo = getDB();
    
    // Check if key exists
    $stmt = $pdo->prepare("SELECT id FROM verify_config WHERE config_key = ?");
    $stmt->execute([$key]);
    
    if($stmt->rowCount() > 0) {
        // Update
        $stmt = $pdo->prepare("UPDATE verify_config SET config_value = ? WHERE config_key = ?");
        $stmt->execute([json_encode($value), $key]);
    } else {
        // Insert
        $stmt = $pdo->prepare("INSERT INTO verify_config (config_key, config_value) VALUES (?, ?)");
        $stmt->execute([$key, json_encode($value)]);
    }
    
    return true;
}

// Handle request
$action = $_GET['action'] ?? '';

switch($action) {
    case 'getConfig':
        $config = getConfig();
        echo json_encode(['success' => true, 'config' => $config]);
        break;
        
    case 'saveConfig':
        if($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = json_decode(file_get_contents('php://input'), true);
            
            if(isset($data['key']) && isset($data['value'])) {
                saveConfig($data['key'], $data['value']);
                echo json_encode(['success' => true]);
            } else {
                echo json_encode(['success' => false, 'error' => 'Missing key or value']);
            }
        }
        break;
        
    default:
        echo json_encode(['success' => false, 'error' => 'Invalid action']);
}
?>
