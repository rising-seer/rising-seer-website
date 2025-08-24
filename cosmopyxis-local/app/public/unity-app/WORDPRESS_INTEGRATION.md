# Cosmopyxis Unity WebGL WordPress Integration

## Integration Steps

1. **Upload Unity Files**
   - Copy all files from this directory to `/wp-content/themes/cosmopyxis/unity-app/`

2. **Create WordPress Page Template**
   - Create `page-unity-app.php` in your theme directory
   - Include the Unity loader script in the template

3. **Create WordPress Page**
   - Create a new page in WordPress admin
   - Set the page template to "Unity App"
   - Publish the page

## Sample WordPress Template Code

```php
<?php
/*
Template Name: Unity App
*/

get_header(); ?>

<div class="cosmopyxis-unity-container">
    <div id="unity-container" class="unity-desktop">
        <canvas id="unity-canvas" width="1920" height="1080"></canvas>
        <div id="unity-loading-bar">
            <div id="unity-logo"></div>
            <div id="unity-progress-bar-empty">
                <div id="unity-progress-bar-full"></div>
            </div>
        </div>
        <div id="unity-warning"> </div>
        <div id="unity-footer">
            <div id="unity-webgl-logo"></div>
            <div id="unity-fullscreen-button"></div>
            <div id="unity-build-title">Cosmopyxis</div>
        </div>
    </div>
    
    <script>
        var container = document.querySelector("#unity-container");
        var canvas = document.querySelector("#unity-canvas");
        var loadingBar = document.querySelector("#unity-loading-bar");
        var progressBarFull = document.querySelector("#unity-progress-bar-full");
        var fullscreenButton = document.querySelector("#unity-fullscreen-button");
        var warningBanner = document.querySelector("#unity-warning");

        function unityShowBanner(msg, type) {
            function updateBannerVisibility() {
                warningBanner.style.display = warningBanner.children.length ? 'block' : 'none';
            }
            var div = document.createElement('div');
            div.innerHTML = msg;
            warningBanner.appendChild(div);
            if (type == 'error') div.style = 'background: red; padding: 10px;';
            else {
                if (type == 'warning') div.style = 'background: yellow; padding: 10px;';
                setTimeout(function() {
                    warningBanner.removeChild(div);
                    updateBannerVisibility();
                }, 5000);
            }
            updateBannerVisibility();
        }

        var buildUrl = "<?php echo get_template_directory_uri(); ?>/unity-app/Build";
        var loaderUrl = buildUrl + "/Cosmopyxis.loader.js";
        var config = {
            dataUrl: buildUrl + "/Cosmopyxis.data",
            frameworkUrl: buildUrl + "/Cosmopyxis.framework.js",
            codeUrl: buildUrl + "/Cosmopyxis.wasm",
            streamingAssetsUrl: "StreamingAssets",
            companyName: "Cosmopyxis",
            productName: "Cosmopyxis",
            productVersion: "1.0.0",
            showBanner: unityShowBanner,
        };

        if (/iPhone|iPad|iPod|Android/i.test(navigator.userAgent)) {
            container.className = "unity-mobile";
            config.devicePixelRatio = 1;
            unityShowBanner('WebGL builds are not supported on mobile devices.');
        } else {
            canvas.style.width = "1920px";
            canvas.style.height = "1080px";
        }

        loadingBar.style.display = "block";

        var script = document.createElement("script");
        script.src = loaderUrl;
        script.onload = () => {
            createUnityInstance(canvas, config, (progress) => {
                progressBarFull.style.width = 100 * progress + "%";
            }).then((unityInstance) => {
                loadingBar.style.display = "none";
                fullscreenButton.onclick = () => {
                    unityInstance.SetFullscreen(1);
                };
            }).catch((message) => {
                alert(message);
            });
        };
        document.body.appendChild(script);
    </script>
</div>

<?php get_footer(); ?>
```

## CSS Styling

Add this CSS to your theme's style.css:

```css
.cosmopyxis-unity-container {
    width: 100%;
    max-width: 1920px;
    margin: 0 auto;
    padding: 20px;
}

#unity-container {
    width: 100%;
    height: 600px;
    background: #231F20;
}

#unity-canvas {
    width: 100%;
    height: 100%;
    background: #231F20;
}
```
