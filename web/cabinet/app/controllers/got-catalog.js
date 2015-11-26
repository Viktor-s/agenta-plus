;(function (angular) {
    "use strict";

    var gotCatalogModule = angular.module('ap.controller.got_catalog', [
        'ap.auth',
        'ap.api.internal',
        'ap.theme',
        'ui.router',
        'processing',
        'angularFileUpload'
    ]);

    gotCatalogModule.config(function ($stateProvider, $apThemeProvider) {
        $stateProvider
            .state('got_catalog', {
                url: '/got-catalog',
                templateUrl: $apThemeProvider.layoutUrl,
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