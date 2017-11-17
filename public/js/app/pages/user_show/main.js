define(function (require) {
    var comments = require('app/pages/user_show/comments')
    var ratings = require('app/pages/user_show/ratings')
    return {
        /**
         * Главная точка входа
         */
        init: function () {
            comments.init();
            ratings.init();
        }
    };
});
