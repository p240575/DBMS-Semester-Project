<?php
require_once 'controllers/Controller.php';

class AuthController extends Controller {
    public function login() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $userModel = $this->model('User');
            $user = $userModel->login($_POST['email'], $_POST['password']);
            if ($user) {
                $_SESSION['user_id'] = $user['user_id'];
                $_SESSION['role'] = $user['role'];
                $_SESSION['user_name'] = $user['user_name'];
                if ($user['role'] === 'admin') {
                    header("Location: /admin");
                } else {
                    header("Location: /home");
                }
                exit;
            } else {
                $this->view('auth/login', ['error' => 'Invalid credentials']);
            }
        } else {
            $this->view('auth/login');
        }
    }

    public function register() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $userModel = $this->model('User');
            $addressData = [
                'address_line' => $_POST['address_line'],
                'city' => $_POST['city'],
                'province' => $_POST['province'],
                'zipcode' => substr($_POST['zipcode'] ?? '', 0, 5)
            ];
            $result = $userModel->register($_POST['name'], $_POST['email'], $_POST['phone'], $_POST['password'], $addressData);
            if ($result === true) {
                // Auto login user
                $user = $userModel->login($_POST['email'], $_POST['password']);
                if ($user) {
                    $_SESSION['user_id'] = $user['user_id'];
                    $_SESSION['role'] = $user['role'];
                    $_SESSION['user_name'] = $user['user_name'];
                    header("Location: /home");
                    exit;
                }
                header("Location: /auth/login");
                exit;
            } else {
                $this->view('auth/register', ['error' => 'Registration failed: ' . $result]);
            }
        } else {
            $this->view('auth/register');
        }
    }

    public function forgot() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $userModel = $this->model('User');
            $email = $_POST['email'];
            $newPassword = $_POST['new_password'];

            // Check if user exists
            $user = $userModel->findByEmail($email);
            if ($user) {
                if ($userModel->updatePassword($user['user_id'], $newPassword)) {
                    $this->view('auth/login', ['success' => 'Password reset successful! Please login with your new password.']);
                    exit;
                } else {
                    $this->view('auth/forgot', ['error' => 'An error occurred. Please try again.']);
                }
            } else {
                // Return error if email not found
                $this->view('auth/forgot', ['error' => 'No account found with that email address.']);
            }
        } else {
            $this->view('auth/forgot');
        }
    }

    public function logout() {
        session_destroy();
        header("Location: /home");
        exit;
    }
}
?>
