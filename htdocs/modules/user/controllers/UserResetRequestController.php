<?php
namespace App\Modules\User\Controllers;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use App\TokenManager;
use App\DB;
class UserResetRequestController
{
    public function index()
    {
        global $config;
        $error = '';
        $success = '';
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $tm = new TokenManager();
            $result = $tm->verify($_POST['token'] ?? '');
            if ($result['status'] !== 'success') {
                $error = 'Invalid CSRF token. Please refresh and try again.';
            } else {
                $email = trim($_POST['email'] ?? '');
                if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                    $error = 'Invalid email address.';
                } else {
                    $db = new DB($config);
                    // Find user by email
                    $sql = "SELECT id FROM users WHERE email = ?";
                    $rows = $db->fetchAll($sql, [$email]);
                    error_log('User lookup: ' . print_r($rows, true));
                    if (count($rows) > 0) {
                        $userId = $rows[0]['id'];
                        $token = bin2hex(random_bytes(32));
                        $expiry = date('Y-m-d H:i:s', strtotime('+1 hour'));
                        // Insert token into reset table
                        $sql = "INSERT INTO reset (user_id, `key`, expiry_date) VALUES (?, ?, ?)";
                        $db->query($sql, [$userId, $token, $expiry]);
                        error_log('Reset insert: ' . print_r([$userId, $token, $expiry], true));
                        // Send email with reset link using PHPMailer
                        $mail = new PHPMailer(true);
                        try {
                            $mail->isSMTP();
                            $mail->Host = $config['mail']['host'];
                            $mail->SMTPAuth = true;
                            $mail->Username = $config['mail']['username'];
                            $mail->Password = $config['mail']['password'];
                            $mail->SMTPSecure = $config['mail']['encryption'];
                            $mail->Port = $config['mail']['port'];
                            $mail->setFrom($config['mail']['from_email'], $config['site_name']);
                            $mail->addAddress($email);
                            $mail->Subject = 'Password Reset Request';
                            $resetLink = $config['base_url'] . "/user/reset?token=$token";
                            $mail->Body = "Click the following link to reset your password: $resetLink\nIf you did not request this, please ignore.";
                            $mail->send();
                        } catch (Exception $e) {
                            error_log('Mail error: ' . $mail->ErrorInfo);
                            $error = 'Email failed: ' . $mail->ErrorInfo;
                        }
                    } else {
                        error_log('No user found for email: ' . $email);
                    }
                    // Always show generic success message
                    $success = 'If your email is registered, a reset link has been sent.';
                }
            }
        }
        include __DIR__ . '/../views/reset_request.php';
    }
}
