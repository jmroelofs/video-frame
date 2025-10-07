// videojs-framebyframe-plugin
//
// copied from https://github.com/douglassllc/videojs-framebyframe
// adjusted itś button placement which was giving an error

const Button = videojs.getComponent('Button');

class FrameByFrameButton extends Button {
  constructor(player, options) {
    super(player, options);
    this.player = player;
    this.frameTime = 1/options.fps;
    this.step_size = options.value;
  }

  handleClick() {
    // Start by pausing the player
    this.player.pause();
    // Calculate movement distance
    var dist = this.frameTime * this.step_size;
    this.player.currentTime(this.player.currentTime() + dist);
  }
}

function framebyframe(options) {
    var player = this,
        frameTime = 1 / 30; // assume 30 fps

    player.ready(function() {
        options.steps.forEach(function(opt) {
            var b = player.controlBar.addChild(
                new FrameByFrameButton(player, {
                    el: videojs.dom.createEl(
                        'button',
                        {
                            className: 'vjs-res-button vjs-control',
                            innerHTML: '<div class="vjs-control-content"><span class="vjs-fbf">' + opt.text + '</span></div>'
                        },
                        {
                            role: 'button'
                        }
                    ),
                    value: opt.step,
                    fps: options.fps,
                }),
            {}, opt.index);
            player.controlBar.el().insertBefore(b.el(), player.controlBar.fullscreenToggle.el());
        });
    });
}

videojs.registerComponent('FrameByFrameButton', FrameByFrameButton);

videojs.registerPlugin('framebyframe', framebyframe);
