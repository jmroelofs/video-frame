// videojs-framebyframe-plugin
//
// copied from https://github.com/douglassllc/videojs-framebyframe
// adjusted it's button placement which was giving an error

"use strict";

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
    const dist = this.frameTime * this.step_size;
    this.player.currentTime(this.player.currentTime() + dist);
  }
}

function framebyframe(options) {
    const player = this;

    player.ready(function() {
        options.steps.forEach(function(opt) {
            const b = player.controlBar.addChild(
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

        // Add mouse wheel support
        if(options.wheel){
            player.el().addEventListener(
                "wheel",
                (event) => {
                    const delta = event.deltaY;
                    if (delta === 0) return;

                    event.preventDefault();
                    // Start by pausing the player
                    player.pause();
                    player.currentTime(player.currentTime() + Math.sign(delta) * options.wheel.step / options.fps);
                },
                { passive: false }
            );
        }
    });
}

videojs.registerComponent('FrameByFrameButton', FrameByFrameButton);

videojs.registerPlugin('framebyframe', framebyframe);
