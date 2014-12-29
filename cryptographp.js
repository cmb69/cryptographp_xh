(function($) {

    Cryptographp = {

        /**
         * Plays the audio challenge.
         *
         * @param string id
         * @return void
         */
        play: function(id) {
            $('#cryptographp_player' + id).jPlayer('setMedia', {
                mp3: Cryptographp.URL + '?cryptographp_mode=audio&cryptographp_id=' + id +
                    '&cryptographp_lang=' + Cryptographp.LANG
            }).jPlayer('pauseOthers').jPlayer('play', 0);
            return;
        },


        /**
         * Gets a new challenge.
         *
         * @param string id
         * @return void
         */
        reload: function(id) {
            $('#cryptographp' + id).attr('src',
                    Cryptographp.DIR + 'cryptographp.php?id=' + id +
                    '&lang=' + Cryptographp.LANG + '&' + new Date().getTime());
            return;
        }

    }

    /**
     * Initialize the cryptographps.
     */
    $(function() {
        $('span.cryptographp_player').jPlayer({
            supplied: 'mp3',
            swfPath: Cryptographp.DIR + 'Jplayer.swf',
            solution: 'html, flash',
            error: function(e) {$(e.target).next().attr('onclick', '')}
        });
        $('a.cryptographp_reload').show()
    });


})(jQuery)
