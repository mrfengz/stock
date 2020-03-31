require.config({
    // baseUrl: "js/",
    paths: {
        jquery: ["jquery"],
        bootstrap: ['https://cdn.bootcss.com/twitter-bootstrap/3.3.7/js/bootstrap.min',
            '../bootstrap/js/bootstrap.min'],
        helper: ['helper']
    },
    shim: {
        'bootstrap': ['jquery'],
    }
});