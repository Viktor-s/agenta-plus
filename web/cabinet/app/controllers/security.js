;(function (angular) {
    "use strict";

    var securityModule = angular.module('ap.controller.security', ['ui.router', 'ap.auth']);

    securityModule
        .config(['$stateProvider', function ($stateProvider) {
            $stateProvider
                .state('login', {
                    url: '/login',
                    templateUrl: '/cabinet/views/security/login.html',
                    controller: LoginController,
                    pageTitle: 'Login'
                })
                .state('logout', {
                    url: '/logout',
                    controller: LogoutController
                });
        }]);

    /**
     * Logout controller
     *
     * @params {*} $apAuthStorage
     * @params {*} $state
     *
     * @constructor
     */
    function LogoutController($apAuthStorage, $state)
    {
        $apAuthStorage.clear();
        $state.go('login');
    }

    /**
     * Login controller
     *
     * @params {*} $scope
     * @params {*} $internalApi
     * @params {*} $state
     * @params {*} internalApiConfig
     * @params {*} $location
     *
     * @constructor
     */
    function LoginController($scope, $apAuth, $state)
    {
        $scope.username = null;
        $scope.password = null;
        $scope.error = null;

        $scope.authorize = function ()
        {
            // Reset error
            $scope.error = null;

            $apAuth
                .auth($scope.username, $scope.password)
                .then(
                function () {
                    $state.go('dashboard.home');
                },
                function () {
                    $scope.error = 'Invalid username or password'
                }
            );
        }
    }
})(window.angular);
