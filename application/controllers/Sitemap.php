<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Sitemap XML dinamis.
 *
 * Hanya memuat konten publik yang benar-benar ada di database, jadi
 * sitemap tidak pernah menautkan halaman yang 404 atau sudah dihapus.
 */
class Sitemap extends CI_Controller {

    private const MAX_POSTS = 1000;
    private const MAX_USERS = 300;

    public function index() {
        // Sitemap ini untuk mesin pencari, bukan halaman interaktif.
        $posts = $this->_posts();
        $users = $this->_users();

        $xml = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
        $xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";

        $now = date('Y-m-d');
        foreach ([
            [base_url(), '1.0', $now],
            [base_url('race-hub'), '0.9', $now],
            [base_url('privacy'), '0.3', $now],
            [base_url('contact'), '0.3', $now],
            [base_url('donate'), '0.4', $now],
        ] as $row) {
            $xml .= "  <url>\n"
                . '    <loc>' . htmlspecialchars($row[0], ENT_XML1, 'UTF-8') . "</loc>\n"
                . '    <changefreq>' . $row[1] . "</changefreq>\n"
                . '    <lastmod>' . $row[2] . "</lastmod>\n"
                . "  </url>\n";
        }

        foreach ($posts as $p) {
            $xml .= "  <url>\n"
                . '    <loc>' . htmlspecialchars(
                    base_url('post/' . $p['username'] . '/' . $p['id_post']),
                    ENT_XML1,
                    'UTF-8'
                ) . "</loc>\n"
                . '    <changefreq>weekly</changefreq>' . "\n"
                . '    <lastmod>' . date('Y-m-d', strtotime($p['created_at'])) . "</lastmod>\n"
                . "  </url>\n";
        }

        foreach ($users as $u) {
            $xml .= "  <url>\n"
                . '    <loc>' . htmlspecialchars(base_url('user/' . $u['username']), ENT_XML1, 'UTF-8') . "</loc>\n"
                . '    <changefreq>monthly</changefreq>' . "\n"
                . '    <lastmod>' . date('Y-m-d', strtotime($u['created_at'])) . "</lastmod>\n"
                . "  </url>\n";
        }

        $xml .= '</urlset>';

        $this->output
            ->set_content_type('application/xml', 'UTF-8')
            ->set_output($xml);
    }

    private function _posts() {
        $this->load->database();
        // Route detail post butuh username DAN id: /post/<username>/<id>
        // (Post::index($username, $id_post)). Kalau id saja, 404.
        return $this->db->query(
            'SELECT p.id_post, u.username, p.created_at'
            . ' FROM posts p'
            . ' JOIN users u ON u.id_user = p.user_id'
            . ' WHERE (p.deleted IS NULL OR p.deleted = 0)'
            . '   AND u.status = "active"'
            . ' ORDER BY p.created_at DESC'
            . ' LIMIT ' . self::MAX_POSTS
        )->result_array();
    }

    private function _users() {
        $this->load->database();
        return $this->db->query(
            'SELECT username, created_at FROM users'
            . ' WHERE status = "active"'
            . ' ORDER BY created_at DESC'
            . ' LIMIT ' . self::MAX_USERS
        )->result_array();
    }
}
