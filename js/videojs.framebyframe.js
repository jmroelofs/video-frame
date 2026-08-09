// videojs-framebyframe-plugin
//
// copied from https://github.com/douglassllc/videojs-framebyframe
// adjusted it's button placement which was giving an error

"use strict";

const
    defaultFps = 30000 / 1001,
    defaultStep = 1,
    defaultButtons = [
        { step: -1, text: '<&hairsp;1f', title: 'Previous frame (wheel down)' },
        { step: 1, text: '1f&hairsp;>', title: 'Next frame (wheel up)' },
    ],

    adjustPosition = (player, step, fps) => {
        player.paused()
            || player.pause();
        player.currentTime(player.currentTime() + step / fps);
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

function frameByFrame({ fps = defaultFps, buttons = defaultButtons, wheel }) {
    this.ready(() => {
        buttons.forEach((option) => {
            this.controlBar.el().insertBefore(
                new FrameByFrameButton(
                    this,
                    {
                        el: videojs.dom.createEl(
                            'button',
                            {
                                className: 'vjs-fbf-button vjs-control',
                                innerHTML: `<span class="vjs-fbf-text">${option.text}</span>`
                            },
                            {
                                title: option.title,
                                role: 'button',
                            }
                        ),
                        step: option.step ?? defaultStep,
                        fps: fps,
                    }
                ).el(),
                this.controlBar.fullscreenToggle.el()
            );
        });

        // Add mouse wheel support
        wheel
            && this.el().addEventListener(
                'wheel',
                (event) => {
                    if (event.deltaY === 0) {
                        return;
                    }

                    event.preventDefault();
                    adjustPosition(this, Math.sign(event.deltaY) * (wheel.step ?? defaultStep), fps);
                },
                { passive: false }
            );
    });
}

videojs.registerComponent('FrameByFrameButton', FrameByFrameButton);
videojs.registerPlugin('frameByFrame', frameByFrame);
