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
        this.stepSize = options.step;
    }

    handleClick() {
        if (!this.player.paused()) {
            this.player.pause();
        }
        this.player.currentTime(
            this.player.currentTime() + this.frameTime * this.stepSize
        );
    }
}

function framebyframe(options) {
    this.ready(() => {
        options.steps.forEach((opt) => {
            const buttonElement = this.controlBar.addChild(
                new FrameByFrameButton(
                    this,
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
                        step: opt.step,
                        fps: options.fps ?? 30000 / 1001,
                    }
                )
            ).el();
            this.controlBar.el().insertBefore(
                buttonElement,
                this.controlBar.fullscreenToggle.el()
            );
        });

        // Add mouse wheel support
        if (!options.wheel) {
            return;
        }
        this.el().addEventListener(
            "wheel",
            (event) => {
                if (event.deltaY === 0) {
                    return;
                }

                event.preventDefault();
                if (!this.paused()) {
                    this.pause();
                }
                this.currentTime(
                    this.currentTime() + Math.sign(event.deltaY) * (options.wheel.step ?? 1) / (options.fps ?? 30000 / 1001)
                );
            },
            { passive: false }
        );
    });
}

videojs.registerComponent('FrameByFrameButton', FrameByFrameButton);
videojs.registerPlugin('framebyframe', framebyframe);
