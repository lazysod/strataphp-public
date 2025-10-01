<?php
namespace App\Modules\Contact\Controllers;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use App\App;
use App\Token;

/**
 * Contact Form Controller
 * 
 * Handles contact form display and submission processing.
 * Manages CSRF protection, form validation, and email sending.
 * 
 * @package App\Modules\Contact\Controllers
 * @author  StrataPHP Framework
 * @version 1.0.0
 */
class ContactFormController
{
    /**
     * Display the contact form
     * 
     * Renders the contact form with CSRF token protection.
     * Sets up the page title and includes the contact form view.
     * 
     * @return void
     */
    public function index()
    {
        $page_title = 'Contact Us';
        if (empty($_SESSION[PREFIX . 'csrf_token'])) {
            $_SESSION[PREFIX . 'csrf_token'] = Token::generate(32);
        }
        $csrf_token = $_SESSION[PREFIX . 'csrf_token'];
        include __DIR__ . '/../views/contact_form.php';
    }
    
    /**
     * Process contact form submission
     * 
     * Validates form data, sends email via PHPMailer, and handles responses.
     * Includes CSRF protection and comprehensive input validation.
     * 
     * @return void
     */
    public function submit()
    {
        $page_title = 'Contact Us';
        $success = false;
        $error = '';
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $tokenValid = isset($_POST['csrf_token']) && isset($_SESSION[PREFIX . 'csrf_token']) && hash_equals($_SESSION[PREFIX . 'csrf_token'], $_POST['csrf_token']);
            if (!$tokenValid) {
                $error = 'Invalid CSRF token.';
            } else {
                $name = trim($_POST['name'] ?? '');
                $email = trim($_POST['email'] ?? '');
                $phone = trim($_POST['phone'] ?? '');
                $message = trim($_POST['message'] ?? '');
                $ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
                
                // Enhanced validation
                if (empty($name) || strlen($name) < 2 || strlen($name) > 100) {
                    $error = 'Name must be between 2 and 100 characters.';
                } elseif (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
                    $error = 'Please provide a valid email address.';
                } elseif (!empty($phone) && !preg_match('/^[\+\d\s\-\(\)\.]+$/', $phone)) {
                    $error = 'Please provide a valid phone number.';
                } elseif (empty($message) || strlen($message) < 10 || strlen($message) > 2000) {
                    $error = 'Message must be between 10 and 2000 characters.';
                } else {
                    $mail = new PHPMailer(true);
                    try {
                        $mail->isSMTP();
                        $mailConfig = App::config('mail');
                        $mail->Host = $mailConfig['host'];
                        $mail->SMTPAuth = true;
                        $mail->Username = $mailConfig['username'];
                        $mail->Password = $mailConfig['password'];
                        $mail->SMTPSecure = $mailConfig['encryption'];
                        $mail->Port = $mailConfig['port'];
                        $mail->setFrom(App::config('admin_email'), $name);
                        $mail->addAddress(App::config('form_email'));
                        $mail->Subject = 'Contact Form Submission';
                        $mail->Body = "Name: $name\nEmail: $email\nPhone: $phone\nIP: $ip\nMessage:\n$message";
                        $mail->send();
                        $success = true;
                    } catch (Exception $e) {
                        $error = 'Mailer Error: ' . $mail->ErrorInfo;
                    }
                }
            }
        }
        include __DIR__ . '/../views/contact_form.php';
    }
}
