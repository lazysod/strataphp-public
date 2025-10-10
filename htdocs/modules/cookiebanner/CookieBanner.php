<?php
namespace App\Modules\CookieBanner;

class CookieBanner
{
    private $config;
    public function __construct($config = [])
    {
        $this->config = $config;
    }

    public function render()
    {
        $cookieName = $this->config['cookie_name'] ?? 'cookie_consent';
        $message = $this->config['message'] ?? 'This website uses cookies to ensure you get the best experience.';
        $readMoreUrl = $this->config['read_more_url'] ?? '/privacy';
        $cookieLength = $this->config['cookie_length'] ?? 365;
        if (isset($_COOKIE[$cookieName])) return '';
        ob_start();
        ?>
        <div id="cookie-banner" style="position:fixed;bottom:0;left:0;width:100%;background:#222;color:#fff;padding:18px 10px;z-index:9999;text-align:center;box-shadow:0 -2px 8px rgba(0,0,0,0.15);">
            <span><?= htmlspecialchars($message) ?>
                <?php if ($readMoreUrl): ?>
                    <a href="<?= htmlspecialchars($readMoreUrl) ?>" style="color:#ffd700;text-decoration:underline;margin-left:10px;">Read more</a>
                <?php endif; ?>
            </span>
            <button id="cookie-accept-btn" style="margin-left:20px;padding:8px 18px;background:#ffd700;color:#222;border:none;border-radius:4px;cursor:pointer;font-weight:bold;">Accept</button>
        </div>
        <script>
        document.getElementById('cookie-accept-btn').onclick = function() {
            var d = new Date();
            d.setTime(d.getTime() + (<?= (int)$cookieLength ?>*24*60*60*1000));
            document.cookie = "<?= addslashes($cookieName) ?>=1;expires="+d.toUTCString()+";path=/";
            document.getElementById('cookie-banner').style.display = 'none';
        };
        </script>
        <?php
        return ob_get_clean();
    }
}
