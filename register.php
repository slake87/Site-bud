<?php
header("Content-Type: application/json");

// Параметри підключення до MSSQL
$serverName = "WIN-COREURL4NB2\\MSSQLSERVER01";
$database = "form_data";
$username = "ваш_логін";
$password = "ваш_пароль";

// Отримуємо вхідні дані
$data = json_decode(file_get_contents("php://input"), true);

if (!$data || !isset($data["username"], $data["email"], $data["password"])) {
    echo json_encode(["success" => false, "message" => "Невірні дані"]);
    exit;
}

try {
    // Підключення до MSSQL
    $conn = new PDO("sqlsrv:Server=$serverName;Database=$database", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Хешування пароля
    $passwordHash = password_hash($data["password"], PASSWORD_DEFAULT);

    // Підготовка SQL-запиту
    $stmt = $conn->prepare("INSERT INTO dbo.Users 
        (username, password_hash, email, role, company, contact_info, created_at, updated_at)
        VALUES (:username, :password_hash, :email, NULL, NULL, NULL, GETDATE(), GETDATE())");

    // Прив'язка значень
    $stmt->bindParam(':username', $data["username"]);
    $stmt->bindParam(':password_hash', $passwordHash);
    $stmt->bindParam(':email', $data["email"]);

    $stmt->execute();

    echo json_encode(["success" => true]);

} catch (PDOException $e) {
    echo json_encode(["success" => false, "message" => "Помилка бази: " . $e->getMessage()]);
}
?>
