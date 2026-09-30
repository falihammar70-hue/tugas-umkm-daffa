<?php
include 'config/koneksi.php';

if (isset($_POST['register'])) {
    $username = trim($_POST['username'] ?? '');
    $password = trim($_POST['password'] ?? '');

    if ($username === '' || $password === '') {
        echo "<script>alert('Username dan password harus diisi.');</script>";
    } else {
        $username = mysqli_real_escape_string($koneksi, $username);
        $passwordHash = md5($password);

        $checkUser = mysqli_query($koneksi, "SELECT * FROM tb_user WHERE username = '$username' LIMIT 1");
        if ($checkUser && mysqli_num_rows($checkUser) > 0) {
            echo "<script>alert('Username sudah terdaftar. Gunakan username lain.');</script>";
        } else {
          $email = $username . '@local.invalid';
          $register = mysqli_query($koneksi, "INSERT INTO tb_user (nama, email, username, password, hp, alamat, role) VALUES ('$username', '$email', '$username', '$passwordHash', '', '', 'pelanggan')");

            if ($register) {
                echo "<script>alert('Registrasi berhasil. Silakan login.'); window.location.href='login.php';</script>";
            } else {
                echo "<script>alert('Registrasi gagal. Silakan coba lagi.');</script>";
            }
        }
    }
}
?>

<!doctype html>
<html lang="id">
  <head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <meta name="description" content="" />
    <meta
      name="author"
      content="Mark Otto, Jacob Thornton, and Bootstrap contributors"
    />
    <meta name="generator" content="Hugo 0.111.3" />
    <title>Register</title>
    <link
      rel="canonical"
      href="https://getbootstrap.com/docs/5.3/examples/sign-in/"
    />
    <meta name="color-scheme" content="light dark" />
    <meta name="theme-color" content="#f4f7f5" />
    <link href="sign-in.css" rel="stylesheet" />
    <style>
      :root {
        --green: #047857;
        --green-dark: #065f46;
        --green-soft: #ecfdf5;
        --bg: #f4f7f5;
        --card: #ffffff;
        --border: #e2e8e4;
        --text: #1f2937;
        --muted: #6b7280;
      }

      * { box-sizing: border-box; margin: 0; padding: 0; }

      body {
        display: grid;
        place-items: center;
        width: 100%;
        min-height: 100vh;
        min-height: 100svh;
        padding: 1.5rem;
        background: var(--bg);
        color: var(--text);
        font-family: system-ui, -apple-system, "Segoe UI", Roboto, Arial, sans-serif;
      }

      .d-none { display: none !important; }

      .form-signin {
        width: 100%;
        max-width: 400px;
        margin: auto;
        padding: 2.5rem 2rem;
        background: var(--card);
        border: 1px solid var(--border);
        border-radius: 16px;
        box-shadow: 0 4px 24px rgba(0, 0, 0, 0.05);
      }

      .form-signin h1 {
        font-size: 1.5rem;
        font-weight: 700;
        line-height: 1.3;
        text-align: center;
      }

      .subtitle {
        margin: 0.5rem 0 2rem;
        font-size: 0.9rem;
        text-align: center;
        color: var(--muted);
      }

      .field { margin-bottom: 1.1rem; }

      .field label {
        display: block;
        margin-bottom: 0.4rem;
        font-size: 0.85rem;
        font-weight: 600;
      }

      .field input {
        width: 100%;
        padding: 0.7rem 0.9rem;
        font-size: 0.95rem;
        color: var(--text);
        background: #fff;
        border: 1px solid var(--border);
        border-radius: 10px;
        transition: border-color 0.15s, box-shadow 0.15s;
      }

      .field input::placeholder { color: #a3aab3; }

      .field input:focus {
        outline: none;
        border-color: var(--green);
        box-shadow: 0 0 0 3px rgba(4, 120, 87, 0.15);
      }

      .password-box { position: relative; }
      .password-box input { padding-right: 4.5rem; }

      .toggle-pass {
        position: absolute;
        top: 50%;
        right: 0.5rem;
        transform: translateY(-50%);
        padding: 0.3rem 0.6rem;
        font-size: 0.8rem;
        font-weight: 600;
        color: var(--green);
        background: transparent;
        border: 0;
        border-radius: 6px;
        cursor: pointer;
      }

      .toggle-pass:hover { background: var(--green-soft); }

      .form-signin .btn {
        width: 100%;
        padding: 0.8rem;
        font-size: 0.95rem;
        font-weight: 600;
        color: #fff;
        background: var(--green);
        border: 0;
        border-radius: 10px;
        cursor: pointer;
        transition: background 0.15s;
      }

      .form-signin .btn:hover { background: var(--green-dark); }

      .form-signin .btn:focus-visible {
        outline: 3px solid rgba(4, 120, 87, 0.35);
        outline-offset: 2px;
      }

      .back {
        display: block;
        margin-top: 1rem;
        font-size: 0.85rem;
        text-align: center;
        color: var(--muted);
        text-decoration: none;
      }

      .back:hover { color: var(--green); }

      .footer {
        margin-top: 1.5rem;
        font-size: 0.75rem;
        text-align: center;
        color: #9ca3af;
      }

      .bd-mode-toggle { display: none; }

      @media (prefers-color-scheme: dark) {
        :root {
          --bg: #101714;
          --card: #18211d;
          --border: #2b3932;
          --text: #e5eee8;
          --muted: #a7b5ad;
          --green-soft: #173c2f;
        }

        .field input { background: #101714; }
        .field input::placeholder { color: #84958a; }
        .footer { color: #84958a; }
      }

      .bd-placeholder-img {
        font-size: 1.125rem;
        text-anchor: middle;
        -webkit-user-select: none;
        -moz-user-select: none;
        user-select: none;
      }
      @media (min-width: 768px) {
        .bd-placeholder-img-lg {
          font-size: 3.5rem;
        }
      }
      .b-example-divider {
        width: 100%;
        height: 3rem;
        background-color: #0000001a;
        border: solid rgba(0, 0, 0, 0.15);
        border-width: 1px 0;
        box-shadow:
          inset 0 0.5em 1.5em #0000001a,
          inset 0 0.125em 0.5em #00000026;
      }
      .b-example-vr {
        flex-shrink: 0;
        width: 1.5rem;
        height: 100vh;
      }
      .bi {
        vertical-align: -0.125em;
        fill: currentColor;
      }
      .nav-scroller {
        position: relative;
        z-index: 2;
        height: 2.75rem;
        overflow-y: hidden;
      }
      .nav-scroller .nav {
        display: flex;
        flex-wrap: nowrap;
        padding-bottom: 1rem;
        margin-top: -1px;
        overflow-x: auto;
        text-align: center;
        white-space: nowrap;
        -webkit-overflow-scrolling: touch;
      }
      .btn-bd-primary {
        --bd-violet-bg: #712cf9;
        --bd-violet-rgb: 112.520718, 44.062154, 249.437846;
        --bs-btn-font-weight: 600;
        --bs-btn-color: var(--bs-white);
        --bs-btn-bg: var(--bd-violet-bg);
        --bs-btn-border-color: var(--bd-violet-bg);
        --bs-btn-hover-color: var(--bs-white);
        --bs-btn-hover-bg: #6528e0;
        --bs-btn-hover-border-color: #6528e0;
        --bs-btn-focus-shadow-rgb: var(--bd-violet-rgb);
        --bs-btn-active-color: var(--bs-btn-hover-color);
        --bs-btn-active-bg: #5a23c8;
        --bs-btn-active-border-color: #5a23c8;
      }
      .bd-mode-toggle {
        z-index: 1500;
      }
      .bd-mode-toggle .bi {
        width: 1em;
        height: 1em;
      }
      .bd-mode-toggle .dropdown-menu .active .bi {
        display: block !important;
      }
    </style>
  </head>
  <body class="d-flex align-items-center py-4 bg-body-tertiary">
    <svg xmlns="http://www.w3.org/2000/svg" class="d-none">
      <symbol id="check2" viewBox="0 0 16 16">
        <path
          d="M13.854 3.646a.5.5 0 0 1 0 .708l-7 7a.5.5 0 0 1-.708 0l-3.5-3.5a.5.5 0 1 1 .708-.708L6.5 10.293l6.646-6.647a.5.5 0 0 1 .708 0z"
        ></path>
      </symbol>
      <symbol id="circle-half" viewBox="0 0 16 16">
        <path
          d="M8 15A7 7 0 1 0 8 1v14zm0 1A8 8 0 1 1 8 0a8 8 0 0 1 0 16z"
        ></path>
      </symbol>
      <symbol id="moon-stars-fill" viewBox="0 0 16 16">
        <path
          d="M6 .278a.768.768 0 0 1 .08.858 7.208 7.208 0 0 0-.878 3.46c0 4.021 3.278 7.277 7.318 7.277.527 0 1.04-.055 1.533-.16a.787.787 0 0 1 .81.316.733.733 0 0 1-.031.893A8.349 8.349 0 0 1 8.344 16C3.734 16 0 12.286 0 7.71 0 4.266 2.114 1.312 5.124.06A.752.752 0 0 1 6 .278z"
        ></path>
        <path
          d="M10.794 3.148a.217.217 0 0 1 .412 0l.387 1.162c.173.518.579.924 1.097 1.097l1.162.387a.217.217 0 0 1 0 .412l-1.162.387a1.734 1.734 0 0 0-1.097 1.097l-.387 1.162a.217.217 0 0 1-.412 0l-.387-1.162A1.734 1.734 0 0 0 9.31 6.593l-1.162-.387a.217.217 0 0 1 0-.412l1.162-.387a1.734 1.734 0 0 0 1.097-1.097l.387-1.162zM13.863.099a.145.145 0 0 1 .274 0l.258.774c.115.346.386.617.732.732l.774.258a.145.145 0 0 1 0 .274l-.774.258a1.156 1.156 0 0 0-.732.732l-.258.774a.145.145 0 0 1-.274 0l-.258-.774a1.156 1.156 0 0 0-.732-.732l-.774-.258a.145.145 0 0 1 0-.274l.774-.258c.346-.115.617-.386.732-.732L13.863.1z"
        ></path>
      </symbol>
      <symbol id="sun-fill" viewBox="0 0 16 16">
        <path
          d="M8 12a4 4 0 1 0 0-8 4 4 0 0 0 0 8zM8 0a.5.5 0 0 1 .5.5v2a.5.5 0 0 1-1 0v-2A.5.5 0 0 1 8 0zm0 13a.5.5 0 0 1 .5.5v2a.5.5 0 0 1-1 0v-2A.5.5 0 0 1 8 13zm8-5a.5.5 0 0 1-.5.5h-2a.5.5 0 0 1 0-1h2a.5.5 0 0 1 .5.5zM3 8a.5.5 0 0 1-.5.5h-2a.5.5 0 0 1 0-1h2A.5.5 0 0 1 3 8zm10.657-5.657a.5.5 0 0 1 0 .707l-1.414 1.415a.5.5 0 1 1-.707-.708l1.414-1.414a.5.5 0 0 1 .707 0zm-9.193 9.193a.5.5 0 0 1 0 .707L3.05 13.657a.5.5 0 0 1-.707-.707l1.414-1.414a.5.5bu 0 0 1 .707 0zm9.193 2.121a.5.5 0 0 1-.707 0l-1.414-1.414a.5.5 0 0 1 .707-.707l1.414 1.414a.5.5 0 0 1 0 .707zM4.464 4.465a.5.5 0 0 1-.707 0L2.343 3.05a.5.5 0 1 1 .707-.707l1.414 1.414a.5.5 0 0 1 0 .708z"
        ></path>
      </symbol>
    </svg>
    <div
      class="dropdown position-fixed bottom-0 end-0 mb-3 me-3 bd-mode-toggle"
    >
      <button
        class="btn btn-bd-primary py-2 dropdown-toggle d-flex align-items-center"
        id="bd-theme"
        type="button"
        aria-expanded="false"
        data-bs-toggle="dropdown"
        aria-label="Toggle theme (auto)"
      >
        <svg class="bi my-1 theme-icon-active" aria-hidden="true">
          <use href="#circle-half"></use>
        </svg>
        <span class="visually-hidden" id="bd-theme-text">Toggle theme</span>
      </button>
      <ul
        class="dropdown-menu dropdown-menu-end shadow"
        aria-labelledby="bd-theme-text"
      >
        <li>
          <button
            type="button"
            class="dropdown-item d-flex align-items-center"
            data-bs-theme-value="light"
            aria-pressed="false"
          >
            <svg class="bi me-2 opacity-50" aria-hidden="true">
              <use href="#sun-fill"></use>
            </svg>
            Light
            <svg class="bi ms-auto d-none" aria-hidden="true">
              <use href="#check2"></use>
            </svg>
          </button>
        </li>
        <li>
          <button
            type="button"
            class="dropdown-item d-flex align-items-center"
            data-bs-theme-value="dark"
            aria-pressed="false"
          >
            <svg class="bi me-2 opacity-50" aria-hidden="true">
              <use href="#moon-stars-fill"></use>
            </svg>
            Dark
            <svg class="bi ms-auto d-none" aria-hidden="true">
              <use href="#check2"></use>
            </svg>
          </button>
        </li>
        <li>
          <button
            type="button"
            class="dropdown-item d-flex align-items-center active"
            data-bs-theme-value="auto"
            aria-pressed="true"
          >
            <svg class="bi me-2 opacity-50" aria-hidden="true">
              <use href="#circle-half"></use>
            </svg>
            Auto
            <svg class="bi ms-auto d-none" aria-hidden="true">
              <use href="#check2"></use>
            </svg>
          </button>
        </li>
      </ul>
    </div>
    <main class="form-signin w-100 m-auto">
      <h1>Buat Akun</h1>
      <p class="subtitle">Daftar untuk mulai berbelanja.</p>
      <form method="POST" action="">
        <div class="field">
          <label for="username">Username</label>
          <input type="text" id="username" name="username" placeholder="Masukkan username"
                 autocomplete="username" required autofocus />
        </div>
        <div class="field">
          <label for="password">Password</label>
          <div class="password-box">
            <input type="password" id="password" name="password" placeholder="Buat password"
                   autocomplete="new-password" required />
            <button type="button" class="toggle-pass" id="togglePass">Lihat</button>
          </div>
        </div>
        <button class="btn" type="submit" name="register">Daftar</button>
      </form>
      <a href="login.php" class="back">Sudah punya akun? Masuk</a>
      <p class="footer">&copy; 2026 Griya Pengantin Ummi</p>
    </main>
    <script>
      const pass = document.getElementById('password');
      const btn = document.getElementById('togglePass');
      btn.addEventListener('click', () => {
        const show = pass.type === 'password';
        pass.type = show ? 'text' : 'password';
        btn.textContent = show ? 'Sembunyi' : 'Lihat';
      });
    </script>
  </body>
</html>