<?php
namespace App;
use App\Logger;
class TokenManager
{
    private $config;
    private $logger;

    public function __construct($config = null)
    {
        $this->config = $config ?? ['token_expiry' => 3600, 'log_path' => __DIR__ . '/../../storage/logs'];
        if (class_exists('Logger')) {
            $this->logger = new Logger($this->config);
        }
    }

    public function renew()
    {
        $sessionPrefix = $this->config['session_prefix'] ?? 'app_';
        if (!empty($_SESSION[$sessionPrefix . 'token'])) {
            unset($_SESSION[$sessionPrefix . 'token']);
            if ($this->logger) {
                $this->logger->info('Token renewed', ['user_ip' => $_SERVER['REMOTE_ADDR'] ?? null]);
            }
        }
        $this->generate();
    }

    public function generate()
    {
        $sessionPrefix = $this->config['session_prefix'] ?? 'app_';
        if (empty($_SESSION[$sessionPrefix . 'token'])) {
            if (function_exists('random_bytes')) {
                $token_id = bin2hex(random_bytes(32));
            } else {
                $token_id = bin2hex(openssl_random_pseudo_bytes(32));
            }
            $expiry = $this->config['token_expiry'] ?? 3600;
            $token_array = [
                'token_expire' => date('Y-m-d H:i:s', time() + $expiry),
                'token_id' => $token_id,
            ];
            $_SESSION[$sessionPrefix . 'token'] = $token_array;
        }
        return $_SESSION[$sessionPrefix . 'token']['token_id'] ?? null;
    }

    /**
     * Static helper to get or generate a CSRF token for forms
     */
    public static function csrf($config = null)
    {
        $sessionPrefix = ($config ?? [])['session_prefix'] ?? 'app_';
        if (empty($_SESSION[$sessionPrefix . 'token']['token_id'])) {
            $tm = new self($config);
            $tm->generate();
        }
        return $_SESSION[$sessionPrefix . 'token']['token_id'] ?? '';
    }

    /**
     * Example usage in a form:
     * <input type="hidden" name="token" value="<?= TokenManager::csrf() ?>">
     *
     * Example verification in controller:
     * $tm = new TokenManager();
     * $result = $tm->verify($_POST['token']);
     * if ($result['status'] === 'success') { ... }
     */
    public function verify($token)
    {
        $sessionPrefix = $this->config['session_prefix'] ?? 'app_';
        $success = false;
        if (isset($_SESSION[$sessionPrefix . 'token']['token_id'], $_SESSION[$sessionPrefix . 'token']['token_expire']) 
            && strtotime($_SESSION[$sessionPrefix . 'token']['token_expire']) > time()
        ) {
            if (hash_equals($_SESSION[$sessionPrefix . 'token']['token_id'], $token)) {
                $success = true;
                if ($this->logger) {
                    $this->logger->info('Token verified', ['user_ip' => $_SERVER['REMOTE_ADDR'] ?? null]);
                }
                return [
                    'status' => 'success',
                    'token' => $token,
                    'session_token' => $_SESSION[$sessionPrefix . 'token']['token_id']
                ];
            }
        }
        if ($this->logger) {
            $this->logger->warning(
                'Token verification failed', [
                'user_ip' => $_SERVER['REMOTE_ADDR'] ?? null,
                'provided_token' => $token,
                'session_token' => $_SESSION[$sessionPrefix . 'token']['token_id'] ?? null
                ]
            );
        }
        return [
            'status' => 'fail',
            'token' => $token,
            'session_token' => $_SESSION[$sessionPrefix . 'token']['token_id'] ?? null
        ];
    }

    public function tokenCheck()
    {
        /* 
            MIGHT set expiry in SESSION 
            
            Possible error codes: 
            - token no set(?)
            - token not valid
            - token is valid
            OR 
            - True = token is ok 
            - false token not set or expired
        */
        $sessionPrefix = $this->config['session_prefix'] ?? 'app_';
        if (isset($_SESSION[$sessionPrefix . 'token']['token_expire'])) {
            $this->generate();
        }
    }
}
