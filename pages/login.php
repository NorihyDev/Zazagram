<?php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../includes/auth.php';

if (is_logged_in()) {
    header('Location: ' . BASE_URL . '/pages/feed.php');
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $login    = trim($_POST['login'] ?? '');      // username or email
    $password = $_POST['password'] ?? '';

    if (!$login || !$password) {
        $error = 'Please fill in all fields.';
    } else {
        $users = db_read('users.json');
        // Find by username or email
        $user = db_find_one($users, 'username', $login)
             ?? db_find_one($users, 'email', $login);

        if (!$user || !password_verify($password, $user['password'])) {
            $error = 'Invalid username/email or password.';
        } elseif ($user['is_banned']) {
            $error = 'Your account has been suspended.';
        } else {
            session_regenerate_id(true);
            $_SESSION['user_id']   = $user['id'];
            $_SESSION['username']  = $user['username'];
            $_SESSION['role']      = $user['role'];
            header('Location: ' . BASE_URL . '/pages/feed.php');
            exit;
        }
    }
}

$page_title = 'Login';
$body_class = 'login-page no-nav';
include __DIR__ . '/../includes/header.php';
?>

<div class="auth-container">
    <div class="auth-box login-layout">
        <section class="login-hero">
            <div class="hero-brand">
                <span>Z</span>
            </div>
            <div class="hero-copy">
                <h1>Explorez les sujets que vous aimez.</h1>
                <p>Rejoignez Zazagram pour découvrir des publications, des photos et des amis qui vous inspirent chaque jour.</p>
            </div>
            <div class="hero-visual">
                <div class="hero-card hero-card-large"></div>
                <div class="hero-card hero-card-medium"></div>
                <div class="hero-card hero-card-small"></div>
                <div class="hero-badge">16:45</div>
                <div class="hero-icon">❤</div>
            </div>
        </section>

        <section class="login-panel">
            <div class="panel-inner">
                <div class="panel-heading">
                    <h2>Se connecter à Zazagram</h2>
                </div>

                <?php if ($error): ?>
                    <div class="alert alert-error"><?= htmlspecialchars($error) ?></div>
                <?php endif; ?>

                <form method="POST" class="login-form">
                    <div class="form-group">
                        <input type="text" name="login" placeholder="E-mail ou numéro de mobile"
                               value="<?= htmlspecialchars($_POST['login'] ?? '') ?>" required autofocus>
                    </div>
                    <div class="form-group">
                        <input type="password" name="password" placeholder="Mot de passe" required>
                    </div>
                    <button type="submit" class="btn btn-primary btn-full">Se connecter</button>
                </form>

                <a href="#" class="link-forgot">Mot de passe oublié ?</a>

                <div class="panel-divider"></div>

                <a href="register.php" class="btn btn-outline btn-full btn-create">Créer un nouveau compte</a>

                <div class="panel-brand">Zazagram</div>

                <div class="auth-demo">
                    <p><strong>Demos accounts</strong> (password: <code>password</code>)</p>
                    <div class="demo-accounts">
                        <button class="demo-btn" onclick="fillLogin('alex_photo')">alex_photo (admin)</button>
                        <button class="demo-btn" onclick="fillLogin('mia_creates')">mia_creates</button>
                        <button class="demo-btn" onclick="fillLogin('jordan_fit')">jordan_fit</button>
                    </div>
                </div>
            </div>
        </section>
    </div>
</div>

<script>
function fillLogin(username) {
    document.querySelector('[name="login"]').value = username;
    document.querySelector('[name="password"]').value = 'password';
}
</script>

<?php include __DIR__ . '/../includes/footer.php'; ?>
