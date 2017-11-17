define(function () {
    var loaderGif = '/img/ajax-loader.gif';
    return {
        /**
         * Показать прелоадер в контейнере
         * @param $container
         */
        show: function ($container) {
            $container.html(('<img src="' + loaderGif + '">'));
        }
    };

});
