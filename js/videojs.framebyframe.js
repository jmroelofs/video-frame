// videojs-framebyframe-plugin
//
// copied from https://github.com/douglassllc/videojs-framebyframe
// adjusted it's button placement which was giving an error

"use strict";

const
    defaultFps = 30000 / 1001,

    adjustPosition = (player, step, fps) => {
        if (!player.paused()) {
            player.pause();
        }

        player.currentTime(player.currentTime() + step / (fps ?? defaultFps));
    }

class FrameByFrameButton extends videojs.getComponent('Button') {
    constructor(player, options) {
        super(player, options);
        this.player = player;
        this.options = options;
    }

    handleClick() {
        adjustPosition(this.player, this.options.step, this.options.fps);
    }
}

function frameByFrame(options) {
    this.ready(() => {
        options.steps?.forEach((option) => {
            const buttonElement = this.controlBar.addChild(
                new FrameByFrameButton(
                    this,
                    {
                        el: videojs.dom.createEl(
                            'button',
                            {
                                className: 'vjs-res-button vjs-control',
                                innerHTML: '<div class="vjs-control-content"><span class="vjs-fbf">' + option.text + '</span></div>'
                            },
                            {
                                role: 'button'
                            }
                        ),
                        step: option.step,
                        fps: options.fps,
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
            'wheel',
            (event) => {
                if (event.deltaY === 0) {
                    return;
                }

                event.preventDefault();
                adjustPosition(this, Math.sign(event.deltaY) * (options.wheel.step ?? 1), options.fps);
            },
            { passive: false }
        );
    });
}

videojs.registerComponent('FrameByFrameButton', FrameByFrameButton);
videojs.registerPlugin('frameByFrame', frameByFrame);
