<!DOCTYPE html>
<html lang="zh-Hant">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap 5 JS（需要 Popper） -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

    <title>Python戀曲遊戲介面範例</title>
 <style>
        /* 全域字體與背景 */
        body {
            margin: 0;
            padding: 0;
            font-family: "Noto Sans TC", "微軟正黑體", sans-serif;
            background: linear-gradient(135deg, #ffdde1 0%, #ee9ca7 100%);
            color: #3a1f2b;
            height: 100vh;
            display: flex;
            flex-direction: column;
        }

        /* 頂部角色狀態欄 */
        #status-bar {
            background: rgba(255, 182, 193, 0.85);
            padding: 10px 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-weight: 600;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }

        #status-bar .heart {
            color: #e63946;
            font-size: 1.4rem;
            margin-right: 6px;
        }

        /* 主體區塊，左右分欄 */
        #main-container {
            flex: 1;
            display: flex;
            padding: 20px;
            gap: 15px;
        }

        /* 左側劇情對話 + 任務提示面板 */
        #left-panel {
            flex: 1;
            display: flex;
            flex-direction: column;
            gap: 15px;
        }

        /* 劇情對話視窗 */
        #dialog-box {
            background: rgba(255, 255, 255, 0.85);
            border-radius: 15px;
            padding: 20px;
            flex-grow: 1;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            font-size: 1.1rem;
            line-height: 1.6;
            overflow-y: auto;
            position: relative;
        }

        #dialog-box .character-name {
            font-weight: 700;
            color: #b8336a;
            margin-bottom: 10px;
        }

        #dialog-box .dialog-text {
            white-space: pre-wrap;
        }

        #dialog-box button.next-btn,
        #dialog-box button.music-toggle-btn {
            background: #f48fb1;
            border: none;
            padding: 8px 14px;
            border-radius: 10px;
            color: white;
            font-weight: 700;
            cursor: pointer;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.2);
            transition: background 0.3s ease;
        }

        #dialog-box button.next-btn:hover,
        #dialog-box button.music-toggle-btn:hover {
            background: #e91e63;
        }

        #dialog-box button.next-btn {
            position: static;
            /* 取消絕對定位 */
        }

        #dialog-box .button-group {
            position: absolute;
            bottom: 15px;
            right: 20px;
            display: flex;
            gap: 10px;
        }


        /* 任務提示面板 */
        #task-panel {
            background: #ffe4ec;
            border-radius: 12px;
            padding: 15px 20px;
            box-shadow: 0 3px 8px rgba(0, 0, 0, 0.1);
            font-size: 0.95rem;
        }

        #task-panel h3 {
            margin-top: 0;
            color: #b8336a;
            margin-bottom: 8px;
        }

        #task-panel ul {
            padding-left: 18px;
            margin: 0;
        }

        #task-panel ul li {
            margin-bottom: 6px;
        }

        /* 右側程式碼輸入區 */
        #code-panel {
            flex: 1;
            display: flex;
            flex-direction: column;
        }

        #code-panel label {
            font-weight: 700;
            margin-bottom: 6px;
            color: #b8336a;
        }

        #code-input {
            flex-grow: 1;
            background:#ecc8d4;
            color: #f8f8f2;
            font-family: 'Source Code Pro', monospace;
            font-size: 1rem;
            padding: 15px;
            border-radius: 12px;
            border: none;
            resize: none;
            box-shadow: inset 0 0 10px #5a2a4b;
        }

        #code-panel button {
            margin-top: 12px;
            padding: 10px 18px;
            background: #f48fb1;
            border: none;
            border-radius: 12px;
            font-weight: 700;
            color: white;
            cursor: pointer;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.2);
            transition: background 0.3s ease;
        }

        #code-panel button:hover {
            background: #e91e63;
        }

        /* 響應式設計，窄螢幕時左右堆疊 */
        @media (max-width: 900px) {
            #main-container {
                flex-direction: column;
                padding: 10px;
            }

            #left-panel,
            #code-panel {
                flex: unset;
                width: 100%;
                height: 300px;
            }

            #code-input {
                height: 200px;
            }

        }

        /* ===== 以下為戀愛夢幻粉紅 modal 美化 ===== */

        /* Modal content */
        .modal-content {
            background: #fff0f5 !important;
            border-radius: 20px !important;
            border: 3px solid #f8bbd0 !important;
            box-shadow: 0 6px 12px rgba(255, 182, 193, 0.3) !important;
        }

        /* Modal header */
        .modal-header {
            border-bottom: 1px dashed #f8bbd0 !important;
            background-color: #ffe4e1 !important;
            color: #6a1b4d !important;
            font-weight: bold !important;
            font-size: 1.2rem !important;
            position: relative !important;
        }

        /* 愛心裝飾 */
        .modal-header::before {
            content: "💗";
            font-size: 1.2rem;
            margin-right: 8px;
        }

        /* Modal body */
        .modal-body {
            color: #6a1b4d !important;
            background-color: #fffafc !important;
        }

        /* Modal footer */
        .modal-footer {
            border-top: 1px dashed #f8bbd0 !important;
            background-color: #fff8fb !important;
        }

        /* 表單輸入欄美化 */
        .form-control {
            background: #fff0f5 !important;
            border: 2px solid #f8bbd0 !important;
            border-radius: 12px !important;
            padding: 10px !important;
            box-shadow: inset 0 0 8px rgba(248, 187, 208, 0.3) !important;
            color: #5a2a4b !important;
            transition: 0.3s ease !important;
        }

        .form-control:focus {
            outline: none !important;
            border-color: #ec407a !important;
            box-shadow: 0 0 10px #f48fb1 !important;
        }

        /* 按鈕戀愛風漸層 */
        .btn-primary {
            background: linear-gradient(to right, #f8bbd0, #f48fb1) !important;
            border: none !important;
            color: white !important;
            font-weight: bold !important;
            padding: 10px 18px !important;
            border-radius: 20px !important;
            box-shadow: 0 4px 8px rgba(244, 143, 177, 0.4) !important;
            transition: all 0.3s ease !important;
        }

        .btn-primary:hover {
            background: linear-gradient(to right, #f48fb1, #ec407a) !important;
            transform: scale(1.05) !important;
            box-shadow: 0 6px 12px rgba(244, 143, 177, 0.6) !important;
        }
    </style>
</head>

<body>


    <!-- 角色狀態欄 -->
    <div id="status-bar">
        <div>
            <span class="heart">❤️</span> 感情值: 80%
        </div>
        <div>
            目前心情: <span id="mood-emoji">🤔</span> <span id="mood-text">期待中</span>
        </div>

        <div>
            關卡: 第 1 關
        </div>
    </div>

    <!-- 主體區 -->
    <div id="main-container">

        <!-- 左側區塊 -->
        <div id="left-panel">
            <!-- 劇情對話 -->
            <div id="dialog-box">
                <!-- 角色圖片 -->
                <div id="character-image-container" style="text-align:center; margin-bottom:10px;">
                    <img id="character-image" src="photo/240909_이주은_李珠珢_(cropped).png" alt="角色表情"
                        style="width:120px; height:auto; border-radius:50%; border: 3px solid #b8336a; box-shadow: 0 0 10px #f48fb1;">
                </div>
                <div class="character-name">她說：</div>
                <div class="dialog-text">
                    「嗨！很高興認識你，請輸入你的名字吧！」
                </div>
                <div class="button-group">
                    <button id="music-toggle-btn" class="music-toggle-btn">播放/暫停音樂</button>
                    <button class="next-btn">下一步 ▶</button>
                </div>
            </div>


            <!-- 任務提示 -->
            <div id="task-panel">
                <h3>任務目標</h3>
                <ul>
                    <li>使用 <code>input()</code> 讀取你的名字</li>
                    <li>使用 <code>print()</code> 顯示歡迎訊息</li>
                </ul>
            </div>
        </div>

        <!-- 右側程式碼輸入區 -->
        <div id="code-panel">
            <label for="code-input">請在此撰寫程式碼：</label>
            <textarea id="code-input" spellcheck="false"
                placeholder="例如：&#10;name = input('請輸入名字：')&#10;print('你好，' + name + '！')"></textarea>

            <div style="display: flex; gap: 10px; margin-top: 12px;">
                <button id="run-code-btn"
                    style="flex: 1; background: #f48fb1; color: white; border: none; border-radius: 12px; font-weight: 700; cursor: pointer; box-shadow: 0 2px 6px rgba(0, 0, 0, 0.2); transition: background 0.3s ease;">
                    執行程式
                </button>
                <button id="hint-btn"
                    style="flex: 1; background: #b8336a; color: white; border: none; border-radius: 12px; font-weight: 700; cursor: pointer; box-shadow: 0 2px 6px rgba(0, 0, 0, 0.2); transition: background 0.3s ease;">
                    提示
                </button>
            </div>
        </div>
    </div>

    <audio class="bg-music" src="music/〘韓繁中字〙BOYNEXTDOOR - Call Me (CHN_KOR Lyrics) - RL2.K림.mp3" autoplay loop>
        你的瀏覽器不支援音樂播放。
    </audio>


    <!-- 歡迎體驗 Modal -->
    <div class="modal fade" id="welcomeModal" tabindex="-1" aria-labelledby="welcomeModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="welcomeModalLabel">歡迎體驗</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="關閉"></button>
                </div>
                <div class="modal-body">
                    歡迎來到 Python 程式學習平台，開始你的第一個程式練習吧！
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" data-bs-dismiss="modal">開始體驗</button>
                </div>
            </div>
        </div>
    </div>


    <!-- 執行程式 Modal -->
    <div class="modal fade" id="runModal" tabindex="-1" aria-labelledby="runModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="runModalLabel">執行結果</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="關閉"></button>
                </div>
                <div class="modal-body">
                    模擬執行：你好，使用者！
                </div>
            </div>
        </div>
    </div>

    <!-- 提示 Modal -->
    <div class="modal fade" id="hintModal" tabindex="-1" aria-labelledby="hintModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="hintModalLabel">提示</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="關閉"></button>
                </div>
                <div class="modal-body">
                    使用 `input()` 讀取名字，例如：<br>
                    <code>name = input('請輸入名字：')</code><br>
                    然後用 `print()` 顯示出來。
                </div>
            </div>
        </div>
    </div>

</body>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const dialogTextElement = document.querySelector('#dialog-box .dialog-text');
    const fullText = '「嗨！很高興認識你，請輸入你的名字吧！」';
    let index = 0;
    const typingSpeed = 80;

    function typeWriter() {
        if (index < fullText.length) {
            dialogTextElement.textContent += fullText.charAt(index);
            index++;
            setTimeout(typeWriter, typingSpeed);
        }
    }

    dialogTextElement.textContent = '';
    typeWriter();

    const affectionPercent = 80;
    const moodEmojiElem = document.getElementById('mood-emoji');
    const moodTextElem = document.getElementById('mood-text');

    function updateMood(percent) {
        if (percent >= 80) {
            moodEmojiElem.textContent = '😍';
            moodTextElem.textContent = '心動不已';
        } else if (percent >= 60) {
            moodEmojiElem.textContent = '😊';
            moodTextElem.textContent = '期待中';
        } else if (percent >= 40) {
            moodEmojiElem.textContent = '😐';
            moodTextElem.textContent = '平靜如水';
        } else if (percent >= 20) {
            moodEmojiElem.textContent = '😟';
            moodTextElem.textContent = '有點失落';
        } else {
            moodEmojiElem.textContent = '💔';
            moodTextElem.textContent = '心碎了';
        }
    }

    updateMood(affectionPercent);

    const music = document.querySelector('.bg-music');
    const musicBtn = document.getElementById('music-toggle-btn');
    musicBtn.addEventListener('click', () => {
        if (music.paused) {
            music.play();
            musicBtn.textContent = '暫停音樂';
        } else {
            music.pause();
            musicBtn.textContent = '播放音樂';
        }
    });

    document.getElementById('run-code-btn').addEventListener('click', () => {
        const runModal = new bootstrap.Modal(document.getElementById('runModal'));
        runModal.show();
    });

    document.getElementById('hint-btn').addEventListener('click', () => {
        const hintModal = new bootstrap.Modal(document.getElementById('hintModal'));
        hintModal.show();
    });

    const welcomeModal = new bootstrap.Modal(document.getElementById('welcomeModal'));
    welcomeModal.show();

    console.log("✅ JS 已成功載入並執行！");
});

</script>



</html>