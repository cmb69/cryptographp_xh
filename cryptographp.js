/*!
 * Copyright 2006-2007 Sylvain Brison
 * Copyright 2011-2017 Christoph M. Becker
 *
 * This file is part of Cryptographp_XH.
 *
 * Cryptographp_XH is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * Cryptographp_XH is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with Cryptographp_XH.  If not, see <http://www.gnu.org/licenses/>.
 */

(function () {
    var currentAudio;

    function find(selector, target) {
        target = target || document;
        if (typeof target.querySelectorAll !== "undefined") {
            return target.querySelectorAll(selector);
        } else {
            return [];
        }
    }

    function each(items, func) {
        for (var i = 0, length = items.length; i < length; i++) {
            func(items[i]);
        }
    }

    function on(target, event, listener) {
        if (typeof target.addEventListener !== "undefined") {
            target.addEventListener(event, listener, false);
        } else if (typeof target.attachEvent !== "undefined") {
            target.attachEvent("on" + event, listener);
        }
    }

    function firstCommentChild(element) {
        var comment = element.firstChild;
        while (comment && comment.nodeType !== 8) {
            comment = comment.nextSibling;
        }
        return comment;
    }

    function isAudioSupported() {
        if (typeof window.Audio == "undefined") {
            return false;
        }
        return !!new Audio().canPlayType("audio/mpeg");
    }
    
    on(window, "load", function () {
        each(find(".cryptographp"), function (captcha) {
            each(find(".cryptographp_audio", captcha), function (element) {
                if (isAudioSupported()) {
                    element.onclick = (function () {
                        var link = this;
                        currentAudio = currentAudio || new Audio();
                        currentAudio.onerror = (function () {
                            link.onclick = null;
                        });
                        currentAudio.src = link.href.replace("&cryptographp_download=yes", "");
                        currentAudio.play();
                        return false;
                    });
                }
            });
            each(find(".cryptographp_reload_container", captcha), function (container) {
                container.innerHTML = firstCommentChild(container).nodeValue;
                each(find(".cryptographp_reload", container), function (anchor) {
                    anchor.onclick = (function () {
                        each(find(".cryptographp_image", captcha), function (image) {
                            image.src = anchor.getAttribute("data-image") + "&" + new Date().getTime();
                        });
                        each(find(".cryptographp_audio", captcha), function (link) {
                            link.href = anchor.getAttribute("data-audio") + "&" + new Date().getTime();
                        });
                        if (currentAudio) {
                            currentAudio.pause();
                        }
                        return false;
                    });
                });
            });
        });
    });
}());
