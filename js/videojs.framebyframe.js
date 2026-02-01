// videojs-framebyframe-plugin
//
// copied from https://github.com/douglassllc/videojs-framebyframe
// adjusted it's button placement which was giving an error

"use strict";

class FrameByFrameButton extends videojs.getComponent('Button') {
    constructor(player, options) {
        super(player, options);
        this.player = player;
        this.frameTime = 1 / options.fps;
        this.step_size = options.value;
    }

    handleClick() {
        if (!this.player.paused()) {
            this.player.pause();
        }
        this.player.currentTime(this.player.currentTime() + this.frameTime * this.step_size);
    }
}

function framebyframe(options) {
    const player = this,
        fps = options.fps ?? 30000 / 1001;

    player.ready(function () {
        options.steps.forEach(function (opt) {
            const b = player.controlBar.addChild(
                new FrameByFrameButton(
                    player,
                    {
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
                        fps: fps,
                    }),
                {}, opt.index);
            player.controlBar.el().insertBefore(b.el(), player.controlBar.fullscreenToggle.el());
        });

        // Add mouse wheel support
        if (!options.wheel) {
            return;
        }
        player.el().addEventListener(
            "wheel",
            (event) => {
                const delta = event.deltaY;
                if (delta === 0) {
                    return;
                }

                event.preventDefault();
                if (!player.paused()) {
                    player.pause();
                }
                player.currentTime(player.currentTime() + Math.sign(delta) * options.wheel.step / fps);
            },
            { passive: false }
        );

    });
}

videojs.registerComponent('FrameByFrameButton', FrameByFrameButton);

videojs.registerPlugin('framebyframe', framebyframe);
