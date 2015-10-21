;(function (angular){
    "use strict";

    var app = angular.module('AgentPlus', [
        'ap.auth',
        'ap.api.internal',
        'ap.controller',
        'ui.router',
        'ui.bootstrap',
        'angular-loading-bar',
        'ngAnimate',
        'xeditable',
        'frapontillo.bootstrap-switch',
        'ui.select'
    ]);

    /**
     * Configuration section
     */
    app.config(function ($apInternalApiConfigProvider) {
        var url = document.getElementsByTagName('html')[0].getAttribute('data-apiau');
        $apInternalApiConfigProvider.setUrl(url);
    });

    app.config(function (cfpLoadingBarProvider) {
        cfpLoadingBarProvider.includeSpinner = false;
    });

    app.config(function ($urlRouterProvider) {
        $urlRouterProvider.otherwise('/dashboard/home')
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
