<?php
session_start();

if (isset($_SESSION['username'])) {
    header('Location: index.php');
    exit;
}

if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $csrfToken = $_POST['csrf_token'] ?? '';
    if (!hash_equals($_SESSION['csrf_token'], $csrfToken)) {
        $error = 'Requête invalide. Veuillez réessayer.';
    } else {
        $usersFile = __DIR__ . '/data/users.json';
        $inputUsername = trim($_POST['username'] ?? '');
        $inputPassword = $_POST['password'] ?? '';

        if ($inputUsername === '' || $inputPassword === '') {
            $error = 'Veuillez remplir tous les champs.';
        } elseif (!file_exists($usersFile)) {
            $error = 'Fichier utilisateurs introuvable.';
        } else {
            $users = json_decode(file_get_contents($usersFile), true) ?? [];
            $found = false;
            $hashedPassword = '';
            foreach ($users as $user) {
                if ($user['username'] === $inputUsername) {
                    $hashedPassword = $user['password'];
                    break;
                }
            }
            if ($hashedPassword !== '' && password_verify($inputPassword, $hashedPassword)) {
                $found = true;
            }
            if ($found) {
                session_regenerate_id(true);
                $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
                $_SESSION['username'] = $inputUsername;
                $_SESSION['login_time'] = time();
                header('Location: index.php');
                exit;
            } else {
                $error = 'Nom d\'utilisateur ou mot de passe incorrect.';
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Connexion — Pulse Atelier</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;700;800&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="public/assets/css/style.css">
  <style>
    .login-wrapper {
      min-height: 100vh;
      display: flex;
      align-items: center;
      justify-content: center;
    }
    .login-card {
      width: min(420px, calc(100% - 32px));
      background: var(--panel-strong);
      border: 1px solid var(--line);
      border-radius: 28px;
      padding: 36px 32px;
      box-shadow: var(--shadow);
      backdrop-filter: blur(18px);
    }
    .login-card .brand {
      margin-bottom: 28px;
    }
    .login-card h1 {
      margin: 0 0 6px;
      font-size: 1.7rem;
      letter-spacing: -0.04em;
    }
    .login-card .lead {
      margin: 0 0 28px;
      font-size: 0.95rem;
    }
    .field {
      display: flex;
      flex-direction: column;
      gap: 7px;
      margin-bottom: 16px;
    }
    .field label {
      font-size: 0.88rem;
      color: var(--muted);
      font-weight: 600;
    }
    .field input {
      padding: 12px 16px;
      border-radius: 14px;
      border: 1px solid var(--line);
      background: rgba(255,255,255,0.04);
      color: var(--text);
      font: inherit;
      font-size: 1rem;
      outline: none;
      transition: border-color 180ms ease;
    }
    .field input:focus {
      border-color: var(--accent-2);
    }
    .login-btn {
      width: 100%;
      margin-top: 8px;
      padding: 14px;
      font-size: 1rem;
      font-weight: 700;
      color: #08101e;
      background: linear-gradient(135deg, var(--accent), #ffe39f);
      border: 0;
      border-radius: 999px;
      cursor: pointer;
      transition: transform 180ms ease, box-shadow 180ms ease;
      box-shadow: 0 16px 30px rgba(246,196,94,0.22);
    }
    .login-btn:hover {
      transform: translateY(-2px);
    }
    .error-msg {
      margin-bottom: 16px;
      padding: 12px 16px;
      border-radius: 14px;
      background: rgba(255, 80, 80, 0.12);
      border: 1px solid rgba(255, 80, 80, 0.25);
      color: #ff9a9a;
      font-size: 0.9rem;
    }
  </style>
</head>
<body>
  <div class="noise"></div>
  <div class="login-wrapper">
    <div class="login-card">
      <div class="brand">
        <span class="brand-mark">PA</span>
      </div>
      <h1>Connexion</h1>
      <p class="lead">Accédez à Pulse Atelier.</p>

      <?php if ($error !== ''): ?>
        <div class="error-msg"><?= htmlspecialchars($error) ?></div>
      <?php endif; ?>

      <form method="post" action="login.php">
        <div class="field">
          <label for="username">Nom d'utilisateur</label>
          <input
            type="text"
            id="username"
            name="username"
            value="<?= htmlspecialchars($_POST['username'] ?? '') ?>"
            autocomplete="username"
            required
          >
        </div>
        <div class="field">
          <label for="password">Mot de passe</label>
          <input
            type="password"
            id="password"
            name="password"
            autocomplete="current-password"
            required
          >
        </div>
        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token']) ?>">
        <button type="submit" class="login-btn">Se connecter</button>
      </form>
    </div>
  </div>
</body>
</html>
