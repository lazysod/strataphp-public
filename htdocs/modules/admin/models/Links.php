<?php
// Links model for admin links management
class Links
{
    public function swapOrder($id1, $id2)
    {
        $link1 = $this->getById($id1);
        $link2 = $this->getById($id2);
        if ($link1 && $link2) {
            $order1 = $link1['order'];
            $order2 = $link2['order'];
            $this->db->query('UPDATE links SET `order` = ? WHERE id = ?', [$order2, $id1]);
            $this->db->query('UPDATE links SET `order` = ? WHERE id = ?', [$order1, $id2]);
        }
    }
    protected $db;
    protected $config;
    public function __construct($db, $config)
    {
        $this->db = $db;
        $this->config = $config;
    }
    public function getAll()
    {
        return $this->db->fetchAll('SELECT * FROM links ORDER BY `order` ASC');
    }
    public function getById($id)
    {
        return $this->db->fetch('SELECT * FROM links WHERE id = ?', [$id]);
    }
    public function addLink($title, $url, $icon, $nsfw = 0)
    {
        if ($icon === '') {
            $icon = $this->detectIcon($url);
        }
        $this->db->query('INSERT INTO links (title, url, icon, nsfw) VALUES (?, ?, ?, ?)', [$title, $url, $icon, $nsfw]);
    }
    public function updateLink($id, $title, $url, $icon, $nsfw = 0)
    {
        if ($icon === '') {
            $icon = $this->detectIcon($url);
        }
        $this->db->query('UPDATE links SET title = ?, url = ?, icon = ?, nsfw = ? WHERE id = ?', [$title, $url, $icon, $nsfw, $id]);
    }
    public function deleteLink($id)
    {
        $this->db->query('DELETE FROM links WHERE id = ?', [$id]);
    }
    public function detectIcon($url)
    {
        $map = [
            'twitter.com' => 'fab fa-twitter',
            'x.com' => 'fab fa-twitter',
            'facebook.com' => 'fab fa-facebook',
            'instagram.com' => 'fab fa-instagram',
            'linkedin.com' => 'fab fa-linkedin',
            'youtube.com' => 'fab fa-youtube',
            'github.com' => 'fab fa-github',
            'tiktok.com' => 'fab fa-tiktok',
            'reddit.com' => 'fab fa-reddit',
            'discord.com' => 'fab fa-discord',
            'pinterest.com' => 'fab fa-pinterest',
            'snapchat.com' => 'fab fa-snapchat',
            'medium.com' => 'fab fa-medium',
            'dribbble.com' => 'fab fa-dribbble',
            'behance.net' => 'fab fa-behance',
            'soundcloud.com' => 'fab fa-soundcloud',
            'spotify.com' => 'fab fa-spotify',
            'tumblr.com' => 'fab fa-tumblr',
            'stack-overflow.com' => 'fab fa-stack-overflow',
            'stackexchange.com' => 'fab fa-stack-exchange',
            'wordpress.com' => 'fab fa-wordpress',
            'telegram.me' => 'fab fa-telegram',
            'telegram.org' => 'fab fa-telegram',
            'whatsapp.com' => 'fab fa-whatsapp',
            'slack.com' => 'fab fa-slack',
            'flickr.com' => 'fab fa-flickr',
            'vimeo.com' => 'fab fa-vimeo',
            'paypal.com' => 'fab fa-paypal',
            'amazon.com' => 'fab fa-amazon',
            'apple.com' => 'fab fa-apple',
            'google.com' => 'fab fa-google',
            'microsoft.com' => 'fab fa-microsoft',
            'threads.com' => 'fa-brands fa-threads',
        ];
        $host = '';
        $parts = parse_url(strtolower($url));
        if (isset($parts['host'])) {
            $host = $parts['host'];
        }
        foreach ($map as $domain => $fa) {
            if ($host === $domain || str_ends_with($host, '.' . $domain)) {
                return $fa;
            }
        }
        return 'fas fa-link'; // fallback generic icon
    }
}
