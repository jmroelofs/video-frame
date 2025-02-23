<?php
/*
    playhtml5vid

    Version 2.05

    Uses video.js to play a video in it's own window.
    Path of filename should be relative to path of this file.
    Video-files must be mp4 or mov.
    A jpg-file with the same name as the videofile is used as placeholder,
    also it's dimensions are used
    If this is an iframe in the same domain, some changes are made to the parent frame
    to get the aspect ratio right and enable fullscreen HTML5 video

    Changelist

    2.05 [23-02-2025] center video vertically
    2.04 [19-02-2025] refactor paths and put on github
    2.03 [16-02-2025] fix aspect ratio for all video iframes in wf-mediabox
    2.02 [01-02-2025] fix a typo 1.25 -> 0.125
    2.01 [22-10-2024] use video.js hotkeys
    2.00 [19-10-2024] use video.js
    1.45 [06-09-2023] add theme color
    1.44 [29-05-2021] add 1by1 and 3by4 handling of iframes in jcemediabox 2.1
    1.43 [18-10-2020] disable aspect ratio fix (too unpredictable), change player library address
    1.42 [18-10-2020] fix aspect ratio on newer jcemediabox (2.x)
    1.41 [21-09-2019] The API method jwplayer().addButton() is disabled in the free edition of JW Player.
    1.40 [21-03-2019] added autopause (available since jwplayer 8.8.0)
    1.39 [11-01-2019] better iframe detection
    1.38 [07-12-2018] work round filter_input() bug
    1.37 [19-10-2017] jwplayer 8, use cloudbased script
    1.36 [10-10-2017] let Joomla handle a 404
    1.35 [09-09-2017] refactoring
    1.34 [24-06-2017] cleanup
    1.33 [23-06-2017] use builtin playbackRateControls, dump our own speed button, requires 7.12.0
    1.32 [13-06-2017] add video title and Google Analytics to player setup
    1.31 [30-01-2017] fix facebook og stuff
    1.30 [04-11-2016] use some Google Rich Snippets
    1.29 [05-10-2016] adjustments for 7.7.0
    1.28 [23-09-2016] code cleanup, urldecode is no longer needed on image link
    1.27 [02-06-2016] use HTML5 download attribute, prettify file name on download
                      stop using our downloader (downloadvid.php)
                      removed an unused mobile-check
    1.26 [23-04-2016] hiding of the logo is no longer needed
    1.25 [10-12-2015] moved buttons to own directory
                      reinit slowmotion buttons after rewind on complete
                      jwplayer now loads video with full link, takes care of firefox stuck player bug
                      upgraded to jwplayer 7.2.3
    1.24 [23-11-2015] minor api adjustments for jwplayer 7
                      7.1.4 works, 7.2.2 gets stuck on startup in firefox
    1.23 [18-10-2015] urldecode imagelink, necessary since jwplayer 7.1.0
    1.22 [29-06-2015] rewind at video end (with a delay of 1 second, needed by the player)
    1.21 [23-06-2015] added download possibility
    1.20 [16-06-2015] slowmo/normal possibilities (only on desktop)
    1.19 [25-03-2015] structurize/prettify php, introduced autoplay variable
    1.18 [08-10-2014] tweak internal encoding to accomodate ï¿½ etc. on different servers
    1.17 [06-10-2014] use 404 header and message on error
    1.16 [04-10-2014] use javascript to change the parent frame
    1.15 [29-09-2014] upgrade to jwplayer 6.10, make flash secondary
    1.14 code refactoring
    1.13 code refactoring
    1.11 add some Open Graph Metadata and improved the security checks
    1.10 also enable for mov and fixed 2 small cosmetic bugs
    1.9  changes to accomodate special url's (ï¿½ etc.)
    1.8  use plugins (slow-mo and shortcuts)
    1.7  always stretch, width and height variables are not used any more
    1.6  Stretch video if it doesn't have a parent frame
    1.5  Center video vertically as well (only if it doesn't have a parent frame)
    1.4  Center video horizontally in window (vertically didn't work well on iPad)

    Jan Martin Roelofs: www.roelofs-coaching.nl
*/

define( '_JEXEC', 1 );

define('SCRIPT', 'video-frame/node_modules/video.js/dist/video.min.js');
define('CSS', 'video-frame/node_modules/video.js/dist/video-js.min.css');

$extensions_and_mime_types = array(
    'mp4'  => 'video/mp4',
    'm4v'  => 'video/mp4',
    'f4v'  => 'video/mp4',
    'mov'  => 'video/quicktime',
    'webm' => 'video/webm',
    'ogv'  => 'video/ogg'
);

header("Access-Control-Allow-Origin: *");

// get file name
$file = rawurldecode($_GET['file']);

// get autoplay, default is 1
$autoplay = ( $_GET['autoplay'] !== '0' );

$ext = pathinfo($file, PATHINFO_EXTENSION);

// for security
if (!isset($file) || !$extensions_and_mime_types[$ext] || !file_exists('../' . $file)) {
    // http_response_code(404);
    // echo '404 The video you are looking for was not found';
    require __DIR__.'/../index.php';
    exit();
}

// helper function
function remove_extension($filename) {
    return substr($filename, 0, strrpos($filename, '.'));
}

// some household variables
$protocol = ((!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') || $_SERVER['SERVER_PORT'] == 443) ? 'https://':'http://';
$current_url = $protocol . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];

// to remove the autoplay thingy
if (strrpos($current_url,'?autoplay') !== false) {
    $current_url = substr($current_url, 0, strrpos($current_url, '?autoplay'));
}
$our_directory = rtrim(dirname($_SERVER['PHP_SELF']), 'video-frame');
$our_server = $protocol . $_SERVER['SERVER_NAME'];
$short_link = implode('/', array_map('rawurlencode', explode('/', $our_directory . $file)));
$full_link = $our_server.$short_link;
$pretty_file_name = basename($file, '.' . $ext);
$short_image_link = remove_extension($short_link) . '.jpg';
$full_image_link = $our_server . $short_image_link;
$image = remove_extension('../' . $file) . '.jpg';
if (file_exists($image)){
    list($width, $height, $image_type, $image_attr) = getimagesize($image);
}
$file_time = date(DATE_ATOM, filemtime('../' . $file));

?>
<!DOCTYPE html>
<html lang="nl">
<head prefix="og: http://ogp.me/ns#"><?php /* see: http://ogp.me/ */ ?>
<meta charset="UTF-8">
<meta name="keywords" content="video">
<meta name="description" content="<?php echo $pretty_file_name; ?> is made and hosted by Roelofs Coaching">
<meta name="site_name" content="Roelofs Coaching">
<meta name="rights" content="www.roelofs-coaching.nl">
<meta name="viewport" content="width=device-width">
<meta name="theme-color" content="#444444">
<meta property="og:title" content="Roelofs Coaching - <?php echo $pretty_file_name; ?>">
<meta property="og:type" content="video">
<meta property="og:video" content="<?php echo $full_link; ?>">
<meta property="og:video:url" content="<?php echo $current_url; ?>">
<meta property="og:video:type" content="<?php echo $extensions_and_mime_types[$ext]; ?>">
<?php if (isset($height) && isset($width)): ?>
<meta property="og:video:width" content="<?php echo $width; ?>">
<meta property="og:video:height" content="<?php echo $height; ?>">
<meta property="og:image" content="<?php echo $full_image_link; ?>">
<meta property="og:image:type" content="image/jpeg">
<meta property="og:image:width" content="<?php echo $width; ?>">
<meta property="og:image:height" content="<?php echo $height; ?>">
<?php endif ?>
<meta property="og:description" content="<?php echo $pretty_file_name; ?> is made and hosted by Roelofs Coaching">
<meta property="og:url" content="<?php echo $current_url; ?>">
<title>Roelofs Coaching - <?php echo $pretty_file_name; ?></title>
<link href="<?php echo $current_url; ?>" rel="canonical">
<link href="<?php echo $our_directory; ?>templates/purity_iii/favicon.ico" rel="shortcut icon" type="image/x-icon">
<link href="<?php echo $our_directory . CSS; ?>" rel="stylesheet">
<script src="<?php echo $our_directory . SCRIPT; ?>"></script>
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
        poster="<?php echo $short_image_link; ?>"
    >
        <source src="<?php echo $full_link; ?>" type="<?php $extensions_and_mime_types[$ext]; ?>">
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

const isIframe = (() => {
    try {
        return window.self !== window.top;
    } catch(e) {
        return true;
    }
})();

<?php  if (isset($height, $width)): ?>
    if (isIframe && (document.referrer.indexOf(document.domain) !== -1)) {
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
