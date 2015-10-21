;(function (angular) {
    "use strict";

    var dashboardModule = angular.module('ap.controller.dashboard', ['ngRoute', 'ap.api.internal']);

    dashboardModule
        .config(['$stateProvider', function ($stateProvider){
            $stateProvider
                .state('dashboard', {
                    url: '/dashboard',
                    templateUrl: '/cabinet/views/dashboard/main.html'
                })
                .state('dashboard.home', {
                    url: '/home',
                    templateUrl: '/cabinet/views/dashboard/home.html',
                    controller: DashboardController,
                    pageTitle: 'Dashboard'
                })
        }]);

    function DashboardController ($apInternalApi)
    {
        $apInternalApi.ping();
    }
})(window.angular);