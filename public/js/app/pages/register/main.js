define(function () {
    var main = {
        /**
         * Точка входа
         */
        init: function () {
            this.subscribe();
        },
        /**
         * Подписка на события страницы
         */
        subscribe: function () {
            $('#show-password').click(function () {
                $('[name=password]').attr('type', $(this).prop('checked') ? 'text' : 'password');
            })
        }
    };
    return main;
});
