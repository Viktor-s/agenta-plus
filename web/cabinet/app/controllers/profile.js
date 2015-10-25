;(function (angular){
    "use strict";

    var profileModule = angular.module('ap.controller.profile', ['ap.api.internal']);

    profileModule
        .config(function ($stateProvider) {
            $stateProvider
                .state('profile', {
                    url: '/profile',
                    templateUrl: '/cabinet/views/profile/main.html'
                })
                .state('profile.active', {
                    url:  '/',
                    templateUrl: '/cabinet/views/profile/active.html',
                    controller: ProfileViewController
                })
        });

    /**
     * Profile controller
     *
     * @param {*} $scope
     * @param {*} $apInternalApi
     *
     * @constructor
     */
    function ProfileViewController ($scope, $apInternalApi)
    {
        $apInternalApi.profileActive()
            .then(
                function (profile) {
                    $scope.profile = profile;
                },
                function (response) {
                    // @todo: add control error
                }
            )
    }
})(window.angular);