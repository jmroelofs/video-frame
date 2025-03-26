<?php

declare(strict_types = 1);

const _JEXEC = 1;

const SCRIPT = '/node_modules/video.js/dist/video.min.js';
const CSS = '/node_modules/video.js/dist/video-js.min.css';

header("Access-Control-Allow-Origin: *");

// get file name
$file = '../' . rawurldecode($_GET['file'] ?? '');

// get autoplay, default is 1
$autoPlay = ($_GET['autoplay'] ?? null !== '0');

// for security
if (! is_file($file) || ! ($mimeType = mime_content_type($file)) || ! str_contains($mimeType, 'video')) {
    require __DIR__ . '/../index.php';
    exit();
}

['extension' => $extension, 'filename' => $fileName] = pathinfo($file);

$protocol = empty($_SERVER['HTTPS']) ? 'http://' : 'https://';
$currentUrl = explode('?', $protocol . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'])[0];

$videoLink = $protocol
    . $_SERVER['SERVER_NAME']
    . '/'
    . implode('/', array_map('rawurlencode', array_filter(explode('/', dirname($_SERVER['SCRIPT_NAME'], 2) . ltrim($file, '..')))));

$image = rtrim($file, ".$extension") . '.jpg';
if (is_file($image)){
    [$width, $height] = getimagesize($image);
}
$imageLink = rtrim($videoLink, ".$extension") . '.jpg';

$fileTime = date(DATE_ATOM, filemtime($file));

?>
<!DOCTYPE html>
<html lang="nl">
<head prefix="og: http://ogp.me/ns#"><?php /* see: http://ogp.me/ */ ?>
<meta charset="UTF-8">
<meta name="keywords" content="video">
<meta name="description" content="<?php echo $fileName; ?> is made and hosted by Roelofs Coaching">
<meta name="site_name" content="Roelofs Coaching">
<meta name="rights" content="www.roelofs-coaching.nl">
<meta name="viewport" content="width=device-width">
<meta name="theme-color" content="#444444">
<meta property="og:title" content="Roelofs Coaching - <?php echo $fileName; ?>">
<meta property="og:type" content="video">
<meta property="og:video" content="<?php echo $videoLink; ?>">
<meta property="og:video:url" content="<?php echo $currentUrl; ?>">
<meta property="og:video:type" content="<?php echo $mimeType; ?>">
<?php if (isset($height) && isset($width)): ?>
<meta property="og:video:width" content="<?php echo $width; ?>">
<meta property="og:video:height" content="<?php echo $height; ?>">
<meta property="og:image" content="<?php echo $imageLink; ?>">
<meta property="og:image:type" content="image/jpeg">
<meta property="og:image:width" content="<?php echo $width; ?>">
<meta property="og:image:height" content="<?php echo $height; ?>">
<?php endif ?>
<meta property="og:description" content="<?php echo $fileName; ?> is made and hosted by Roelofs Coaching">
<meta property="og:url" content="<?php echo $currentUrl; ?>">
<title>Roelofs Coaching - <?php echo $fileName; ?></title>
<link href="<?php echo $currentUrl; ?>" rel="canonical">
<link href="<?php echo dirname($_SERVER['SCRIPT_NAME'], 2); ?>/templates/purity_iii/favicon.ico" rel="shortcut icon" type="image/x-icon">
<link href="<?php echo dirname($_SERVER['SCRIPT_NAME']) . CSS; ?>" rel="stylesheet">
<script src="<?php echo dirname($_SERVER['SCRIPT_NAME']) . SCRIPT; ?>"></script>
<style>
    @-ms-viewport     {width: device-width;}
    @-o-viewport      {width: device-width;}
    @viewport         {width: device-width;}

    html, body {
        height: 100%;
        width: 100%;
        margin: 0;
        padding: 0;
    }

    body {
        background-color:black;
        overflow: hidden;
    }

    #container {
        display: flex;
        height: 100%;
        max-width: 100%;
        margin: auto;
        <?php  if (isset($height, $width)): ?>
            aspect-ratio: <?php echo round($width / $height, 6) ?>;
        <?php endif ?>
    }

    #my-player {
        align-self: center;
    }
</style>
<?php include __DIR__.'/../klimwoordenboek/analytics.php'; ?>
<script type="application/ld+json">
{
    "@context": "http://schema.org/",
    "@type": "VideoObject",
    "name": "<?php echo $fileName; ?>",
    "@id": "<?php echo $currentUrl ?>",
    "description": "<?php echo $fileName; ?> is made and hosted by Roelofs Coaching",
    "contentURL": "<?php echo $videoLink; ?>",
    "embedUrl": "<?php echo $currentUrl ?>",
<?php if (isset($height) && isset($width)): ?>
    "height": <?php echo $height; ?>,
    "width": <?php echo $width; ?>,
    "thumbnailUrl": "<?php echo $imageLink; ?>",
    "uploadDate": "<?php echo $fileTime; ?>",
<?php endif ?>
    "author": {
        "@type": "Person",
        "name": "Jan Martin Roelofs"
    }
}
</script>
</head>
<body>
<div id="container">

    <video
        id="my-player"
        class="video-js"
        controls
        preload="auto"
        poster="<?php echo $imageLink; ?>"
    >
        <source src="<?php echo $videoLink; ?>" type="<?php echo $mimeType; ?>">
        <p class="vjs-no-js">
            To view this video please enable JavaScript, and consider upgrading to a
            web browser that
            <a href="https://videojs.com/html5-video-support/" target="_blank">
            supports HTML5 video
            </a>
        </p>
    </video>

</div>
<script>
const player = videojs('my-player', {
    fluid: true,
    responsive: true,
    playbackRates: [0.125, 0.25, 0.5, 1, 2, 4, 8],
    // enableSmoothSeeking: true,
    controls: true,
    controlBar: {
        skipButtons: {
            backward: 5,
            forward: 5
        }
    },
    userActions: {
        hotkeys: function(event) {
            if (event.which === 32) { // space
                if (this.paused()){
                    this.play();
                } else {
                    this.pause();
                };
            }
            else if (event.which === 37) { // arrow left
                this.currentTime(this.currentTime() - 5);
            }
            else if (event.which === 39) { // arrow right
                this.currentTime(this.currentTime() + 5);
            }
            else if (event.which === 38) { // arrow up
                this.playbackRate(Math.min(this.playbackRate() * 2, 8));
            }
            else if (event.which === 40) { // arrow down
                this.playbackRate(Math.max(this.playbackRate() / 2, 0.125));
            }
        }
    }
});

<?php  if (isset($height, $width) && str_contains($_SERVER['HTTP_REFERER'] ?? '', $_SERVER['SERVER_NAME'])): ?>

    const isIframe = (() => {
        try {
            return window.self !== window.top;
        } catch(e) {
            return true;
        }
    })();

    if (isIframe) {
        const jQuery = window.parent.jQuery;
        jQuery('#mediabox-iframe-fix').remove();
        jQuery(`<style id="mediabox-iframe-fix">
            .wf-mediabox-content-item {
                padding-bottom: <?php echo round(100 * $height / $width, 6); ?>% !important;
                height: 0 !important;
            }
        </style>`)
        .appendTo('body');
    }

<?php endif ?>

</script>
</body>
</html>
