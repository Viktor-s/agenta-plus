;(function (angular){
    "use strict";

    angular.module('AgentPlus')
        .controller('SidebarController', ['$scope', '$apAuth', function ($scope, $apAuth) {
            $scope.access = $apAuth.isAccesses({
                factoryList: 'FACTORY_LIST',
                clientList: 'CLIENT_LIST',
                diaryList: 'DIARY_LIST',
                orderList: 'ORDER_LIST',
                stageList: 'STAGE_LIST'
            });
        }])
        .directive('sidebar', function () {
            return {
                templateUrl: '/cabinet/app/directives/sidebar/sidebar.html',
                restrict: 'E',
                replace: true
            }
        });
})(window.angular);
