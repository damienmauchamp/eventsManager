$(document).ready(function () {


    /**
     * Champ de recherche
     * @type {*|jQuery|HTMLElement}
     */
    var searchInput = $("#search");
    var searchAddon = $("#search-addon-wrap");

    /**
     * Event listener sur la touche entrée
     * @param fnc
     * @returns {*}
     */
    $.fn.enterKey = function (fnc) {
        return this.each(function () {
            $(this).keypress(function (e) {
                var keycode = (e.keyCode ? e.keyCode : e.which);
                if (keycode == '13') {
                    fnc.call(this, e);
                }
            })
        })
    };

    /**
     * Lance la recherche si le champ n'est pas vide
     */
    searchInput.enterKey(function () {
        var query = $(this).val().trim();
        if (query) {
            window.location.href = "/search/" + query + "?page=1&max-results=10";
        }
    });

    searchAddon.on('click', function () {
        var query = searchInput.val().trim();
        if (query) {
            window.location.href = "/search/" + query + "?page=1&max-results=10";
        }
    })

});