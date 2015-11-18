;(function (angular) {
    "use strict";

    var dashboardModule = angular.module('ap.controller.dashboard', [
        'ngRoute',
        'ap.api.internal',
        'ap.theme'
    ]);

    dashboardModule
        .config(['$stateProvider', '$apThemeProvider', function ($stateProvider, $apThemeProvider) {
            $stateProvider
                .state('dashboard', {
                    url: '/dashboard',
                    templateUrl: $apThemeProvider.layoutUrl,
                    pageTitle: 'Dashboard'
                })
                .state('dashboard.home', {
                    url: '/home',
                    templateUrl: '/cabinet/views/dashboard/home.html',
                    controller: DashboardController,
                    pageTitle: 'Home'
                })
        }]);

    function DashboardController ($apInternalApi)
    {
        $apInternalApi.ping();
    }
})(window.angular);
