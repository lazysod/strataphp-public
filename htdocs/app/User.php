<?php
namespace App;
use App\Logger;
use PHPMailer\PHPMailer\PHPMailer;
class User
{
    private $db;
    private $config;
    private $logger;

    // Delete user by ID (admin panel)
    public function deleteUser($id)
    {
        $sql = "DELETE FROM users WHERE id = ?";
        return $this->db->query($sql, [(int)$id]);
    }

    // suspend
    public function suspend($id)
    {
        $sql = "UPDATE users SET active = 0, dead_switch = 1 WHERE id = ?";
        return $this->db->query($sql, [(int)$id]);
    }

    // unsuspend
    public function unsuspend($id)
    {
        $sql = "UPDATE users SET active = 1, dead_switch = 0 WHERE id = ?";
        return $this->db->query($sql, [(int)$id]);
    }

    // Create user (admin panel)
    public function createUser($data)
    {
        $fields = [];
        $placeholders = [];
        $params = [];
        // Always add security_hash
        $data['security_hash'] = bin2hex(random_bytes(16));
        foreach ($data as $key => $value) {
            $fields[] = $key;
            $placeholders[] = '?';
            // Hash password if field is pwd
            if ($key === 'pwd') {
                $params[] = $this->make_pass($value);
            } else {
                $params[] = $value;
            }
        }
        $sql = "INSERT INTO users (" . implode(", ", $fields) . ") VALUES (" . implode(", ", $placeholders) . ")";
        return $this->db->query($sql, $params);
    }
    // Update user by ID (admin panel, flexible fields)
    public function updateUser($id, $data)
    {
        if (empty($data) || !$id) {
            return false;
        }
        $fields = [];
        $params = [];
        foreach ($data as $key => $value) {
            $fields[] = "$key = ?";
            $params[] = $value;
        }
        $params[] = (int)$id;
        $sql = "UPDATE users SET " . implode(", ", $fields) . " WHERE id = ?";
        return $this->db->query($sql, $params);
    }
    // Fetch a user by ID
    public function getById($id)
    {
        $sql = "SELECT * FROM users WHERE id = ?";
        $rows = $this->db->fetchAll($sql, [(int)$id]);
        return $rows ? $rows[0] : null;
    }
    // Count all users (for pagination)
    public function countAll()
    {
        $sql = "SELECT COUNT(*) as total FROM users";
        $row = $this->db->fetch($sql);
        return $row ? (int)$row['total'] : 0;
    }

    // Get paginated users (for admin list)
    public function getPaginated($limit, $offset)
    {
        $limit = (int)$limit;
        $offset = (int)$offset;
        $sql = "SELECT * FROM users ORDER BY id DESC LIMIT $limit OFFSET $offset";
        return $this->db->fetchAll($sql);
    }

    // Simple logger using the framework's Logger class (logs to storage/logs/app.log)
    private function log($message, $context = [])
    {
        if (!isset($this->logger)) {
            $this->logger = new Logger($this->config);
        }
        $this->logger->info($message, $context);
    }

    public function __construct($db, $config)
    {
        $this->db = $db;
        $this->config = $config;
    }

    /**
     * Request a password reset for a user (or admin if $adminOnly is true)
     * Returns ['status' => 'success'|'fail', 'message' => string]
     */
    public function requestPasswordReset($email, $baseUrl, $adminOnly = false)
    {
        $where = $adminOnly ? ' AND is_admin = 1' : '';
        $sql = "SELECT id FROM users WHERE email = ?$where";
        $rows = $this->db->fetchAll($sql, [$email]);
        if (count($rows) > 0) {
            $userId = $rows[0]['id'];
            $token = bin2hex(random_bytes(32));
            $expiry = date('Y-m-d H:i:s', strtotime('+1 hour'));
            // Insert or update token in reset table (user resets)
            $this->db->query("INSERT INTO reset (user_id, `key`, expiry_date) VALUES (?, ?, ?) ON DUPLICATE KEY UPDATE `key` = VALUES(`key`), expiry_date = VALUES(expiry_date)", [$userId, $token, $expiry]);
            $resetLink = $baseUrl . "/user/reset?token=$token";
            // You can send the email here or return the link for the controller to handle
            return [
                'status' => 'success',
                'message' => $resetLink,
                'token' => $token,
                'user_id' => $userId
            ];
        } else {
            return [
                'status' => 'fail',
                'message' => $adminOnly ? 'No admin user found with that email.' : 'No user found with that email.'
            ];
        }
    }

    // htdocs/app/class/User.php
    // Modernized User class for the new framework (PDO, config injected)

    public function generate_session()
    {
        $session_array = array(
            'session_id' => session_create_id(),
            'session_expire' => date('Y-m-d H:i:s', strtotime('+1 hour')),
        );
        $_SESSION[PREFIX . 'session'] = $session_array;
    }

    public function session_check()
    {
        if (isset($_SESSION[PREFIX . 'session'])) {
            $now = date('Y-m-d H:i:s');
            if ($now >= $_SESSION[PREFIX . 'session']['session_expire']) {
                if (session_status() === PHP_SESSION_NONE) {
                    session_start();
                }
                session_destroy();
                unset($_COOKIE[PREFIX . '_cookie_login']);
                header('location: ' . $this->config['base_url'] . '/user/login');
                exit;
            }
        }
    }

    public function cookie_check()
    {
        if (isset($_COOKIE[PREFIX . 'cookie_login'])) {
            if (!isset($_SESSION[PREFIX . 'user_id'])) {
                $oldCookie = $_COOKIE[PREFIX . 'cookie_login'];
                $sql = "SELECT * FROM `cookie_login` WHERE `cookie_hash`=?";
                $rows = $this->db->fetchAll($sql, [$oldCookie]);
                if (count($rows) > 0) {
                    foreach ($rows as $row) {
                        $userId = $row['user_id'];
                        $loginID = $row['id'];
                        $sql2 = "SELECT * FROM `users` WHERE `id`=?";
                        $users = $this->db->fetchAll($sql2, [$userId]);
                        // APP::dump($users);
                        // die();
                        foreach ($users as $row2) {
                            if ($row2['dead_switch'] > 0) {
                                return [
                                    'status' => 'fail',
                                    'message' => 'Your account was closed or is inaccessible.'
                                ];
                            }
                            $rank = $this->get_rank($row2['id']);
                            $_SESSION[PREFIX . 'rank_title'] = $rank['title'];
                            $_SESSION[PREFIX . 'rank_level'] = $rank['level'];
                            if ($rank['admin'] > 0) {
                                $_SESSION[PREFIX . 'admin'] = $rank['admin'];
                            }
                            $_SESSION[PREFIX . 'display_name'] = $row2['display_name'];
                            $_SESSION[PREFIX . 'email'] = $row2['email'];
                            $_SESSION[PREFIX . 'first_name'] = $row2['first_name'];
                            $_SESSION[PREFIX . 'second_name'] = $row2['second_name'];
                            $_SESSION[PREFIX . 'user_id'] = $userId;
                            $_SESSION[PREFIX . 'rank_title'] = $rank['title'];
                            $_SESSION[PREFIX . 'rank_level'] = $rank['level'];
                            $_SESSION[PREFIX . 'admin'] = $rank['admin'];
                            $_SESSION[PREFIX . 'sec_hash'] = $row2['security_hash'];
                            $_SESSION[PREFIX . 'avatar'] = $this->gravatar($_SESSION[PREFIX . 'email']);
                            $_SESSION[PREFIX . 'last_log'] = $row2['last_access'];
                            $_SESSION[PREFIX . 'user'] = [
                                'id' => $row2['id'],
                                'display_name' => $row2['display_name'],
                                // 'first_name' => $row2['first_name'],
                                // 'second_name' => $row2['second_name'],
                                'email' => $row2['email'],
                                'is_admin' => ($rank['admin'] > 0 ? 1 : 0),
                                'rank_title' => $rank['title'],
                                'rank_level' => $rank['level'],
                                'avatar' => $this->gravatar($row2['email'])
                            ];
                        }
                    }
                    $time = time() + 60 * 60 * 24 * 7;
                    $salt = md5(uniqid(rand(), true));
                    $hash = sha1(rand(1, 1000) . $salt);
                    setcookie(
                        PREFIX . 'cookie_login',
                        $hash,
                        [
                            'expires' => $time,
                            'path' => '/',
                            'secure' => isset($_SERVER['HTTPS']),
                            'httponly' => true,
                            'samesite' => 'Lax'
                        ]
                    );
                    $update1 = "UPDATE `cookie_login` SET `cookie_hash`=? WHERE `id`=?";
                    $this->db->query($update1, [$hash, $loginID]);
                    $update2 = "UPDATE `users` SET `last_access`=? WHERE `id`=?";
                    $now = date('Y-m-d H:i:s');
                    $this->db->query($update2, [$now, $_SESSION[PREFIX . 'user_id']]);
                }
            }
        }
    }

    public function gravatar($email)
    {
        return 'https://www.gravatar.com/avatar/' . md5(strtolower(trim($email))) . '?s=80&r=g&d=mm';
    }

    public function make_pass($pass)
    {
        $dbCost = array('cost' => 12);
        $pwdEncrypted = password_hash($pass, PASSWORD_BCRYPT, $dbCost);
        return $pwdEncrypted;
    }

    public function register($userInfo)
    {
        $dbCost = array('cost' => 12);

        $email = $userInfo['email'];
        $pass = $userInfo['pwd'];
        $pass2 = $userInfo['confirm_pwd'];
        $fName = $userInfo['first_name'];
        $sName = $userInfo['second_name'];
        if (empty($fName) || empty($sName) || empty($email) || empty($pass) || empty($pass2)) {
            return [
                'status' => 'fail',
                'message' => 'Please fill in all fields.',
            ];
        }
        if ($pass !== $pass2) {
            return [
                'status' => 'fail',
                'message' => 'Passwords do not match.',
            ];
        }
        $pwdEncrypted = password_hash($pass, PASSWORD_BCRYPT, $dbCost);
        $hash = md5(date('d-m-Y H:i:s'));
        $sql = "SELECT * FROM users WHERE email = ?";
        $rows = $this->db->fetchAll($sql, [$email]);
        if (count($rows) > 0) {
            return [
                'status' => 'fail',
                'message' => 'That email is currently in use. Please use another email address.',
            ];
        } else {
            // Insert user with active=0
            $sql = "INSERT INTO users (id, first_name, second_name, email, pwd, security_hash, active) VALUES (NULL, ?, ?, ?, ?, ?, 0)";
            $this->db->query($sql, [$fName, $sName, $email, $pwdEncrypted, $hash]);
            $userId = $this->db->insertId();
            // Generate activation key and expiry (24h) 
            $activationKey = bin2hex(random_bytes(32));
            $entryDate = date('Y-m-d H:i:s');
            $expiryDate = date('Y-m-d H:i:s', strtotime('+1 day'));
            $this->log(
                'User registration activation dates',
                [
                    'entryDate' => $entryDate,
                    'expiryDate' => $expiryDate,
                    'userId' => $userId,
                    'email' => $email
                ]
            );
            $this->db->query("INSERT INTO user_activation (user_id, activation_key, entry_date, expiry_date) VALUES (?, ?, ?, ?)", [$userId, $activationKey, $entryDate, $expiryDate]);
            // Send activation email
            $activationLink = $this->config['base_url'] . "/user/activate?key=$activationKey";
            if (class_exists('PHPMailer\PHPMailer\PHPMailer')) {
                $mail = new PHPMailer(true);
                try {
                    $mail->isSMTP();
                    $mailConfig = $this->config['mail'];
                    $mail->Host = $mailConfig['host'];
                    $mail->SMTPAuth = true;
                    $mail->Username = $mailConfig['username'];
                    $mail->Password = $mailConfig['password'];
                    $mail->SMTPSecure = $mailConfig['encryption'];
                    $mail->Port = $mailConfig['port'];
                    $mail->setFrom($mailConfig['from_email'], $this->config['site_name'] ?? 'Site');
                    $mail->addAddress($email);
                    $mail->Subject = 'Activate Your Account';
                    $mail->Body = "Thank you for registering. Please activate your account by clicking the link below:\n$activationLink\nIf you did not register, please ignore this email.";
                    $mail->send();
                } catch (\Exception $e) {
                    // Optionally log or handle email errors
                }
            }
            return [
                'status' => 'success',
                'message' => 'Registration successful!<br><strong>Please check your email</strong> to activate your account.',
            ];
        }
    }

    public function update($userInfo)
    {
        $params = [];
        $sql2 = "UPDATE users SET ";
        $fields = [];
        if (isset($userInfo['first_name'])) {
            $fields[] = "first_name = ?";
            $params[] = $userInfo['first_name'];
        }
        if (isset($userInfo['second_name'])) {
            $fields[] = "second_name = ?";
            $params[] = $userInfo['second_name'];
        }
        if (isset($userInfo['email'])) {
            $fields[] = "email = ?";
            $params[] = $userInfo['email'];
        }
        if (isset($userInfo['avatar']) && $userInfo['avatar']) {
            $fields[] = "avatar = ?";
            $params[] = $userInfo['avatar'];
        }
        if (isset($userInfo['pwd']) && isset($userInfo['pwd2']) && $userInfo['pwd'] === $userInfo['pwd2'] && strlen($userInfo['pwd']) > 0) {
            $pwdEncrypted = $this->make_pass($userInfo['pwd']);
            $fields[] = "pwd = ?";
            $params[] = $pwdEncrypted;
        }
        $sql2 .= implode(", ", $fields) . " WHERE id = ?";
        $params[] = $userInfo['id'];
        $this->db->query($sql2, $params);
        return [
            'status' => 'success',
            'message' => 'Profile updated successfully.',
        ];
    }

    public function get_rank($id)
    {
        $sql = "SELECT * FROM rank WHERE user_id = ?";
        $rows = $this->db->fetchAll($sql, [$id]);
        if (count($rows) > 0) {
            $row = $rows[0];
            $rank = [
                'title' => $row['title'],
                'level' => $row['level'],
                'admin' => $row['admin'],
            ];
        } else {
            $rank = [
                'title' => 'Registered User',
                'level' => '0',
                'admin' => '0',
            ];
        }
        return $rank;
    }

    public function login($user)
    {
        $sql = "SELECT * FROM users WHERE email = ?";
        $rows = $this->db->fetchAll($sql, [$user['email']]);
        if (count($rows) > 0) {
            foreach ($rows as $row) {
                if (isset($row['active']) && $row['active'] == 0) {
                    return [
                        'status' => 'fail',
                        'message' => 'Your account is not activated. Please check your email for the activation link.'
                    ];
                }
                $db_pwd = $row['pwd'];
                if (password_verify($user['pwd'], $db_pwd)) {
                    $rank = $this->get_rank($row['id']);
                    if ($row['dead_switch'] > 0) {
                        return [
                            'status' => 'fail',
                            'message' => 'Your account was closed or is inaccessible.'
                        ];
                    }
                    $_SESSION[PREFIX . 'rank_title'] = $rank['title'];
                    $_SESSION[PREFIX . 'rank_level'] = $rank['level'];
                    if ($rank['admin'] > 0) {
                        $_SESSION[PREFIX . 'admin'] = $rank['admin'];
                    }
                    $_SESSION[PREFIX . 'email'] = $row['email'];
                    $_SESSION[PREFIX . 'user_id'] = $row['id'];
                    $_SESSION[PREFIX . 'sec_hash'] = $row['security_hash'];
                    $_SESSION[PREFIX . 'first_name'] = $row['first_name'];
                    $_SESSION[PREFIX . 'second_name'] = $row['second_name'];
                    $_SESSION[PREFIX . 'last_log'] = $row['last_access'];
                    $_SESSION[PREFIX . 'avatar'] = $row['avatar'];
                    // Store a full user array for convenience
                    $_SESSION[PREFIX . 'user'] = [
                        'id' => $row['id'],
                        'email' => $row['email'],
                        'is_admin' => ($rank['admin'] > 0 ? 1 : 0),
                        'rank_title' => $rank['title'],
                        'rank_level' => $rank['level'],
                        'avatar' => $this->gravatar($row['email'])
                    ];
                    $now = date('Y-m-d H:i:s');
                    $update = "UPDATE users SET last_access = ? WHERE id = ?";
                    $this->db->query($update, [$now, $_SESSION[PREFIX . 'user_id']]);
                    if (isset($user['remember']) && $user['remember'] > 0) {
                        $time = time() + 60 * 60 * 24 * 30;
                        $hash = sha1(rand(1, 1000) . $this->config['salt']);
                        $date_set = date('Y-m-d H:i:s');
                        $date_expire = date('Y-m-d H:i:s', strtotime('+30 days'));
                        // Use the new DB class for the insert
                        $sql = "INSERT INTO `cookie_login`(`user_id`, `cookie_hash`, `date_added`, `expiry_date`) VALUES (?, ?, ?, ?)";
                        $this->db->query($sql, [
                            $_SESSION[PREFIX . 'user_id'],
                            $hash,
                            $date_set,
                            $date_expire
                        ]);
                        setcookie(PREFIX . 'cookie_login', $hash, [
                            'expires' => $time,
                            'path' => '/',
                            'secure' => isset($_SERVER['HTTPS']),
                            'httponly' => true,
                            'samesite' => 'Lax'
                        ]);
                    }
                    $this->generate_session();
                    // Optionally call record_session() if implemented
                    return [
                        'status' => 'success',
                        'message' => 'Login successful',
                    ];
                } else {
                    return [
                        'status' => 'fail',
                        'message' => 'Login mismatch'
                    ];
                }
            }
        } else {
            return [
                'status' => 'fail',
                'message' => 'Login mismatch'
            ];
        }
    }
}
