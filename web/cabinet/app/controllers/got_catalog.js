;(function (angular) {
    "use strict";

    var gotCatalogModule = angular.module('ap.controller.got_catalog', [
        'ap.auth',
        'ap.api.internal',
        'ui.router',
        'processing',
        'angularFileUpload'
    ]);

    gotCatalogModule.config(function ($stateProvider) {
        $stateProvider
            .state('got_catalog', {
                url: '/got-catalog',
                templateUrl: '/cabinet/views/catalog/got/main.html',
                pageTitle: 'Got catalogs'
            })
            .state('got_catalog.search', {
                url: '/search',
                templateUrl: '/cabinet/views/catalog/got/search.html',
                controller: GotCatalogSearchController,
                pageTitle: 'Search'
            });
    });

    function GotCatalogSearchController($scope, $apAuth, $apInternalApi)
    {
        var
            loadGotCatalogs = function ()
            {
                $apInternalApi.gotCatalogs()
                    .then(function (pagination) {
                        $scope.pagination = pagination;
                    });
            };

        loadGotCatalogs();
    }
})(window.angular);