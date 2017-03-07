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

var CRYPTOGRAPHP = CRYPTOGRAPHP || {};

CRYPTOGRAPHP.audio = null;

CRYPTOGRAPHP.isAudioSupported = function () {
    if (typeof window.Audio == "undefined") {
        return false;
    }
    return !!new Audio().canPlayType("audio/mpeg");
};

CRYPTOGRAPHP.doForEach = function (className, func) {
    var elements, i, n;

    if (typeof document.getElementsByClassName != "undefined") {
        elements = document.getElementsByClassName(className);
    } else if (typeof document.querySelectorAll != "undefined") {
        elements = document.querySelectorAll("." + className);
    } else {
        elements = [];
    }
    for (i = 0, n = elements.length; i < n; i++) {
        func(elements[i]);
    }
};

CRYPTOGRAPHP.playAudio = function (link) {
    this.audio = this.audio || new Audio();

    this.audio.onerror = function () {
        link.onclick = "";
    };
    this.audio.src = link.href.replace("&cryptographp_download=yes", "");
    this.audio.play();
};

CRYPTOGRAPHP.onClickAudioLink = function () {
    CRYPTOGRAPHP.playAudio(this);
    return false;
}

CRYPTOGRAPHP.onReload = function () {
    var image = this.previousSibling.previousSibling;

    image.src = this.href + "&" + new Date().getTime();
    if (CRYPTOGRAPHP.audio) {
        CRYPTOGRAPHP.audio.pause();
    }
    return false;
};

CRYPTOGRAPHP.prepareReload = function (audio) {
    var container, reload;

    container = document.createElement("div");
    container.innerHTML = audio.nextSibling.nodeValue;
    reload = container.firstChild;
    reload.onclick = this.onReload;
    audio.parentNode.insertBefore(reload, audio.nextSibling);
};

(function () {
    var isAudioSupported = CRYPTOGRAPHP.isAudioSupported();
    CRYPTOGRAPHP.doForEach("cryptographp_audio", function (element) {
        if (isAudioSupported) {
            element.onclick = CRYPTOGRAPHP.onClickAudioLink;
        }
        CRYPTOGRAPHP.prepareReload(element);
    });
}());
