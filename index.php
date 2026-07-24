<?php
session_start();
if (isset($_POST['set_session'])) {
    $_SESSION['bottle_accepted'] = true;
    echo json_encode(["status" => "success"]);
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>WINBIN ECO SYSTEM</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
        body { background-color: #0d1624; color: #ffffff; height: 100vh; overflow: hidden; display: flex; flex-direction: column; }
        header { background-color: #0a111c; padding: 15px 30px; display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid #1a2638; }
        .logo-area { display: flex; align-items: center; gap: 10px; font-weight: bold; font-size: 1.2rem; color: #00e699; letter-spacing: 1px; }
        .status-badge { background-color: #1a2638; padding: 8px 16px; border-radius: 20px; font-size: 0.85rem; color: #8fa0b5; border: 1px solid #2d3d54; }
        .wrapper { display: flex; flex: 1; position: relative; }
        .main-content { flex: 1; padding: 40px; display: flex; justify-content: center; align-items: flex-start; }
        .panel { background-color: #152233; border-radius: 12px; padding: 40px; border: 1px solid #203147; height: 75vh; }
        .full-panel { flex: 1; width: 100%; display: flex; flex-direction: column; justify-content: center; align-items: center; position: relative; max-width: 1200px; }
        h1.brand-title { font-size: 3rem; margin-bottom: 15px; color: #00e699; text-align: center; font-weight: 800; text-transform: uppercase; }
        p.subtitle { color: #8fa0b5; margin-bottom: 35px; text-align: center; font-size: 1.1rem; }
        .panel-title { font-size: 1.4rem; color: #ffffff; font-weight: 600; text-transform: uppercase; margin-bottom: 20px; letter-spacing: 0.5px; text-align: center; }
        
        /* Floating Button Animation styling setup */
        @keyframes floatAnimation {
            0% { transform: translateY(0px); }
            50% { transform: translateY(-8px); }
            100% { transform: translateY(0px); }
        }
        .btn { background-color: #00e699; color: #0d1624; border: none; padding: 18px 45px; font-size: 1.1rem; font-weight: bold; border-radius: 8px; cursor: pointer; transition: background 0.2s; display: inline-flex; align-items: center; gap: 10px; }
        .btn:hover { background-color: #00cc88; }
        .btn-float { animation: floatAnimation 2.5s ease-in-out infinite; }
        
        /* Fixed Button Layout Configuration Container */
        .button-group { display: flex; flex-direction: column; align-items: center; gap: 15px; width: 100%; max-width: 560px; }
        
        .btn-exit { background-color: #1c2d42; color: #8fa0b5; text-transform: uppercase; font-size: 0.9rem; border: 1px solid #2d3d54; padding: 14px 35px; border-radius: 8px; cursor: pointer; transition: all 0.2s; width: 100%; max-width: 200px; text-align: center; }
        .btn-exit:hover { background-color: #253a54; color: #ffffff; }
        .btn-stop { background-color: #ff4d4d; color: #ffffff; padding: 18px 45px; font-size: 1.1rem; font-weight: bold; display: none; width: auto; border-radius: 8px; cursor: pointer; border: none; }
        .btn-stop:hover { background-color: #e63939; }
        
        .camera-box { width: 100%; max-width: 560px; height: 340px; background-color: #0a111c; border-radius: 10px; border: 1px solid #253a54; display: flex; flex-direction: column; justify-content: center; align-items: center; overflow: hidden; margin-bottom: 25px; position: relative; }
        .camera-box video { width: 100%; height: 100%; object-fit: cover; }
        .camera-placeholder-text { color: #536982; font-size: 0.85rem; margin-top: 10px; text-transform: uppercase; letter-spacing: 1px; }
        
        .info-card { width: 100%; max-width: 600px; }
        .step-item, .faq-item { background-color: #0a111c; border: 1px solid #1f2f45; border-radius: 8px; padding: 20px; margin-bottom: 15px; }
        .step-header { display: flex; align-items: center; gap: 12px; margin-bottom: 8px; color: #00e699; font-weight: bold; }
        .step-num { background-color: #00e699; color: #0d1624; width: 24px; height: 24px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 0.85rem; }
        .info-desc { color: #a4b5cb; font-size: 0.9rem; line-height: 1.5; }
        
        .menu-btn { background: none; border: none; cursor: pointer; display: flex; flex-direction: column; gap: 5px; }
        .menu-btn span { display: block; width: 22px; height: 2px; background-color: #ffffff; }
        .sidebar { position: absolute; right: -300px; top: 0; width: 300px; height: 100%; background-color: #0a111c; border-left: 1px solid #1a2638; z-index: 10; padding: 40px 20px; display: flex; flex-direction: column; justify-content: space-between; transition: right 0.3s cubic-bezier(0.4, 0, 0.2, 1); }
        .sidebar.active { right: 0; }
        .sidebar-close { align-self: flex-end; background: none; border: none; color: #8fa0b5; font-size: 1.5rem; cursor: pointer; margin-bottom: 30px; }
        .sidebar-links { display: flex; flex-direction: column; gap: 20px; }
        .nav-link { color: #8fa0b5; text-decoration: none; font-size: 1.05rem; display: flex; align-items: center; gap: 12px; padding: 10px; border-radius: 6px; transition: all 0.2s; cursor: pointer; }
        .nav-link:hover, .nav-link.active-link { color: #ffffff; background-color: #152233; }
        .nav-link-icon { font-size: 1.2rem; }
        .sidebar-footer { font-size: 0.75rem; color: #435469; text-align: center; text-transform: uppercase; letter-spacing: 0.5px; }
        
        .toast-overlay { display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(10, 17, 28, 0.85); z-index: 100; align-items: center; justify-content: center; }
        .toast-box { background: #152233; border: 1px solid #ff4d4d; border-radius: 12px; padding: 40px; text-align: center; max-width: 400px; width: 90%; }
        .toast-msg { font-size: 1.2rem; margin-bottom: 25px; font-weight: 600; }
    </style>
</head>
<body>

    <header>
        <div class="logo-area">♻️ WINBIN ECO SYSTEM</div>
        <div style="display: flex; align-items: center; gap: 20px;">
            <div class="status-badge" id="global-status">Waiting for Scanning</div>
            <button class="menu-btn" onclick="toggleSidebar()">
                <span></span><span></span><span></span>
            </button>
        </div>
    </header>

    <div class="wrapper">
        <div class="sidebar" id="sidebarMenu">
            <div style="display: flex; flex-direction: column;">
                <button class="sidebar-close" onclick="toggleSidebar()">&times;</button>
                <div class="sidebar-links">
                    <div class="nav-link active-link" onclick="switchView('home', this)"><span class="nav-link-icon">🏠</span> Home Screen</div>
                    <div class="nav-link" onclick="switchView('scan', this)"><span class="nav-link-icon">📷</span> AI Bottle Scanner</div>
                    <div class="nav-link" onclick="switchView('how', this)"><span class="nav-link-icon">📋</span> How to Use</div>
                    <div class="nav-link" onclick="switchView('faq', this)"><span class="nav-link-icon">❓</span> Help & FAQs</div>
                </div>
            </div>
            <div class="sidebar-footer">All Rights Reserved © WINBIN</div>
        </div>

        <div class="main-content">
            <div class="panel full-panel">
                
                <!-- VIEW 1: HOME PANEL -->
                <div id="view-home" class="view-section" style="display: flex; flex-direction: column; align-items: center;">
                    <div style="font-size: 4.5rem; margin-bottom: 20px;">♻️</div>
                    <h1 class="brand-title">WINBIN SMART KIOSK</h1>
                    <p class="subtitle">Automated AI Reverse Vending Ecosystem</p>
                    <button class="btn btn-float" onclick="switchView('scan', document.querySelectorAll('.nav-link')[1])">Tap Screen to Recycle</button>
                </div>

                <!-- VIEW 2: FULL WIDTH CAMERA SCANNER -->
                <div id="view-scan" class="view-section" style="display: none; width: 100%; flex-direction: column; align-items: center;">
                    <div class="panel-title">AI CAMERA PROCESSING</div>
                    <p class="subtitle" style="margin-bottom: 25px; font-size: 1rem;">Position the bottle component inside the visual overlay capture box</p>
                    
                    <div class="camera-box">
                        <video id="webcam" autoplay playsinline></video>
                        <canvas id="canvas" width="560" height="340" style="display:none;"></canvas>
                        <div id="camera-placeholder" style="position: absolute; display: flex; flex-direction: column; align-items: center; justify-content: center; pointer-events: none;">
                            <span style="font-size: 2.5rem;">📷</span>
                            <div class="camera-placeholder-text">INTAKE SHUTTER STANDBY</div>
                        </div>
                    </div>
                    
                    <!-- Formulated UI Button Alignment Layout Grid -->
                    <div class="button-group">
                        <button class="btn" id="scan-btn">📷 Tap to Scan Bottle</button>
                        <button class="btn btn-stop" id="stop-btn">🛑 Stop Scanning</button>
                        <button class="btn-exit" onclick="resetTerminal()">Exit Session</button>
                    </div>
                </div>

                <!-- VIEW 3: HOW TO USE -->
                <div id="view-how" class="view-section" style="display: none; width: 100%; flex-direction: column; align-items: center;">
                    <div class="panel-title">How to Use WINBIN 📋</div>
                    <div class="info-card">
                        <div class="step-item">
                            <div class="step-header"><div class="step-num">1</div> Scan First</div>
                            <div class="info-desc">Tap the optical scanner link to bring the AI lens processing configuration online to evaluate materials.</div>
                        </div>
                        <div class="step-item">
                            <div class="step-header"><div class="step-num">2</div> Enter Identity</div>
                            <div class="info-desc">Upon receiving a validation check, type your phone profile on the display keypad node to complete reward tracking.</div>
                        </div>
                    </div>
                </div>

                <!-- VIEW 4: FAQ -->
                <div id="view-faq" class="view-section" style="display: none; width: 100%; flex-direction: column; align-items: center;">
                    <div class="panel-title">Help & Common Queries ❓</div>
                    <div class="info-card">
                        <div class="faq-item">
                            <div class="step-header">Q: Do I need an account prior to recycling?</div>
                            <div class="info-desc">A: No! Present your bottle directly to the reader framework. You can securely formulate your identity tracker matrix immediately after successful validation.</div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <div class="toast-overlay" id="statusToast">
        <div class="toast-box" id="toastBox">
            <div class="toast-msg" id="toastMessage">Verification Status</div>
            <button class="btn" style="padding: 10px 25px;" onclick="closeToast()">Acknowledge</button>
        </div>
    </div>

    <script>
        const video = document.getElementById('webcam');
        const canvas = document.getElementById('canvas');
        const scanBtn = document.getElementById('scan-btn');
        const stopBtn = document.getElementById('stop-btn');
        const placeholder = document.getElementById('camera-placeholder');
        const globalStatus = document.getElementById('global-status');
        const toastOverlay = document.getElementById('statusToast');
        const toastBox = document.getElementById('toastBox');
        const toastMessage = document.getElementById('toastMessage');
        let streamInstance = null;
        let detectionInterval = null;
        let isPausedForAlert = false;

        function toggleSidebar() { document.getElementById('sidebarMenu').classList.toggle('active'); }

        function switchView(viewId, element) {
            document.querySelectorAll('.view-section').forEach(v => v.style.display = 'none');
            document.querySelectorAll('.nav-link').forEach(link => link.classList.remove('active-link'));
            document.getElementById('view-' + viewId).style.display = 'flex';
            if (element) element.classList.add('active-link');
            
            if (viewId !== 'scan') { stopCamera(); }
            document.getElementById('sidebarMenu').classList.remove('active');
        }

        function startCamera() {
            if (streamInstance) return;
            navigator.mediaDevices.getUserMedia({ video: { width: 560, height: 340 } })
                .then(stream => { 
                    video.srcObject = stream; 
                    streamInstance = stream; 
                    placeholder.style.display = 'none';
                    scanBtn.style.display = 'none';
                    stopBtn.style.display = 'inline-flex';
                    globalStatus.innerText = "Scanning Active...";
                    isPausedForAlert = false;
                    
                    detectionInterval = setInterval(captureAndDetect, 1500);
                })
                .catch(err => { showPopup("Error: Camera unavailable or blocked.", "error"); });
        }

        function stopCamera() {
            if (detectionInterval) { clearInterval(detectionInterval); detectionInterval = null; }
            if (streamInstance) { streamInstance.getTracks().forEach(track => track.stop()); streamInstance = null; video.srcObject = null; }
            
            placeholder.style.display = 'flex';
            scanBtn.style.display = 'inline-flex';
            stopBtn.style.display = 'none';
            globalStatus.innerText = "Waiting for Scanning";
        }

        function captureAndDetect() {
            if (isPausedForAlert) return;

            const context = canvas.getContext('2d');
            context.drawImage(video, 0, 0, canvas.width, canvas.height);
            const imageData = canvas.toDataURL('image/jpeg');

            fetch('http://127.0.0.1:5000/scan', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ image: imageData })
            })
            .then(res => res.json())
            .then(data => {
                if (data.status === 'c') {
                    isPausedForAlert = true;
                    globalStatus.innerText = "Cap Detected";
                    showPopup("⚠️ Cap Detected! Please remove the cap before inserting the bottle.", "warning");
                } 
                else if (data.status === 'nr') {
                    isPausedForAlert = true;
                    globalStatus.innerText = "Invalid Object";
                    showPopup("❌ This item is not recyclable for this bin.", "error");
                } 
                else if (data.status === 'accepted') {
                    globalStatus.innerText = "Verification Clear";
                    stopCamera();
                    fetch('index.php', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                        body: 'set_session=1'
                    }).then(() => { window.location.href = 'auth.php'; });
                }
            })
            .catch(err => {});
        }

        function showPopup(text, type) {
            toastMessage.innerText = text;
            toastBox.classList.remove('success-border', 'warning-border');
            
            if (type === 'success') {
                toastBox.style.borderColor = "#00e699";
            } else if (type === 'warning') {
                toastBox.style.borderColor = "#ffcc00";
            } else {
                toastBox.style.borderColor = "#ff4d4d";
            }
            toastOverlay.style.display = 'flex';
        }

        function closeToast() { 
            toastOverlay.style.display = 'none'; 
            isPausedForAlert = false;
        }
        
        function resetTerminal() { stopCamera(); window.location.href = 'index.php'; }

        scanBtn.addEventListener('click', startCamera);
        stopBtn.addEventListener('click', stopCamera);
    </script>
</body>
</html>