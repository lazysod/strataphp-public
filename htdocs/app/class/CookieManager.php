<?php

class CookieManager extends User
{
    private $db;
    private $config;

    public function __construct($db, $config)
    {
        $this->db = $db;
        $this->config = $config;
    }

    public function set($user_hash)
    {
        // + 10 days
        $expire_time = time() + (60 * 60 * 24 * 10);
        $cookie_hash = sha1(rand(1, 1000) . $this->config['salt']);
        $cookie_data = array(
            'hash' => $cookie_hash,
            'expires' => date('m/d/Y', $expire_time)
        );
        setcookie(PREFIX . "login_cookie", json_encode($cookie_data), $expire_time, '/');
        $user_id = $_SESSION[PREFIX . 'user_id'];
        $expire_date = date('Y-m-d', $expire_time);
        $today = date('Y-m-d');
        $sql = "INSERT INTO `cookie_login`(`id`, `user_id`, `cookie_hash`, `date_added`, `expiry_date`) VALUES (NULL, ?, ?, ?, ?)";
        $this->db->query($sql, [$user_id, $cookie_hash, $today, $expire_date]);
    }

    public function unset()
    {
        setcookie(PREFIX . 'login_cookie', '', time() + (-86400 * 30), "/");
    }

    public function is_active()
    {
        if (isset($_COOKIE[PREFIX . "login_cookie"]) && $_COOKIE[PREFIX . "login_cookie"] == true) {
            return true;
        } else {
            return false;
        }
    }

    public function login_check()
    {
        if ($this->is_active() != true) {
            return false;
        } else {
            $cookie_data = json_decode($_COOKIE[PREFIX . 'login_cookie']);
            $hash = $cookie_data->hash;
            $today = date('Y-m-d');
            $sql = "SELECT `user_id`, `expiry_date` FROM `cookie_login` WHERE `cookie_hash` = ? AND `expiry_date` > ?";
            $rows = $this->db->fetchAll($sql, [$hash, $today]);
            if (count($rows) < 1) {
                return false;
            } else {
                foreach ($rows as $row) {
                    $user_id = $row['user_id'];
                }
                $result = $this->cookie_login($user_id);
                return ($result['status'] == 'success');
            }
        }
    }

    public function cookie_login($user_id)
    {
        $sql = "SELECT email, id, pwd, username FROM users WHERE id = ? AND active = 1 AND dead_switch = '0'";
        $rows = $this->db->fetchAll($sql, [$user_id]);
        if (count($rows) < 1) {
            return [
                'status' => 'fail',
                'message' => 'Unable to login, please try later or contact support.'
            ];
        } else {
            foreach ($rows as $row) {
                $email = $row['email'];
                $name = $row['username'];
                $hash = sha1($this->config['salt'] . rand(1, 1000) . date("Y-m-d H:i:s"));

                $_SESSION[PREFIX . 'user_id'] = $row['id'];

                $now = date('Y-m-d H:i:s');
                $sql3 = "UPDATE users SET last_access = ? WHERE email = ?";
                $this->db->query($sql3, [$now, $email]);
                $_SESSION[PREFIX . 'email'] = $row['email'];
                $_SESSION[PREFIX . 'sec_hash'] = $hash;
                $_SESSION[PREFIX . 'username'] = $name;
                $_SESSION[PREFIX . 'verified'] = 1;
            }
            return [
                'status' => 'success',
                'message' => 'Login Success!?',
                'returning_user' => true
            ];
        }
    }

    public function check($cookie)
    {
        $cookie = json_decode($_COOKIE[PREFIX . 'login_cookie'], true);
        echo date('d/m/Y H:i:s', strtotime($cookie['expires']));
    }
}
