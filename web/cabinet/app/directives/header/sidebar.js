;(function (angular){
    "use strict";

    angular.module('AgentPlus')
        .directive('headerSidebar', function () {
            return {
                templateUrl: '/cabinet/app/directives/header/sidebar.html',
                restrict: 'E',
                replace: true
            }
        });
})(window.angular);
