<?php
session_start();

// 資料庫連線
$servername = "localhost";
$db_username = "root";
$db_password = "";
$dbname = "python_love_game";

try {
    $pdo = new PDO("mysql:host=$servername;dbname=$dbname;charset=utf8", $db_username, $db_password);
} catch (PDOException $e) {
    die("資料庫連線失敗：" . $e->getMessage());
}
$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['form_mode'])) {
    $formMode = $_POST['form_mode'];
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);
    $error = "";
    $success = "";

    if ($formMode === 'register') {
        // 檢查是否已存在帳號
        $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
        $stmt->execute([$username]);

        if ($stmt->rowCount() > 0) {
            $error = "此帳號已被註冊，請使用其他帳號。";
        } else {
            // 密碼加密並寫入資料庫
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("INSERT INTO users (username, password) VALUES (?, ?)");
            if ($stmt->execute([$username, $hash])) {
                $success = "註冊成功，請登入！";
            } else {
                $error = "註冊失敗，請稍後再試。";
            }
        }
    } elseif ($formMode === 'login') {
        $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
        $stmt->execute([$username]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user'] = $user['username'];
            $success = "登入成功！即將跳轉首頁...";
            // 不用 header 跳轉，由 JS 控制跳轉
        } else {
            $error = "帳號或密碼錯誤。";
        }
    }
}




?>



<!DOCTYPE html>
<html lang="zh-Hant">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Python 戀曲 - 登入 / 註冊</title>
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+TC&display=swap" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
    <style>
        /* 背景輪播容器 */
        .background-slideshow {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: -1;
            background-size: cover;
            background-position: center;
            animation: slideshow 20s infinite;
        }

        @keyframes slideshow {
            0% {
                background-image: url('photo/36146c8f0b3b8617c216d4bc9882c6b7.png');
            }

            33% {
                background-image: url('photo/70ea5c714cc8a8dbd8bcdb973dd58383.png');
            }

            66% {
                background-image: url('photo/be898144743cd473a8cc3bf41c26a20f.png');
            }

            100% {
                background-image: url('photo/e894900b47f0735a2419efedae0d7eb3.png');
            }
        }

        body {
            margin: 0;
            padding: 0;
            font-family: "Noto Sans TC", "微軟正黑體", sans-serif;
            background: linear-gradient(135deg, #ffdde1 0%, #ee9ca7 100%);
            color: #3a1f2b;
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .auth-card {
            background: rgba(255, 255, 255, 0.85);
            padding: 30px 25px;
            border-radius: 20px;
            box-shadow: 0 8px 20px rgba(184, 72, 113, 0.3);
            width: 100%;
            max-width: 400px;
        }

        h3 {
            text-align: center;
            margin-bottom: 30px;
            font-weight: 700;
            color: #b8336a;
        }

        input.form-control {
            border-radius: 12px;
            border: 1.5px solid #b8336a;
            padding: 10px 12px;
            font-size: 1rem;
            transition: border-color 0.3s ease;
        }

        input.form-control:focus {
            border-color: #e91e63;
            box-shadow: 0 0 8px rgba(233, 30, 99, 0.5);
            outline: none;
        }

        button.btn-primary {
            background: #f48fb1;
            border: none;
            border-radius: 12px;
            font-weight: 700;
            transition: background 0.3s ease;
        }

        button.btn-primary:hover {
            background: #e91e63;
        }

        button.btn-secondary {
            background: #f48fb1;
            border: none;
            border-radius: 12px;
            font-weight: 700;
            transition: background 0.3s ease;
            color: white;
        }

        button.btn-secondary:hover {
            background: #e91e63;
        }

        .btn-outline-primary {
            border-color: #b8336a;
            color: #b8336a;
            border-radius: 12px;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .btn-outline-primary:hover {
            background-color: #b8336a;
            color: white;
        }

        .btn-outline-secondary {
            border-color: #b8336a;
            color: #b8336a;
            border-radius: 12px;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .btn-outline-secondary:hover {
            background-color: #b8336a;
            color: white;
        }

        .alert-danger {
            background-color: #ffd6dc;
            border-color: #e91e63;
            color: #b8336a;
            border-radius: 12px;
        }

        .alert-success {
            background-color: #ffe4ec;
            border-color: #b8336a;
            color: #b8336a;
            border-radius: 12px;
        }

        .typed-text {
            font-size: 1rem;
            font-weight: 600;
            text-align: center;
            color: #b8336a;
            height: 1.5rem;
            margin-bottom: 20px;
            min-height: 1.5rem;
        }

        #datetime {
            text-align: center;
            font-size: 0.95rem;
            font-weight: 500;
            color: #6b1b3f;
            margin-top: 15px;
        }

        /* 背景半透明漸層 */
        .modal-backdrop.show {
            background: linear-gradient(135deg, rgba(255, 182, 193, 0.7), rgba(221, 160, 221, 0.7));
        }

        /* Modal 內容夢幻感 */
        .modal-content {
            background: linear-gradient(135deg, #f8e1f4, #d4c1ec);
            border-radius: 20px;
            box-shadow: 0 8px 30px rgba(186, 85, 211, 0.4);
            color: #5a2a6a;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            text-align: center;
            padding: 2rem;
            font-weight: 600;
            font-size: 1.2rem;
        }

        /* 按鈕夢幻風 (可選) */
        .modal-footer .btn {
            background: linear-gradient(135deg, #c08de0, #ea9eed);
            border: none;
            color: white;
            border-radius: 15px;
            box-shadow: 0 4px 15px rgba(234, 158, 237, 0.6);
            transition: background 0.3s ease;
        }

        .modal-footer .btn:hover {
            background: linear-gradient(135deg, #ea9eed, #c08de0);
        }
    </style>
</head>

<body>
    <div class="background-slideshow"></div>
    <div class="auth-card" id="mainContent">
        <h3>Python 戀曲</h3>

        <!-- 項目2：打字效果區塊 -->
        <div class="typed-text" id="typedText"></div>



        <!-- 登入註冊按鈕 -->
        <div class="text-center mb-3">
            <button class="btn btn-outline-primary me-2" id="showLogin">登入</button>
            <button class="btn btn-outline-secondary" id="showRegister">註冊</button>
        </div>

        <!-- 登入表單 -->
        <form method="post" id="loginForm" style="display: none;">
            <input type="hidden" name="form_mode" value="login" />
            <input type="text" name="username" class="form-control mb-2" placeholder="帳號" required />
            <div class="mb-3">
                <input type="password" id="loginPassword" name="password" class="form-control" placeholder="密碼" required />
                <div class="form-check mt-1">
                    <input class="form-check-input" type="checkbox" id="showLoginPassword" />
                    <label class="form-check-label" for="showLoginPassword">顯示密碼</label>
                </div>
            </div>
            <button type="submit" name="login" class="btn btn-primary w-100">登入</button>
        </form>

        <!-- 註冊表單 -->
        <form method="post" id="registerForm" style="display: none;">
            <input type="hidden" name="form_mode" value="register" />
            <input type="text" name="username" class="form-control mb-2" placeholder="帳號" required />
            <div class="mb-3">
                <input type="password" id="registerPassword" name="password" class="form-control" placeholder="密碼" required />
                <div class="form-check mt-1">
                    <input class="form-check-input" type="checkbox" id="showRegisterPassword" />
                    <label class="form-check-label" for="showRegisterPassword">顯示密碼</label>
                </div>
            </div>
            <button type="submit" name="register" class="btn btn-secondary w-100">註冊</button>
        </form>


        <!-- 項目1：時間顯示 -->
        <div id="datetime"></div>
    </div>

    <!-- 成功訊息 Modal -->
    <div class="modal fade" id="successModal" tabindex="-1" aria-labelledby="successModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-success">
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title" id="successModalLabel">成功訊息</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body text-success" id="successMessage">
                    <!-- 成功訊息內容會從 JS 填入 -->
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-success" data-bs-dismiss="modal">關閉</button>
                </div>
            </div>
        </div>
    </div>

    <!-- 錯誤訊息 Modal -->
    <div class="modal fade" id="errorModal" tabindex="-1" aria-labelledby="errorModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-danger">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title" id="errorModalLabel">錯誤訊息</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body text-danger" id="errorMessage">
                    <!-- 錯誤訊息內容會從 JS 填入 -->
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger" data-bs-dismiss="modal">關閉</button>
                </div>
            </div>
        </div>
    </div>


    <!-- 成功提示 Modal -->
    <div class="modal fade" id="successModal" tabindex="-1" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
        <div class="modal-dialog modal-sm modal-dialog-centered">
            <div class="modal-content border-0 shadow rounded-4">
                <div class="modal-body text-center py-4">
                    <svg xmlns="http://www.w3.org/2000/svg" class="mb-3 text-success" width="64" height="64" fill="currentColor" viewBox="0 0 16 16">
                        <path d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0zM7 11.414 4.707 9.12l-.707.707L7 12.828l6-6-.707-.707-5.293 5.293z" />
                    </svg>
                    <h4 class="mb-2 fw-bold text-success">登入成功！</h4>
                    <p class="text-muted mb-0">3 秒後自動跳轉首頁...</p>
                    <div class="spinner-border text-success mt-3" role="status" style="width: 2.5rem; height: 2.5rem;">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>


<!-- 引入 Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<script>
    document.getElementById('showLoginPassword').addEventListener('change', function () {
        const pwd = document.getElementById('loginPassword');
        pwd.type = this.checked ? 'text' : 'password';
    });

    document.getElementById('showRegisterPassword').addEventListener('change', function () {
        const pwd = document.getElementById('registerPassword');
        pwd.type = this.checked ? 'text' : 'password';
    });

    // 項目1：時間顯示
    function updateDateTime() {
        const now = new Date();
        const formatted = now.toLocaleString("zh-Hant-TW", {
            year: "numeric",
            month: "2-digit",
            day: "2-digit",
            hour: "2-digit",
            minute: "2-digit",
            second: "2-digit"
        });
        document.getElementById("datetime").textContent = `目前時間：${formatted}`;
    }
    setInterval(updateDateTime, 1000);
    updateDateTime();

    // 項目2：打字效果
    const message = "請先登入或註冊才能體驗遊戲喔！❤️";
    const typedText = document.getElementById("typedText");
    let charIndex = 0;

    function typeEffect() {
        if (charIndex < message.length) {
            typedText.textContent += message.charAt(charIndex);
            charIndex++;
            setTimeout(typeEffect, 100);
        }
    }

    // 項目3：切換登入註冊表單
    const showLoginBtn = document.getElementById('showLogin');
    const showRegisterBtn = document.getElementById('showRegister');
    const loginForm = document.getElementById('loginForm');
    const registerForm = document.getElementById('registerForm');

    loginForm.style.display = 'block';
    showLoginBtn.disabled = true;

    showLoginBtn.addEventListener('click', () => {
        loginForm.style.display = 'block';
        registerForm.style.display = 'none';
        showLoginBtn.disabled = true;
        showRegisterBtn.disabled = false;
    });

    showRegisterBtn.addEventListener('click', () => {
        loginForm.style.display = 'none';
        registerForm.style.display = 'block';
        showLoginBtn.disabled = false;
        showRegisterBtn.disabled = true;
    });

    // Modal backdrop 修正
    const errorModalEl = document.getElementById('errorModal');
    if (errorModalEl) {
        errorModalEl.addEventListener('hidden.bs.modal', () => {
            const backdrops = document.querySelectorAll('.modal-backdrop');
            backdrops.forEach(backdrop => backdrop.remove());
            document.body.classList.remove('modal-open');
        });
    }

    // 載入時處理訊息顯示與跳轉邏輯
    window.addEventListener('load', () => {
        typeEffect();

        const errorMsg = <?php echo json_encode($error); ?>;
        const successMsg = <?php echo json_encode($success); ?>;

        if (errorMsg) {
            const errorModal = new bootstrap.Modal(document.getElementById('errorModal'));
            document.getElementById('errorMessage').textContent = errorMsg;
            errorModal.show();
        }

        if (successMsg) {
            const successModal = new bootstrap.Modal(document.getElementById('successModal'));
            document.getElementById('successMessage').textContent = successMsg;
            successModal.show();

            setTimeout(() => {
                if (successMsg.includes("註冊成功")) {
                    // 註冊成功：切換回登入表單
                    loginForm.style.display = 'block';
                    registerForm.style.display = 'none';
                    showLoginBtn.disabled = true;
                    showRegisterBtn.disabled = false;
                    successModal.hide();
                } else if (successMsg.includes("登入成功")) {
                    // 登入成功：跳轉至首頁
                    window.location.href = 'index.php';
                }
            }, 3000);
        }
    });
</script>

</html>