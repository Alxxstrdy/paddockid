<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Post extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->library('session');
        $this->load->model('Post_model');
        $this->load->model('Notification_model');
        $this->load->model('Activity_model');
        $this->load->helper('waktu_helper');
        $this->load->library('Moderation', NULL, 'moderation');
    }

    public function index($username = NULL, $id_post = NULL) {
        if (empty($id_post)) {
            show_404();
        }
        $session_data = $this->session->userdata('user_logged_in');
        $current_user_id = $session_data ? $session_data['user_id'] : 0;

        $data['post'] = $this->Post_model->get_post_by_id($id_post, $current_user_id);

        if (!$data['post']) {
            show_404();
        }

        // Jika current_user diblokir oleh author post, jangan tampilkan
        if ($current_user_id && !empty($data['post']['user_id'])) {
            $this->db->where('blocker_id', $data['post']['user_id']);
            $this->db->where('blocked_id', $current_user_id);
            if ($this->db->get('blocked_users')->num_rows() > 0) {
                show_404();
            }
        }

        $data['comments'] = $this->Post_model->get_post_comments($id_post, $current_user_id);

        if ($session_data) {
            $data['current_user_avatar'] = avatar_url($session_data['profile_pic'] ?? 'default.jpg');
        } else {
            $data['current_user_avatar'] = assets_url('default.jpg');
        }

        $data['title'] = "Postingan oleh @" . $data['post']['username'] . " | PaddockID";
        $data['current_user_id'] = $current_user_id;
        $data['page_css'][] = 'home';
        $data['page_css'][] = 'post';

        $this->load->view('layout/header', $data);
        $this->load->view('layout/sidebar-left', $data);
        $this->load->view('detail-post', $data);
        $this->load->view('layout/sidebar-right', $data);
        $this->load->view('layout/footer');
    }

    public function create_post_page()
    {
        $session_data = $this->session->userdata('user_logged_in');
        if (!$session_data) {
            redirect('auth');
        }

        $data['title'] = 'Buat Postingan | PaddockID';

        $fresh_user = $this->db->select('display_name, username, avatar, border_active')
            ->from('users')
            ->where('id_user', $session_data['user_id'])
            ->get()
            ->row_array();
        $data['user'] = [
            'user_id'      => $session_data['user_id'],
            'display_name' => $fresh_user ? $fresh_user['display_name'] : ($session_data['fullname'] ?? $session_data['username']),
            'username'     => $fresh_user ? $fresh_user['username'] : $session_data['username'],
            'avatar'       => avatar_url($fresh_user ? $fresh_user['avatar'] : ($session_data['profile_pic'] ?? null)),
        ];

        $this->load->view('layout/header', $data);
        $this->load->view('layout/sidebar-left', $data);
        $this->load->view('create-post', $data);
        $this->load->view('layout/sidebar-right', $data);
        $this->load->view('layout/footer');
    }

    public function edit_post_page($id_post = NULL)
    {
        $session_data = $this->session->userdata('user_logged_in');
        if (!$session_data) {
            redirect('auth');
        }

        if (empty($id_post)) {
            show_404();
        }

        $post = $this->Post_model->get_post_by_id($id_post, $session_data['user_id']);
        if (!$post) {
            show_404();
        }

        if ((string)$post['user_id'] !== (string)$session_data['user_id']) {
            show_404();
        }

        $data['post'] = $post;
        $data['title'] = 'Edit Postingan | PaddockID';

        $this->load->view('layout/header', $data);
        $this->load->view('layout/sidebar-left', $data);
        $this->load->view('edit-post', $data);
        $this->load->view('layout/sidebar-right', $data);
        $this->load->view('layout/footer');
    }

    public function add_comment() {
        if ($this->input->method() !== 'post') {
            return $this->output
                ->set_content_type('application/json')
                ->set_status_header(405)
                ->set_output(json_encode(['status' => 'error', 'message' => 'Method tidak diizinkan.']));
        }

        $session_data = $this->session->userdata('user_logged_in');
        if (!$session_data) {
            return $this->output
                ->set_content_type('application/json')
                ->set_status_header(401)
                ->set_output(json_encode(['status' => 'error', 'message' => 'Silakan login terlebih dahulu.']));
        }

        // Wajib verifikasi email sebelum berkomentar
        if (empty($session_data['email_verified'])) {
            return $this->output
                ->set_content_type('application/json')
                ->set_status_header(403)
                ->set_output(json_encode(['status' => 'error', 'message' => 'Verifikasi email dulu untuk berkomentar. Cek inbox atau kirim ulang di Pengaturan Akun.']));
        }

        // Anti-spam: maks 30 komentar per jam
        if (!throttle('post_comment', 30, 60, $session_data['user_id'])) {
            return $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode(['status' => 'error', 'message' => 'Terlalu banyak komentar. Coba lagi nanti.']));
        }

        $id_post      = $this->input->post('id_post', TRUE);
        $comment_text = $this->input->post('comment_text', TRUE);
        $parent_id    = $this->input->post('parent_id', TRUE);

        if (empty($id_post) || empty($comment_text)) {
            return $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode(['status' => 'error', 'message' => 'Komentar tidak boleh kosong.']));
        }

        // Generate random comment ID
        do {
            $id_comment = (string) random_int(100000000, 999999999);
        } while ($this->db->get_where('post_comments', ['id_comment' => $id_comment])->num_rows() > 0);

        $save_data = [
            'id_comment' => $id_comment,
            'id_post'   => $id_post,
            'user_id'   => $session_data['user_id'],
            'content'   => $comment_text,
            'parent_id' => ($parent_id > 0) ? $parent_id : NULL,
        ];

        $this->db->insert('post_comments', $save_data);

        $this->Activity_model->log(
            $session_data['user_id'], $session_data['username'],
            'add_comment', 'comment', $id_comment,
            'Mengomentari post #' . $id_post
        );

        // --- Moderasi komentar (monitor-only, fail-open) ---
        // Hanya menghitung & mencatat; komentar tetap tersimpan apa pun hasilnya.
        $this->moderation->check(
            $comment_text,
            [],
            ['type' => 'comment', 'id' => $id_comment, 'user_id' => $session_data['user_id']]
        );

        if ($parent_id > 0) {
            $parent = $this->db->select('user_id, id_post')
                ->from('post_comments')
                ->where('id_comment', $parent_id)
                ->get()
                ->row_array();
            if ($parent && $parent['user_id'] !== $session_data['user_id']) {
                $this->Notification_model->create([
                    'id_user'    => $parent['user_id'],
                    'type'       => 'reply',
                    'actor_id'   => $session_data['user_id'],
                    'id_post'    => $parent['id_post'],
                    'id_comment' => $id_comment
                ]);
            }
        } else {
            $post = $this->db->select('user_id')
                ->from('posts')
                ->where('id_post', $id_post)
                ->get()
                ->row_array();

            if ($post && $post['user_id'] !== $session_data['user_id']) {
                $this->Notification_model->create([
                    'id_user'  => $post['user_id'],
                    'type'     => 'comment',
                    'actor_id' => $session_data['user_id'],
                    'id_post'  => $id_post,
                ]);
            }
        }

        $user_avatar = avatar_url($session_data['profile_pic'] ?? 'default.jpg');

        $response = [
            'status' => 'success',
            'message' => 'Komentar berhasil dikirim!',
            'new_comment' => [
                'id_comment' => $id_comment,
                'username'   => $session_data['username'] ?? 'user',
                'avatar'     => $user_avatar,
                'created_at' => 'Baru saja'
            ]
        ];

        return $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode($response));
    }

    public function toggle_like_comment($id_comment = NULL) {
        if ($this->input->method() !== 'post') {
            return $this->output
                ->set_content_type('application/json')
                ->set_status_header(405)
                ->set_output(json_encode(['status' => 'error', 'message' => 'Method tidak diizinkan.']));
        }

        if (empty($id_comment)) {
            return $this->output
                ->set_content_type('application/json')
                ->set_status_header(400)
                ->set_output(json_encode(['status' => 'error', 'message' => 'ID Komentar tidak ditemukan.']));
        }

        try {
            $session_data = $this->session->userdata('user_logged_in');
            if (!$session_data) {
                throw new Exception("Kamu harus login terlebih dahulu.");
            }

            $user_id = $session_data['user_id'];

            // Anti-spam: maks 60 interaksi like komentar per jam
            if (!throttle('post_like', 60, 60, $user_id)) {
                throw new Exception("Terlalu banyak interaksi. Coba lagi nanti.");
            }

            $check = $this->db->get_where('comment_likes', [
                'comment_id' => $id_comment,
                'user_id'    => $user_id
            ])->row();

            if ($check) {
                $this->db->delete('comment_likes', [
                    'comment_id' => $id_comment,
                    'user_id'    => $user_id
                ]);
                $action = 'unliked';
            } else {
                $this->db->insert('comment_likes', [
                    'comment_id' => $id_comment,
                    'user_id'    => $user_id
                ]);
                $action = 'liked';
            }

            $this->Activity_model->log(
                $session_data['user_id'], $session_data['username'],
                $action === 'liked' ? 'like_comment' : 'unlike_comment',
                'comment', $id_comment,
                ($action === 'liked' ? 'Menyukai' : 'Tidak menyukai') . ' komentar #' . $id_comment
            );

            $likes_count = $this->db->where('comment_id', $id_comment)
                ->count_all_results('comment_likes');

            $response = [
                'status' => 'success',
                'action' => $action,
                'likes_count' => $likes_count
            ];

            return $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode($response));

        } catch (Exception $e) {
            return $this->output
                ->set_content_type('application/json')
                ->set_status_header(500)
                ->set_output(json_encode(['status' => 'error', 'message' => safe_error_msg($e)]));
        }
    }

    public function create_post()
    {
        if ($this->input->method() !== 'post') {
            return $this->output
                ->set_content_type('application/json')
                ->set_status_header(405)
                ->set_output(json_encode(['status' => 'error', 'message' => 'Method tidak diizinkan.']));
        }

        $session_data = $this->session->userdata('user_logged_in');
        if (!$session_data) {
            return $this->output
                ->set_content_type('application/json')
                ->set_status_header(401)
                ->set_output(json_encode(['status' => 'error', 'message' => 'Silakan login terlebih dahulu.']));
        }

        // Wajib verifikasi email sebelum posting
        if (empty($session_data['email_verified'])) {
            return $this->output
                ->set_content_type('application/json')
                ->set_status_header(403)
                ->set_output(json_encode(['status' => 'error', 'message' => 'Verifikasi email dulu untuk bisa posting. Cek inbox atau kirim ulang di Pengaturan Akun.']));
        }

        // Anti-spam & anti-coin-farm: maks 5 posting per jam
        if (!throttle('post_create', 5, 60, $session_data['user_id'])) {
            return $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode(['status' => 'error', 'message' => 'Terlalu banyak posting. Maksimal 5 per jam. Coba lagi nanti.']));
        }

        $content    = trim($this->input->post('content', true));

        if (empty($content)) {
            return $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode(['status' => 'error', 'message' => 'Konten tidak boleh kosong.']));
        }

        // Upload images
        $media_files = [];

        // Kalau total request melebihi post_max_size, PHP membuang seluruh
        // $_FILES tanpa pesan. Tanpa cek ini, gambar-gambar hilang diam-diam
        // dan yang tersimpan post teks biasa.
        $content_length = (int) ($_SERVER['CONTENT_LENGTH'] ?? 0);
        $post_max = ini_get('post_max_size');
        $post_max_bytes = $post_max ? (int) $post_max * 1024 * 1024 : 0;
        if ($post_max_bytes > 0 && $content_length > $post_max_bytes) {
            log_message('error', 'create_post: total request ' . $content_length . ' melebihi post_max_size ' . $post_max);
            return $this->output
                ->set_content_type('application/json')
                ->set_status_header(413)
                ->set_output(json_encode([
                    'status' => 'error',
                    'message' => 'Total ukuran gambar terlalu besar. Maksimal 4 gambar, masing-masing 10 MB.'
                ]));
        }

        if (!empty($_FILES['images']['name'][0])) {
            $total_files = count($_FILES['images']['name']);

            if ($total_files > 4) {
                return $this->output
                    ->set_content_type('application/json')
                    ->set_output(json_encode(['status' => 'error', 'message' => 'Maksimal 4 gambar per postingan.']));
            }

            $upload_path = upload_storage_path('uploads/posts/');
            if (!ensure_upload_directory($upload_path)) {
                log_message('error', 'create_post: uploads/posts tidak writable oleh user web server: ' . $upload_path);
                return $this->output
                    ->set_content_type('application/json')
                    ->set_status_header(500)
                    ->set_output(json_encode([
                        'status' => 'error',
                        'message' => 'Gagal menyimpan gambar. Folder uploads/posts tidak bisa ditulis server. Hubungi admin.'
                    ]));
            }

            $max_size = 10 * 1024 * 1024; // 10 MB
            $submitted_files = 0; // jumlah gambar yang dikirim user (termasuk yang ditolak PHP)
            $upload_error = 0;    // kode error PHP terakhir (1=terlalu besar, dsb)
            $validation_error = ''; // alasan validasi terakhir (mis. resolusi terlalu besar)

            foreach ($_FILES['images']['name'] as $key => $name) {
                if ($name === '' || $name === null) {
                    continue;
                }

                $submitted_files++;

                if ($_FILES['images']['error'][$key] !== UPLOAD_ERR_OK) {
                    // Dihitung juga di sini: kalau dibiarkan lanjut tanpa tanda,
                    // post teks tetap terbuat dan user tidak tahu gambarnya gagal.
                    $upload_error = (int) $_FILES['images']['error'][$key];
                    log_message('error', 'create_post: PHP menolak "' . $name
                        . '" dengan kode ' . $upload_error);
                    continue;
                }

                if ($_FILES['images']['size'][$key] > $max_size) {
                    $validation_error = 'Salah satu gambar terlalu besar. Maksimal 10 MB per gambar.';
                    continue;
                }

                // Validasi isi file + dimensi/piksel SEBELUM decode.
                $validation = validate_image_file($_FILES['images']['tmp_name'][$key]);
                if (empty($validation['ok'])) {
                    $validation_error = $validation['error'];
                    log_message('error', 'create_post: gambar "' . $name . '" ditolak: ' . $validation_error);
                    continue;
                }
                $ext = $validation['ext'];

                $new_name = md5(uniqid(mt_rand(), true)) . '.' . $ext;
                $dest = rtrim($upload_path, '/\\') . DIRECTORY_SEPARATOR . $new_name;

                if (move_uploaded_file($_FILES['images']['tmp_name'][$key], $dest)) {
                    $converted = convert_image_file_to_webp($dest);
                    if ($converted) {
                        $media_files[] = 'uploads/posts/' . basename($converted);
                    } else {
                        @unlink($dest);
                        log_message('error', 'create_post: konversi ke WebP gagal untuk ' . $name);
                    }
                } else {
                    log_message('error', 'create_post: move_uploaded_file gagal untuk ' . $name);
                }
            }

            // Ada gambar yang dikirim tapi tidak satu pun tersimpan: jangan buat post
            // teks tanpa gambar. Laporkan dengan pesan sesuai penyebabnya.
            if ($submitted_files > 0 && empty($media_files)) {
                $too_big = in_array($upload_error, [UPLOAD_ERR_INI_SIZE, UPLOAD_ERR_FORM_SIZE], true);
                $message = $validation_error !== '' ? $validation_error : match ($upload_error) {
                    UPLOAD_ERR_INI_SIZE  => 'Salah satu gambar terlalu besar. Maksimal 10 MB per gambar.',
                    UPLOAD_ERR_FORM_SIZE => 'Total ukuran gambar terlalu besar. Maksimal 4 gambar berjumlah 50 MB.',
                    UPLOAD_ERR_PARTIAL   => 'Sebagian gambar gagal terunggah penuh. Coba unggah ulang.',
                    UPLOAD_ERR_CANT_WRITE => 'Gagal menyimpan gambar di server. Hubungi admin.',
                    UPLOAD_ERR_NO_TMP_DIR, UPLOAD_ERR_EXTENSION => 'Gagal mengunggah gambar. Coba unggah ulang.',
                    default => 'Gagal menyimpan gambar. Format harus JPG, PNG, GIF, atau WebP (disimpan sebagai WebP) dan maksimal 10 MB.',
                };
                if ($validation_error !== '') {
                    $too_big = (strpos($validation_error, 'terlalu besar') !== false || strpos($validation_error, 'Resolusi') !== false);
                }

                return $this->output
                    ->set_content_type('application/json')
                    ->set_status_header($too_big ? 413 : 500)
                    ->set_output(json_encode([
                        'status' => 'error',
                        'message' => $message
                    ]));
            }
        }

        $id_post = $this->Post_model->create_post(
            $session_data['user_id'],
            $content,
            $media_files
        );

        if ($id_post) {
            $this->db->set('coins', 'coins + 3', FALSE)
                ->where('id_user', $session_data['user_id'])
                ->update('users');
            $coin_row = $this->db->select('coins')
                ->from('users')
                ->where('id_user', $session_data['user_id'])
                ->get()
                ->row_array();
            $session_data['coins'] = $coin_row ? (int) $coin_row['coins'] : 0;
            $this->session->set_userdata('user_logged_in', $session_data);

            $this->Activity_model->log(
                $session_data['user_id'], $session_data['username'],
                'create_post', 'post', $id_post,
                'Membuat postingan baru'
            );

            // --- Moderasi postingan (monitor-only, fail-open) ---
            // Hitung teks + gambar, catat hasilnya. Postingan tetap tersimpan.
            $image_paths = array_map(static function ($p) {
                return FCPATH . $p;
            }, $media_files);
            $this->moderation->check(
                $content,
                $image_paths,
                ['type' => 'post', 'id' => $id_post, 'user_id' => $session_data['user_id']]
            );

            $post = $this->Post_model->get_post_by_id($id_post, $session_data['user_id']);

            // Halaman create-post langsung redirect ke home setelah sukses, jadi
            // toast-nya dititipkan lewat flashdata dan dimunculkan di halaman tujuan.
            $this->session->set_flashdata('post_toast', [
                'type'    => 'success',
                'message' => 'Postingan berhasil dibuat! +3 koin.',
            ]);

            return $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'status' => 'success',
                    'message' => 'Postingan berhasil dibuat! +3 koin.',
                    'coins' => $session_data['coins'],
                    'post' => $post
                ]));
        }

        return $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode(['status' => 'error', 'message' => 'Gagal membuat postingan.']));
    }

    public function report()
    {
        if ($this->input->method() !== 'post') {
            return $this->output
                ->set_content_type('application/json')
                ->set_status_header(405)
                ->set_output(json_encode(['status' => 'error', 'message' => 'Method tidak diizinkan.']));
        }

        $session_data = $this->session->userdata('user_logged_in');
        if (!$session_data) {
            return $this->output
                ->set_content_type('application/json')
                ->set_status_header(401)
                ->set_output(json_encode(['status' => 'error', 'message' => 'Silakan login terlebih dahulu.']));
        }

        // Anti-spam report: maks 10 laporan post per hari
        if (!throttle('post_report', 10, 1440, $session_data['user_id'])) {
            return $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode(['status' => 'error', 'message' => 'Terlalu banyak laporan hari ini. Coba lagi besok.']));
        }

        $id_post = $this->input->post('id_post', true);
        $reason  = trim($this->input->post('reason', true));

        if (empty($id_post) || empty($reason)) {
            return $this->output
                ->set_content_type('application/json')
                ->set_status_header(400)
                ->set_output(json_encode(['status' => 'error', 'message' => 'Alasan laporan harus diisi.']));
        }

        $inserted = $this->Post_model->report_post($id_post, $session_data['user_id'], $reason);

        if ($inserted) {
            return $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode(['status' => 'success', 'message' => 'Laporan berhasil dikirim.']));
        }

        return $this->output
            ->set_content_type('application/json')
            ->set_status_header(500)
            ->set_output(json_encode(['status' => 'error', 'message' => 'Gagal mengirim laporan.']));
    }

    public function report_comment()
    {
        if ($this->input->method() !== 'post') {
            return $this->output
                ->set_content_type('application/json')
                ->set_status_header(405)
                ->set_output(json_encode(['status' => 'error', 'message' => 'Method tidak diizinkan.']));
        }

        $session_data = $this->session->userdata('user_logged_in');
        if (!$session_data) {
            return $this->output
                ->set_content_type('application/json')
                ->set_status_header(401)
                ->set_output(json_encode(['status' => 'error', 'message' => 'Silakan login terlebih dahulu.']));
        }

        // Anti-spam report komentar: maks 10 laporan per hari
        if (!throttle('post_report_comment', 10, 1440, $session_data['user_id'])) {
            return $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode(['status' => 'error', 'message' => 'Terlalu banyak laporan hari ini. Coba lagi besok.']));
        }

        $id_comment = $this->input->post('id_comment', true);
        $reason     = trim($this->input->post('reason', true));

        if (empty($id_comment) || empty($reason)) {
            return $this->output
                ->set_content_type('application/json')
                ->set_status_header(400)
                ->set_output(json_encode(['status' => 'error', 'message' => 'Alasan laporan harus diisi.']));
        }

        $inserted = $this->Post_model->report_comment($id_comment, $session_data['user_id'], $reason);

        if ($inserted) {
            return $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode(['status' => 'success', 'message' => 'Laporan berhasil dikirim.']));
        }

        return $this->output
            ->set_content_type('application/json')
            ->set_status_header(500)
            ->set_output(json_encode(['status' => 'error', 'message' => 'Gagal mengirim laporan.']));
    }

    public function edit_post()
    {
        if ($this->input->method() !== 'post') {
            return $this->output
                ->set_content_type('application/json')
                ->set_status_header(405)
                ->set_output(json_encode(['status' => 'error', 'message' => 'Method tidak diizinkan.']));
        }

        $session_data = $this->session->userdata('user_logged_in');
        if (!$session_data) {
            return $this->output
                ->set_content_type('application/json')
                ->set_status_header(401)
                ->set_output(json_encode(['status' => 'error', 'message' => 'Silakan login terlebih dahulu.']));
        }

        $id_post = $this->input->post('id_post', true);
        $content  = trim($this->input->post('content', true));

        if (empty($id_post) || empty($content)) {
            return $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode(['status' => 'error', 'message' => 'Konten tidak boleh kosong.']));
        }

        $updated = $this->Post_model->update_post($id_post, $session_data['user_id'], $content);

        if ($updated) {
            $this->Activity_model->log(
                $session_data['user_id'], $session_data['username'],
                'edit_post', 'post', $id_post,
                'Mengedit postingan #' . $id_post
            );

            // Sama seperti create: view me-redirect, jadi toast via flashdata.
            $this->session->set_flashdata('post_toast', [
                'type'    => 'success',
                'message' => 'Postingan berhasil diedit.',
            ]);

            return $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode(['status' => 'success', 'message' => 'Postingan berhasil diedit.']));
        }

        return $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode(['status' => 'error', 'message' => 'Gagal mengedit postingan atau kamu bukan pemiliknya.']));
    }

    public function delete_post()
    {
        if ($this->input->method() !== 'post') {
            return $this->output
                ->set_content_type('application/json')
                ->set_status_header(405)
                ->set_output(json_encode(['status' => 'error', 'message' => 'Method tidak diizinkan.']));
        }

        $session_data = $this->session->userdata('user_logged_in');
        if (!$session_data) {
            return $this->output
                ->set_content_type('application/json')
                ->set_status_header(401)
                ->set_output(json_encode(['status' => 'error', 'message' => 'Silakan login terlebih dahulu.']));
        }

        $id_post = $this->input->post('id_post', true);

        if (empty($id_post)) {
            return $this->output
                ->set_content_type('application/json')
                ->set_status_header(400)
                ->set_output(json_encode(['status' => 'error', 'message' => 'ID Postingan tidak ditemukan.']));
        }

        $deleted = $this->Post_model->delete_post($id_post, $session_data['user_id']);

        if ($deleted) {
            $this->Activity_model->log(
                $session_data['user_id'], $session_data['username'],
                'delete_post', 'post', $id_post,
                'Menghapus postingan #' . $id_post
            );
            return $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode(['status' => 'success', 'message' => 'Postingan berhasil dihapus.']));
        }

        return $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode(['status' => 'error', 'message' => 'Gagal menghapus postingan atau kamu bukan pemiliknya.']));
    }

    public function edit_comment()
    {
        if ($this->input->method() !== 'post') {
            return $this->output
                ->set_content_type('application/json')
                ->set_status_header(405)
                ->set_output(json_encode(['status' => 'error', 'message' => 'Method tidak diizinkan.']));
        }

        $session_data = $this->session->userdata('user_logged_in');
        if (!$session_data) {
            return $this->output
                ->set_content_type('application/json')
                ->set_status_header(401)
                ->set_output(json_encode(['status' => 'error', 'message' => 'Silakan login terlebih dahulu.']));
        }

        $id_comment = $this->input->post('id_comment', true);
        $content    = trim($this->input->post('content', true));

        if (empty($id_comment) || empty($content)) {
            return $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode(['status' => 'error', 'message' => 'Komentar tidak boleh kosong.']));
        }

        $updated = $this->Post_model->update_comment($id_comment, $session_data['user_id'], $content);

        if ($updated) {
            $this->Activity_model->log(
                $session_data['user_id'], $session_data['username'],
                'edit_comment', 'comment', $id_comment,
                'Mengedit komentar #' . $id_comment
            );
            return $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode(['status' => 'success', 'message' => 'Komentar berhasil diedit.']));
        }

        return $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode(['status' => 'error', 'message' => 'Gagal mengedit komentar atau kamu bukan pemiliknya.']));
    }

    public function delete_comment()
    {
        if ($this->input->method() !== 'post') {
            return $this->output
                ->set_content_type('application/json')
                ->set_status_header(405)
                ->set_output(json_encode(['status' => 'error', 'message' => 'Method tidak diizinkan.']));
        }

        $session_data = $this->session->userdata('user_logged_in');
        if (!$session_data) {
            return $this->output
                ->set_content_type('application/json')
                ->set_status_header(401)
                ->set_output(json_encode(['status' => 'error', 'message' => 'Silakan login terlebih dahulu.']));
        }

        $id_comment = $this->input->post('id_comment', true);

        if (empty($id_comment)) {
            return $this->output
                ->set_content_type('application/json')
                ->set_status_header(400)
                ->set_output(json_encode(['status' => 'error', 'message' => 'ID Komentar tidak ditemukan.']));
        }

        $deleted = $this->Post_model->delete_comment($id_comment, $session_data['user_id']);

        if ($deleted) {
            $this->Activity_model->log(
                $session_data['user_id'], $session_data['username'],
                'delete_comment', 'comment', $id_comment,
                'Menghapus komentar #' . $id_comment
            );
            return $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode(['status' => 'success', 'message' => 'Komentar berhasil dihapus.']));
        }

        return $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode(['status' => 'error', 'message' => 'Gagal menghapus komentar atau kamu bukan pemiliknya.']));
    }
}
