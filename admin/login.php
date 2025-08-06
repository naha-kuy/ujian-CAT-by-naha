<?php
session_start();
include '../koneksi/koneksi.php';
include '../inc/functions.php';

$error = '';

// Generate CSRF token
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
if (empty($_SESSION['captcha_question'])) {
    $a = rand(1, 9);
    $b = rand(1, 9);
    $_SESSION['captcha_question'] = "$a + $b";
    $_SESSION['captcha_answer'] = $a + $b;
}
// Redirect jika sudah login
if (isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true) {
    header("Location: dashboard.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);
    $captcha_input = $_POST['captcha'] ?? '';

    // Validasi CAPTCHA terlebih dahulu
    if ((int)$captcha_input !== $_SESSION['captcha_answer']) {
        $error = 'Captcha salah!';
    } else {
        // CAPTCHA benar, lanjutkan cek username dan password
        if (authenticate_user($username, $password, 'admin')) {
            unset($_SESSION['captcha_question'], $_SESSION['captcha_answer']);
            header("Location: dashboard.php");
            exit;
        } else {
            $error = 'Username atau password salah!';
        }

        // Regenerasi captcha setelah proses login selesai
        $a = rand(1, 9);
        $b = rand(1, 9);
        $_SESSION['captcha_question'] = "$a + $b";
        $_SESSION['captcha_answer'] = $a + $b;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Admin Login</title>
  <?php include '../inc/css.php'; ?>
  <style>
    body {
        background: url('../assets/images/bglogin.webp') no-repeat center center fixed;
        background-size: cover;
        margin: 0;
        height: 100vh;
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    }
    .overlay {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0, 0, 0, 0.81); /* lebih terang, jelas */
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .glass-card:hover {
        box-shadow: 0 12px 35px rgba(0, 0, 0, 0.49);
    }

    label {
        color: #444;
        font-weight: 600;
        font-size: 14px;
    }
    .glass-card {
        border-radius: 20px;

    }
    .glass-card input {
        background-color: #f8f9fa;
        border: 1px solid #ced4da;
        border-radius: 20px;
        padding: 10px;
        width: 100%;
        transition: border-color 0.3s ease;
        color: #333;
    }

    .glass-card input:focus {
        border-color: #0d6efd;
        outline: none;
        background-color: #fff;
    }

    .glass-card input::placeholder {
        color: #888;
    }

    button.btn {
        background-color: #0d6efd;
        border: none;
        color: #fff;
        padding: 10px 15px;
        border-radius: 20px;
        font-weight: 600;
        transition: background-color 0.3s ease;
    }

    button.btn:hover {
        background-color: #0b5ed7;
    }

    .text-danger {
        color: #dc3545 !important;
    }

    @media (max-width: 576px) {
        .glass-card {
            padding: 1.5rem;
        }

        .glass-card input {
            font-size: 14px;
        }
    }
  </style>
</head>
<body class="d-flex align-items-center justify-content-center" style="height: 100vh;">
   <div class="overlay d-flex align-items-center justify-content-center" style="height: 100vh;">
  <div class="container">
    <div class="row justify-content-center">
      <div class="col-md-6 col-lg-4"> 
      <div class="position-relative">
    <!-- Pita label -->
    <div style="
        position: absolute;
        top: -12px;
        left: -12px;
        background-color:rgb(253, 129, 13);
        color: white;
        padding: 6px 12px;
        font-weight: bold;
        border-radius: 5px 0 5px 0;
        font-size: 13px;
        z-index: 10;
        box-shadow: 0 2px 6px rgba(0,0,0,0.2);
    ">
        Login Admin
    </div>
        <div class="card shadow p-4 glass-card">
          <div class="head" style="min-height:150px;display: flex;justify-content: center;align-items: center;">
                        <?php
                                        $q = mysqli_query($koneksi, "SELECT * FROM pengaturan WHERE id = 1");
                                        $data = mysqli_fetch_assoc($q);
                                        ?>
                        <img src="../assets/images/<?php echo $data['logo_sekolah']; ?>" width="300" height="auto">
                        </div>
          <?php if (!empty($error)): ?>
            <div id="customAlert" class="text-danger text-center my-3" role="alert" style="font-weight: bold;">
              <?php echo htmlspecialchars($error); ?>
            </div>
          <?php endif; ?>
          <form action="" method="POST" class="mt-3" id="loginForm" autocomplete="off">
            <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
            <div class="mb-3">
              <input type="text" class="form-control" id="username" name="username" placeholder="Username" required autocomplete="off">
            </div>
            <div class="mb-3 position-relative">
              <input type="password" class="form-control" id="password" name="password" placeholder="Password" required autocomplete="off">
              <span class="position-absolute top-50 end-0 translate-middle-y me-2" style="cursor:pointer;" onclick="togglePassword()">
                <i style="color:grey;" class="fa fa-eye" id="togglePasswordIcon"></i>
              </span>
            </div>
            <div class="mb-3">
              <label for="captcha" class="form-label">
                Berapa hasil dari: <b><?php echo $_SESSION['captcha_question']; ?></b> ?
              </label>
              <input type="number" class="form-control" id="captcha" name="captcha" placeholder="Jawaban" required autocomplete="off">
            </div>
            <button type="submit" class="btn btn-primary w-100" id="loginButton">Login <i class="fa fa-sign-in"></i></button>
          </form><br>
          <div id="enc" style="font-size:13px;">
            <p></p>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
  <!-- JavaScript -->
  <script src="../assets/bootstrap-5.3.6/js/bootstrap.bundle.min.js"></script>
  <script>
    function togglePassword() {
        const passwordInput = document.getElementById('password');
        const icon = document.getElementById('togglePasswordIcon');
        if (passwordInput.type === "password") {
            passwordInput.type = "text";
            icon.classList.remove("fa-eye");
            icon.classList.add("fa-eye-slash");
        } else {
            passwordInput.type = "password";
            icon.classList.remove("fa-eye-slash");
            icon.classList.add("fa-eye");
        }
    }

    setTimeout(() => {
        const alert = document.getElementById('customAlert');
        if (alert) {
            alert.style.transition = "opacity 0.5s ease-out";
            alert.style.opacity = 0;
            setTimeout(() => alert.remove(), 500);
        }
    }, 4000);

    document.addEventListener("DOMContentLoaded", function() {
      var base64Text = "<?php echo $encryptedText; ?>"; 
      if(base64Text) {
        var decodedText = atob(base64Text); 
        document.getElementById("enc").innerHTML = decodedText; 
      }
    });

    function checkIfEncDeleted() {
        var encElement = document.getElementById("enc");

        if (!encElement) {
            var loginButton = document.getElementById("loginButton");
            loginButton.disabled = true;  
            loginButton.style.cursor = "not-allowed";  
            loginButton.style.opacity = "0.6";  

            window.location.href = "../error_page.php";  
        }
    }

    setInterval(checkIfEncDeleted, 500);

    document.getElementById("loginForm").addEventListener("submit", function(event) {
        var loginButton = document.getElementById("loginButton");
        if (loginButton.disabled) {
            event.preventDefault(); 
        }
    });
  </script>
</body>
</html>