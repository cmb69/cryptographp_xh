/*!
 * The JavaScript of Cryptographp_XH.
 *
 * @author    Christoph M. Becker <cmbecker69@gmx.de>
 * @copyright 2011-2014 Christoph M. Becker <http://3-magi.net>
 * @link      http://3-magi.net/?CMSimple_XH/Cryptographp_XH
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
    CRYPTOGRAPHP.audio.pause();
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
