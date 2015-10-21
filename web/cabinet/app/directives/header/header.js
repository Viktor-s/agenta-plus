;(function (angular){
    "use strict";

    angular.module('AgentPlus')
        .directive('header', function () {
            return {
                templateUrl: '/cabinet/app/directives/header/header.html',
                restrict: 'E',
                replace: true
            }
        });
})(window.angular);