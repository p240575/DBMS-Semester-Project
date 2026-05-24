<?php
class User {
    private $conn;

    public function __construct() {
        require_once 'config/database.php';
        $db = new Database();
        $this->conn = $db->getConnection();
    }

    public function login($loginId, $password) {
        // First check if it's the admin trying to log in
        $stmt = $this->conn->prepare("SELECT * FROM Admins WHERE username = :loginId OR email = :loginId");
        $stmt->execute(['loginId' => $loginId]);
        $admin = $stmt->fetch();
        if ($admin && password_verify($password, $admin['password'])) {
            $admin['role'] = 'admin';
            $admin['user_id'] = $admin['admin_id']; // For compatibility with session checks
            $admin['user_name'] = $admin['username'];
            return $admin;
        }

        // Otherwise check the standard customers table
        $stmt2 = $this->conn->prepare("SELECT * FROM Users WHERE email = :loginId");
        $stmt2->execute(['loginId' => $loginId]);
        $user = $stmt2->fetch();
        if ($user && password_verify($password, $user['password'])) {
            $user['role'] = 'customer';
            return $user;
        }
        return false;
    }

    public function register($name, $email, $phone, $password, $addressData) {
        try {
            $this->conn->beginTransaction();
            
            $stmt = $this->conn->prepare("INSERT INTO Users (user_name, email, phone, password) VALUES (:n, :e, :p, :pw)");
            $hash = password_hash($password, PASSWORD_BCRYPT);
            $stmt->execute(['n' => $name, 'e' => $email, 'p' => $phone, 'pw' => $hash]);
            $user_id = $this->conn->lastInsertId();

            $stmtAddr = $this->conn->prepare("INSERT INTO Addresses (user_id, full_name, phone, address_line, city, province, zipcode, is_default) VALUES (:uid, :n, :p, :addr, :city, :prov, :zip, 1)");
            $stmtAddr->execute([
                'uid' => $user_id,
                'n' => $name,
                'p' => $phone,
                'addr' => $addressData['address_line'],
                'city' => $addressData['city'],
                'prov' => $addressData['province'],
                'zip' => $addressData['zipcode']
            ]);

            $this->conn->commit();
            return true;
        } catch (PDOException $e) {
            $this->conn->rollBack();
            return $e->getMessage();
        }
    }

    public function findByEmail($email) {
        $stmt = $this->conn->prepare("SELECT * FROM Users WHERE email = :email");
        $stmt->execute(['email' => $email]);
        return $stmt->fetch();
    }

    public function updatePassword($user_id, $newPassword) {
        try {
            $hash = password_hash($newPassword, PASSWORD_BCRYPT);
            $stmt = $this->conn->prepare("UPDATE Users SET password = :pw WHERE user_id = :uid");
            return $stmt->execute(['pw' => $hash, 'uid' => $user_id]);
        } catch (PDOException $e) {
            return false;
        }
    }
}
?>
