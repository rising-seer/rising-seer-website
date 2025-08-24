<?php
/*
Template Name: Rising Seer Unity App
*/

get_header(); ?>

<style>
.cosmopyxis-unity-container {
    width: 100%;
    max-width: 1920px;
    margin: 0 auto;
    padding: 20px;
    background: linear-gradient(135deg, #1a1a2e 0%, #16213e 50%, #0f3460 100%);
    min-height: 100vh;
}

#unity-container {
    width: 100%;
    height: 80vh;
    min-height: 600px;
    background: #231F20;
    border-radius: 10px;
    overflow: hidden;
    box-shadow: 0 10px 30px rgba(0,0,0,0.5);
}

#unity-canvas {
    width: 100%;
    height: 100%;
    background: #231F20;
}

#unity-loading-bar {
    position: absolute;
    left: 50%;
    top: 50%;
    transform: translate(-50%, -50%);
    display: none;
}

#unity-progress-bar-empty {
    width: 300px;
    height: 20px;
    background: rgba(255,255,255,0.2);
    border-radius: 10px;
    overflow: hidden;
}

#unity-progress-bar-full {
    width: 0%;
    height: 100%;
    background: linear-gradient(90deg, #4facfe 0%, #00f2fe 100%);
    transition: width 0.3s ease;
}

#unity-footer {
    text-align: center;
    margin-top: 20px;
    color: #fff;
}

.unity-mobile #unity-container {
    height: 70vh;
}
</style>

<div class="cosmopyxis-unity-container">
    <div id="unity-container" class="unity-desktop">
        <canvas id="unity-canvas"></canvas>
        <div id="unity-loading-bar">
            <div id="unity-progress-bar-empty">
                <div id="unity-progress-bar-full"></div>
            </div>
        </div>
        <div id="unity-warning"></div>
        <div id="unity-footer">
            <div id="unity-build-title">Cosmopyxis - Cosmic Insights Through Technology</div>
        </div>
    </div>
</div>

<script>
    var container = document.querySelector("#unity-container");
    var canvas = document.querySelector("#unity-canvas");
    var loadingBar = document.querySelector("#unity-loading-bar");
    var progressBarFull = document.querySelector("#unity-progress-bar-full");
    var warningBanner = document.querySelector("#unity-warning");

    function unityShowBanner(msg, type) {
        function updateBannerVisibility() {
            warningBanner.style.display = warningBanner.children.length ? 'block' : 'none';
        }
        var div = document.createElement('div');
        div.innerHTML = msg;
        warningBanner.appendChild(div);
        if (type == 'error') div.style = 'background: red; padding: 10px; color: white; margin: 10px 0;';
        else {
            if (type == 'warning') div.style = 'background: orange; padding: 10px; color: white; margin: 10px 0;';
            setTimeout(function() {
                warningBanner.removeChild(div);
                updateBannerVisibility();
            }, 5000);
        }
        updateBannerVisibility();
    }

    // Build folder lives in the Cosmopyxis (legacy) theme until branding update completes
    var buildUrl = "<?php echo esc_url( get_theme_root_uri() . '/cosmopyxis/unity-app/Build' ); ?>";
    var loaderUrl = buildUrl + "/unity-app.loader.js";
    var config = {
        dataUrl: buildUrl + "/unity-app.data.gz",
        frameworkUrl: buildUrl + "/unity-app.framework.js.gz",
        codeUrl: buildUrl + "/unity-app.wasm.gz",
        streamingAssetsUrl: "StreamingAssets",
        companyName: "Rising Seer",
        productName: "Rising Seer",
        productVersion: "1.0.0",
        showBanner: unityShowBanner,
    };

    // Check for mobile devices
    if (/iPhone|iPad|iPod|Android/i.test(navigator.userAgent)) {
        container.className = "unity-mobile";
        config.devicePixelRatio = 1;
        unityShowBanner('WebGL builds are not supported on mobile devices.', 'warning');
    }

    loadingBar.style.display = "block";

    var script = document.createElement("script");
    script.src = loaderUrl;
    script.onload = () => {
        createUnityInstance(canvas, config, (progress) => {
            progressBarFull.style.width = 100 * progress + "%";
        }).then((unityInstance) => {
            loadingBar.style.display = "none";
        }).catch((message) => {
            alert(message);
        });
    };
    script.onerror = () => {
        unityShowBanner('Failed to load Unity application. Please check your connection and try again.', 'error');
    };
    document.body.appendChild(script);
</script>

<?php get_footer(); ?>
