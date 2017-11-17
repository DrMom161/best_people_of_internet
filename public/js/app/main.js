requirejs.config({
    waitSeconds: 15,
    baseUrl: '/js',
    urlArgs: 'v=0.0.1'
});

//скрипты для каждой страницы инициализируются с помощью переменной
if (typeof(user_show_page) !== 'undefined') {
    requirejs(['app/pages/user_show/main'], function (user_show) {
        user_show.init();
    });
}

if (typeof(index_page) !== 'undefined') {
    requirejs(['app/pages/index/main'], function (index) {
        index.init();
    });
}

if (typeof(register_page) !== 'undefined') {
    requirejs(['app/pages/register/main'], function (register) {
        register.init();
    });
}