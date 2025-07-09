<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login</title>
    <link rel="stylesheet" href="login_style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
</head>

<body>
    <?php
    session_start();

    if (isset($_SESSION['login_message'])) {
        echo "<div class='alert-message' id='successMessage'>" . $_SESSION['login_message'] . "</div>";
        unset($_SESSION['login_message']); 
    }

    if (isset($_GET['message']) && $_GET['message'] == 'logged_out') {
        echo "<div class='alert-message' id='logoutMessage'>Logged out successfully!</div>";
    }

    if (isset($_GET['error'])) {
        echo "<div class='alert-message' id='errorMessage'>" . htmlspecialchars($_GET['error']) . "</div>";
    }
    ?>

    <div class="container">
        <h1>Welcome! Please login to continue</h1>

        <div class="form-container">
            <form action="login_process.php" method="POST">
                <div id="role-label">
                    <label for="role">Login as</label>
                </div>
                <div class="form-group">
                    <select id="role" name="role" required onchange="showLoginFields()">
                        <option value="" disabled selected>Select your role</option>
                        <option value="student">Student</option>
                        <option value="faculty">Faculty Member</option>
                        <option value="staff">Administrative Staff</option>
                    </select>
                </div>
                <div id="login-fields" style="display: none;">
                    <div class="form-group">
                        <label for="username">Username</label>
                        <input type="text" id="username" name="username" required>
                    </div>
                    <div class="form-group">
                        <label for="password">Password</label>
                        <div class="password-container">
                            <input type="password" id="password" name="password" required>
                            <i id="togglePassword" class="fas fa-eye-slash"></i>
                        </div>
                    </div>
                    <button type="submit">Login</button>
                </div>
            </form>
        </div>
    </div>
    
    <script>
        window.onload = function() {
            const logoutMessage = document.getElementById('logoutMessage');
            if (logoutMessage) {
                setTimeout(function() {
                    logoutMessage.style.display = 'none';
                }, 4000); 
            }

            const errorMessage = document.getElementById('errorMessage');
            if (errorMessage) {
                setTimeout(function() {
                    errorMessage.style.display = 'none';
                }, 4000);
            }

            const successMessage = document.getElementById('successMessage');
            if (successMessage) {
                setTimeout(function() {
                    successMessage.style.display = 'none';
                }, 4000);
            }
        }

        function showLoginFields() {
            const role = document.getElementById('role').value;
            const loginFields = document.getElementById('login-fields');
            if (role) {
                loginFields.style.display = 'block';
            } else {
                loginFields.style.display = 'none';
            }
        }

        const togglePassword = document.getElementById('togglePassword');
        const passwordField = document.getElementById('password');

        togglePassword.addEventListener('click', function () {
            const type = passwordField.type === 'password' ? 'text' : 'password';
            passwordField.type = type;

            this.classList.toggle('fa-eye');
            this.classList.toggle('fa-eye-slash');
        });
    </script>
</body>
</html>