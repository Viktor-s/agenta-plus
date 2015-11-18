;(function (angular){
    "use strict";

    angular.module('AgentPlus')
        .controller('SidebarController', ['$scope', '$apAuth', function ($scope, $apAuth) {
            $scope.access = $apAuth.isAccesses({
                factoryList: 'FACTORY_LIST',
                clientList: 'CLIENT_LIST',
                diaryList: 'DIARY_LIST',
                orderList: 'ORDER_LIST',
                stageList: 'STAGE_LIST',
                catalogList: 'CATALOG_LIST',
                gotCatalogList: 'GOT_CATALOG_LIST'
            });
        }])
        .directive('sidebar', function ($apTheme) {
            return {
                templateUrl: $apTheme.resolveDirectiveTemplate('sidebar', 'sidebar'),
                restrict: 'E',
                replace: true
            }
        });
})(window.angular);
