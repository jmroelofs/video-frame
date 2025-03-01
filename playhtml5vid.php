<?php

declare(strict_types = 1);

const _JEXEC = 1 ;

const SCRIPT = '/node_modules/video.js/dist/video.min.js';
const CSS = '/node_modules/video.js/dist/video-js.min.css';

header("Access-Control-Allow-Origin: *");

// get file name
$file = rawurldecode($_GET['file'] ?? '');

// get autoplay, default is 1
$autoplay = ($_GET['autoplay'] ?? null !== '0');

// for security
if (! file_exists('../' . $file)) {
    require __DIR__ . '/../index.php';
    exit();
}

$mime_type = mime_content_type('../' . $file);

if (! str_contains($mime_type, 'video')) {
    require __DIR__ . '/../index.php';
    exit();
}

[
    'extension' => $extension,
    'filename' => $filename,
] = pathinfo($file) + [
    'extension' => '',
    'filename' => '',
];

// some household variables
$protocol = ((!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') || $_SERVER['SERVER_PORT'] == 443) ? 'https://':'http://';
$current_url = explode('?', $protocol . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'])[0];

$full_link = $protocol . $_SERVER['SERVER_NAME'] . implode('/', array_map('rawurlencode', explode('/', dirname($_SERVER['PHP_SELF'], 2) . '/' . $file)));
$full_image_link = rtrim($full_link, ".$extension") . '.jpg';
$image = rtrim('../' . $file, ".$extension") . '.jpg';
if (file_exists($image)){
    [$width, $height, $image_type, $image_attr] = getimagesize($image);
}
$file_time = date(DATE_ATOM, filemtime('../' . $file));

?>
<!DOCTYPE html>
<html lang="nl">
<head prefix="og: http://ogp.me/ns#"><?php /* see: http://ogp.me/ */ ?>
<meta charset="UTF-8">
<meta name="keywords" content="video">
<meta name="description" content="<?php echo $filename; ?> is made and hosted by Roelofs Coaching">
<meta name="site_name" content="Roelofs Coaching">
<meta name="rights" content="www.roelofs-coaching.nl">
<meta name="viewport" content="width=device-width">
<meta name="theme-color" content="#444444">
<meta property="og:title" content="Roelofs Coaching - <?php echo $filename; ?>">
<meta property="og:type" content="video">
<meta property="og:video" content="<?php echo $full_link; ?>">
<meta property="og:video:url" content="<?php echo $current_url; ?>">
<meta property="og:video:type" content="<?php echo $mime_type; ?>">
<?php if (isset($height) && isset($width)): ?>
<meta property="og:video:width" content="<?php echo $width; ?>">
<meta property="og:video:height" content="<?php echo $height; ?>">
<meta property="og:image" content="<?php echo $full_image_link; ?>">
<meta property="og:image:type" content="image/jpeg">
<meta property="og:image:width" content="<?php echo $width; ?>">
<meta property="og:image:height" content="<?php echo $height; ?>">
<?php endif ?>
<meta property="og:description" content="<?php echo $filename; ?> is made and hosted by Roelofs Coaching">
<meta property="og:url" content="<?php echo $current_url; ?>">
<title>Roelofs Coaching - <?php echo $filename; ?></title>
<link href="<?php echo $current_url; ?>" rel="canonical">
<link href="<?php echo dirname($_SERVER['PHP_SELF'], 2); ?>/templates/purity_iii/favicon.ico" rel="shortcut icon" type="image/x-icon">
<link href="<?php echo dirname($_SERVER['PHP_SELF']) . CSS; ?>" rel="stylesheet">
<script src="<?php echo dirname($_SERVER['PHP_SELF']) . SCRIPT; ?>"></script>
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
    "name": "<?php echo $pretty_file_name; ?>",
    "@id": "<?php echo $current_url ?>",
    "description": "<?php echo $pretty_file_name; ?> is made and hosted by Roelofs Coaching",
    "contentURL": "<?php echo $full_link; ?>",
    "embedUrl": "<?php echo $current_url ?>",
<?php if (isset($height) && isset($width)): ?>
    "height": <?php echo $height; ?>,
    "width": <?php echo $width; ?>,
    "thumbnailUrl": "<?php echo $full_image_link; ?>",
    "uploadDate": "<?php echo $file_time; ?>",
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
        poster="<?php echo $full_image_link; ?>"
    >
        <source src="<?php echo $full_link; ?>" type="<?php $mime_type; ?>">
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

<?php  if (isset($height, $width) && str_contains($_SERVER['HTTP_REFERER'], $_SERVER['SERVER_NAME'])): ?>

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
