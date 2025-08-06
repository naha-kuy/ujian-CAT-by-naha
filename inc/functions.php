<?php
include '../koneksi/koneksi.php'; // Pastikan koneksi sudah ada
include_once '../inc/encrypt.php'; // berisi $method dan $rahasia

$pengaturan = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT nama_aplikasi FROM pengaturan WHERE id = 1"));

// Fungsi untuk pengecekan login user
function check_login($role) {
    global $koneksi;

    // Daftar role yang valid
    $valid_roles = ['admin', 'siswa'];

    // Memastikan role yang diberikan valid
    if (!in_array($role, $valid_roles)) {
        die("Invalid role.");
    }

    // Mengecek apakah pengguna sudah login
    if (!isset($_SESSION[$role . '_logged_in']) || $_SESSION[$role . '_logged_in'] !== true) {
        header("Location: login.php");
        exit;
    }

}

// Fungsi untuk autentikasi user pakai mysqli
function authenticate_user($username, $password_input, $role) {
    global $koneksi;

    if (empty($username) || empty($password_input)) {
        return false;
    }

    $table = ($role == 'admin') ? 'admins' : 'siswa';
    $query = "SELECT * FROM $table WHERE username = ?";

    if (!$stmt = mysqli_prepare($koneksi, $query)) {
        die("Database query preparation failed: " . mysqli_error($koneksi));
    }

    mysqli_stmt_bind_param($stmt, "s", $username);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $user = mysqli_fetch_assoc($result);

    if ($user) {
        $stored_password = $user['password'];

        if ($role === 'admin') {
            if (verify_admin_password($password_input, $stored_password)) {
                $_SESSION[$role . '_logged_in'] = true;
                $_SESSION[$role . '_id'] = $user['id'];
                return true;
            }
        } else {
            // Cek apakah siswa dipaksa logout oleh admin
            if (!empty($user['force_logout'])) {
                // Reset force_logout dan session_token
                mysqli_query($koneksi, "UPDATE siswa SET force_logout = FALSE, session_token = NULL WHERE id_siswa = " . $user['id_siswa']);
                return false;
            }

            $settings = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT login_ganda FROM pengaturan WHERE id = 1"));
            $allow_multiple = ($settings['login_ganda'] == 'izinkan');

            if (!$allow_multiple && !empty($user['session_token'])) {
                return false;
            }

            if (verify_siswa_password($password_input, $stored_password)) {
                $session_token = bin2hex(random_bytes(32));
                $update = mysqli_prepare($koneksi, "UPDATE $table SET session_token = ?, force_logout = FALSE WHERE id_siswa = ?");
                mysqli_stmt_bind_param($update, "si", $session_token, $user['id_siswa']);
                mysqli_stmt_execute($update);

                $_SESSION[$role . '_logged_in'] = true;
                $_SESSION[$role . '_id'] = $user['id_siswa'];
                $_SESSION[$role . '_token'] = $session_token;
                return true;
            }
        }
    }

    return false;
}

// Fungsi untuk memverifikasi password admin
function verify_admin_password($password_input, $stored_password) {
    return password_verify($password_input, $stored_password);
}

// Fungsi untuk memverifikasi password siswa
function verify_siswa_password($password_input, $stored_password) {
    global $method, $rahasia;

    if (empty($password_input) || empty($stored_password)) {
        return false;
    }

    $decoded = base64_decode($stored_password);
    $iv_length = openssl_cipher_iv_length($method);
    $iv = substr($decoded, 0, $iv_length);
    $ciphertext = substr($decoded, $iv_length);
    $decrypted_password = openssl_decrypt($ciphertext, $method, $rahasia, 0, $iv);

    if ($decrypted_password === false) {
        error_log("Failed to decrypt password.");
        return false;
    }

    return ($decrypted_password === $password_input);
}


// Fungsi untuk mendapatkan informasi kredensial yang terenkripsi
function get_encrypted_credit() {
    global $koneksi;

    $query = "SELECT encrypt FROM profil WHERE id = 1";  // Ganti dengan ID yang sesuai
    $result = mysqli_query($koneksi, $query);

    if ($result) {
        $row = mysqli_fetch_assoc($result);
        return $row['encrypt'];
    }

    return null;
}

// Ambil teks terenkripsi
$encryptedText = get_encrypted_credit();
?>
