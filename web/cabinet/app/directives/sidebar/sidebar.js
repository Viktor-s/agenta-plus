;(function (angular){
    "use strict";

    angular.module('AgentPlus')
        .controller('SidebarController', ['$scope', '$apAuth', function ($scope, $apAuth) {
            var accesses = {
                factoryList: 'FACTORY_LIST',
                clientList: 'CLIENT_LIST'
            };

            $scope.access = $apAuth.isAccesses(accesses);

            //for (var i in accesses) {
            //    if (accesses.hasOwnProperty(i)) {
            //        $apAuth.isGranted(accesses[i])
            //            .then(function (status) {
            //                $scope.access[i] = status;
            //            });
            //    }
            //}
        }])
        .directive('sidebar', function () {
            return {
                templateUrl: '/cabinet/app/directives/sidebar/sidebar.html',
                restrict: 'E',
                replace: true
            }
        });
})(window.angular);
