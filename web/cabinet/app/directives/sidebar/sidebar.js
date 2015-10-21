;(function (angular){
    "use strict";

    angular.module('AgentPlus')
        .directive('sidebar', function () {
            return {
                templateUrl: '/cabinet/app/directives/sidebar/sidebar.html',
                restrict: 'E',
                replace: true
            }
        });
})(window.angular);
