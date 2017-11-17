define(function (require) {
    var date = require('app/helpers/date');
    var loader = require('app/helpers/loader');
    var $container = $('#ratings-list');
    //работа с рейтингами
    var main = {
        /**
         * Точка входа
         */
        init: function () {
            this.update();
            this.subscribe();
        },
        /**
         * Обновление истории изменений рейтинга
         */
        update: function () {
            loader.show($container);
            $.get('/ratings', {userId: $('#userId').val()}, function (response) {
                var listHtml = ['<div class="col-sm-12 form-group">Пока нет ни одной оценки</div>'];
                if (response.list && response.list.length) {
                    listHtml = [];
                    $.each(response.list, function () {
                        listHtml.push(
                            '<div class="alert alert-' + (this.mark == 1 ? 'success' : 'danger') + '">'
                            + date.convertUnixToRusFormat(this.date)
                            + ' <a href="/users/show/' + this.authorId + '">' + this.author + '</a> '
                            + (this.gender == 0 ? 'поставил ' : 'поставила ')
                            + (this.mark == 1 ? '+' : '-')
                            + '</div>'
                        )
                    });
                }
                $container.html(listHtml.join(''));
            });
            loader.show($('.user-rating'));
            $.get('/ratings/getForUser', {userId: $('#userId').val()}, function (response) {
                $('.user-rating').html(response.rating);
            });
        },
        /**
         * Подписка на события страницы
         */
        subscribe: function () {
            //отправка оценки
            $('.ratingForm').submit(function (e) {
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
