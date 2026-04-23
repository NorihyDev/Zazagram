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
include __DIR__ . '/../includes/header.php';
?>

<div class="auth-container">
    <div class="auth-box">
        <div class="auth-logo">
            <h1>Zazagram</h1>
            <p>Sign in to see your feed</p>
        </div>

        <?php if ($error): ?>
            <div class="alert alert-error"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <form method="POST" class="auth-form">
            <div class="form-group">
                <input type="text" name="login" placeholder="Username or email"
                       value="<?= htmlspecialchars($_POST['login'] ?? '') ?>" required autofocus>
            </div>
            <div class="form-group">
                <input type="password" name="password" placeholder="Password" required>
            </div>
            <button type="submit" class="btn btn-primary btn-full">Sign In</button>
        </form>

        <div class="auth-demo">
            <p><strong>Demo accounts</strong> (password: <code>password</code>)</p>
            <div class="demo-accounts">
                <button class="demo-btn" onclick="fillLogin('alex_photo')">alex_photo (admin)</button>
                <button class="demo-btn" onclick="fillLogin('mia_creates')">mia_creates</button>
                <button class="demo-btn" onclick="fillLogin('jordan_fit')">jordan_fit</button>
            </div>
        </div>

        <p class="auth-switch">Don't have an account? <a href="register.php">Sign up</a></p>
    </div>
</div>

<script>
function fillLogin(username) {
    document.querySelector('[name="login"]').value = username;
    document.querySelector('[name="password"]').value = 'password';
}
</script>

<?php include __DIR__ . '/../includes/footer.php'; ?>
