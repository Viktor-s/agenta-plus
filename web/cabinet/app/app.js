;(function (angular){
    "use strict";

    var app = angular.module('AgentPlus', [
        'ap.auth',
        'ap.api.internal',
        'ap.api.external',
        'ap.controllers',
        'ap.filters',
        'ui.router',
        'ui.bootstrap',
        'angular-loading-bar',
        'ngAnimate',
        'xeditable',
        'frapontillo.bootstrap-switch',
        'ui.select',
        'request.errors'
    ]);

    /**
     * Configuration section
     */
    app.config(function ($apInternalApiConfigProvider, $apExternalApiConfigProvider) {
        var html = document.getElementsByTagName('html')[0];
        $apInternalApiConfigProvider.setUrl(html.getAttribute('data-apiau'));
        $apExternalApiConfigProvider.setUrl(html.getAttribute('data-apeau'));
    });

    app.config(function (cfpLoadingBarProvider) {
        cfpLoadingBarProvider.includeSpinner = false;
    });

    app.config(function ($urlRouterProvider) {
        $urlRouterProvider.otherwise('/dashboard/home')
    });

    app.config(function (NotificationProvider) {
        NotificationProvider.setOptions({
            delay: 10000,
            positionX: 'right',
            positionY: 'bottom'
        });
    });

    /**
     * Run section
     */
    app.run(function ($apAuthStorage, $location) {
        // Check authentication, and redirect to login page, if not authentication
        if (!$apAuthStorage.has()) {
            $location.url('/login');
        }
    });

    app.run(function (editableOptions) {
        editableOptions.theme = 'bs3';
    });
})(window.angular);
