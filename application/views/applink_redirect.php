<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>

<head>
    <title>Applink | Foodi</title>
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta property="al:ios:url" content="<?= $link ?>" />
    <meta property="al:ios:app_store_id" content="1610910233" />
    <meta property="al:ios:app_name" content="Foodi" />
    <meta property="al:android:url" content="<?= $link ?>">
    <meta property="al:android:package" content="com.foodiBd">
    <meta property="al:android:app_name" content="Foodi">
    <meta property="al:web:should_fallback" content="false" />
    <meta property="og:title" content="<?= $name ?> | Foodi" />
    <meta property="og:type" content="Website" />
    <meta property="og:url" content="<?= $link ?>" />
    <meta property="og:description" content="Order your food" />
    <meta property="og:image" content="<?= $image ?>" />
    <meta property="og:image:width" content="500px" />
    <meta property="og:image:height" content="500px" />
</head>

<body>

</body>
<script>
    document.addEventListener("DOMContentLoaded", () => {
        var userAgent = navigator.userAgent || navigator.vendor || window.opera;
        if (/android/i.test(userAgent)) {
            window.location.replace("foodi://foodibd.com/applink/<?= $segment ?>");
            window.setTimeout(() => {
                window.location.replace("<?= ANDROID_APK_LINK ?>")
            }, 250)
        } else if (/iPhone Simulator|iPad Simulator|iPad|iPhone|iPod/.test(userAgent) && !window.MSStream) {
            window.location.replace("foodi://applink/<?= $segment ?>");
            window.setTimeout(() => {
                window.location.replace("<?= IOS_APK_LINK ?>")
            }, 250)
        } else {
            window.location = "<?= base_url() ?>";
        }
    })
</script>