<?php
include 'config.php';

if (isset($_POST['login'])) {
    $username = trim($_POST['username'] ?? '');
    $password = trim($_POST['password'] ?? '');

    if ($username === '' || $password === '') {
        echo "<script>alert('Username dan password harus diisi.');</script>";
    } else {
        $username = mysqli_real_escape_string($koneksi, $username);
        $query = mysqli_query($koneksi, "SELECT * FROM tb_user WHERE username = '$username' LIMIT 1");

        if ($query && mysqli_num_rows($query) > 0) {
            $user = mysqli_fetch_assoc($query);
            $storedPassword = $user['password'] ?? '';

            $isValid = hash_equals(strtolower($storedPassword), md5($password));

            if ($isValid) {
                $_SESSION['is_logged_in'] = true;
                $_SESSION['user_id'] = (int) $user['id'];
                $_SESSION['nama'] = $user['nama'] ?? '';
                $_SESSION['username'] = $user['username'];
                $_SESSION['email'] = $user['email'] ?? '';
                $_SESSION['role'] = $user['role'] ?? 'pelanggan';
                $_SESSION['user'] = [
                    'username' => $user['username'],
                    'role' => $user['role'] ?? 'pelanggan'
                ];
                redirect(($user['role'] ?? 'pelanggan') === 'admin' ? 'admin/dashboard.php' : 'index.php');
            }
        }

        echo "<script>alert('Username atau password salah.');</script>";
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Masuk - Griya Pengantin Ummi</title>

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
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 1.5rem;
            background: var(--bg);
            color: var(--text);
            font-family: system-ui, -apple-system, "Segoe UI", Roboto, Arial, sans-serif;
        }

        .card {
            width: 100%;
            max-width: 400px;
            padding: 2.5rem 2rem;
            background: var(--card);
            border: 1px solid var(--border);
            border-radius: 16px;
            box-shadow: 0 4px 24px rgba(0, 0, 0, 0.05);
        }

        h1 {
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

        label {
            display: block;
            margin-bottom: 0.4rem;
            font-size: 0.85rem;
            font-weight: 600;
        }

        input[type="text"],
        input[type="password"] {
            width: 100%;
            padding: 0.7rem 0.9rem;
            font-size: 0.95rem;
            color: var(--text);
            background: #fff;
            border: 1px solid var(--border);
            border-radius: 10px;
            transition: border-color 0.15s, box-shadow 0.15s;
        }

        input::placeholder { color: #a3aab3; }

        input[type="text"]:focus,
        input[type="password"]:focus {
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

        .remember {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            margin: 0.25rem 0 1.5rem;
            font-size: 0.85rem;
            font-weight: 400;
            color: var(--muted);
            cursor: pointer;
        }

        .remember input {
            width: 1rem;
            height: 1rem;
            accent-color: var(--green);
        }

        .btn {
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

        .btn:hover { background: var(--green-dark); }

        .btn:focus-visible {
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
    </style>
</head>
<body>

    <main class="card">
        <h1>Selamat Datang di Griya Pengantin Ummi</h1>
        <p class="subtitle">Masuk untuk melanjutkan.</p>

        <form action="" method="post">
            <div class="field">
                <label for="username">Username</label>
                <input type="text" id="username" name="username"
                       placeholder="Masukkan username" autocomplete="username" autofocus>
            </div>

            <div class="field">
                <label for="password">Password</label>
                <div class="password-box">
                    <input type="password" id="password" name="password"
                           placeholder="Masukkan password" autocomplete="current-password">
                    <button type="button" class="toggle-pass" id="togglePass">Lihat</button>
                </div>
            </div>

            <label class="remember">
                <input type="checkbox" value="remember-me"> Ingat saya
            </label>

            <button type="submit" name="login" class="btn">Masuk</button>
        </form>

        <a href="index.php" class="back">Kembali ke beranda</a>
        <p class="footer">&copy; 2017–2026 Griya Pengantin Ummi</p>
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