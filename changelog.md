Changelog

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
