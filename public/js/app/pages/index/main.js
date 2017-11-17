define(function (require) {
    var loader = require('app/helpers/loader');
    var $container = $('#users-list')
    var main = {
        /**
         * Точка входа в скрипты страницы
         */
        init: function () {
            this.update();
            this.subscribe();
        },
        /**
         * Обновление списка пользователей
         */
        update: function () {
            loader.show($container);
            $.get('/users', function (response) {
                $container.html(response);
            })
        },
        /**
         * Подписка на события страницы
         */
        subscribe: function () {
            //изменения рейтинга
            $(document).on('submit', '.ratingForm', function (e) {
                e.preventDefault();
                var $form = $(this);
                $form.attr('disabled', 'disabled');
                $.post('/ratings/save', $form.serialize(), function () {
                    $form.trigger('reset');
                    main.update();
                    $('.ratingForm button').removeClass('active');
                    $('button', $form).addClass('active');
                }).fail(function () {
                    location.reload();
                }).always(function () {
                    $form.attr('disabled', false);
                });
            })
        }
    };
    return main;
});
