;(function (angular){
    "use strict";

    var clientModule = angular.module('ap.controller.client', [
        'ap.auth',
        'ap.api.internal',
        'ap.api.external',
        'ui.router',
        'processing'
    ]);

    clientModule.config(function ($stateProvider) {
        $stateProvider
            .state('client', {
                url: '/client',
                templateUrl: '/cabinet/views/client/main.html',
                pageTitle: 'Clients'
            })
            .state('client.search', {
                url: '/search',
                templateUrl: '/cabinet/views/client/search.html',
                controller: ClientSearchController,
                pageTitle: 'Search'
            })
            .state('client.create', {
                url: '/create',
                templateUrl: '/cabinet/views/client/create.html',
                controller: ClientCreateController,
                pageTitle: 'Create :: Clients'
            })
            .state('client.edit', {
                url: '/:client/edit',
                templateUrl: '/cabinet/views/client/edit.html',
                controller: ClientEditController
            });
    });

    function ClientSearchController($scope, $apInternalApi, $apAuth, $processing, $location, $anchorScroll, $state)
    {
        var
            query = {
                page: $location.search().page ? $location.search().page : 1
            },
            loadClientsByQuery = function ()
            {
                $anchorScroll();

                $apInternalApi.clients(query)
                    .then(
                        function (result) {
                            $scope.pagination = result;
                        },
                        function (response) {
                            // @todo: add control error
                        }
                    )
            },

            findClient = function (id)
            {
                if (!$scope.hasOwnProperty('pagination')) {
                    return null;
                }

                var i, c;

                for (i in $scope.pagination.storage) {
                    if ($scope.pagination.storage.hasOwnProperty(i)) {
                        c = $scope.pagination.storage[i];

                        if (c.id == id) {
                            return c;
                        }
                    }
                }

                return null;
            };

        loadClientsByQuery();

        $scope.accesses = $apAuth.isAccesses({
            clientCreate: 'CLIENT_CREATE',
            clientEdit: 'CLIENT_EDIT'
        });

        /**
         * Update name for client
         *
         * @param {String} id
         * @param {String} name
         */
        $scope.updateName = function (id, name)
        {
            var client = findClient(id);

            if (client && $processing.isNot(client)) {
                $processing.start(client);
                client.name = name;
                $apInternalApi.clientUpdate(client)
                    .then(
                        function () { $processing.end(client); },
                        function () {
                            $processing.end(client);
                            // @todo: add control error
                        }
                    );
            }
        };

        $scope.changePage = function ()
        {
            query.page = $scope.pagination.page;
            loadClientsByQuery();
            $location.search('page', query.page);
        };

        $scope.edit = function (id)
        {
            $state.go('client.edit', { client: id});
        }
    }

    function ClientCreateController($scope, $apInternalApi, $apExternalApi, $state, $processing, Notification)
    {
        $scope.client = {
            phones: [],
            emails: [],
            name: null,
            countryCode: null,
            city: null,
            address: null,
            notes: null
        };

        $apExternalApi.countries()
            .then(function (countries) {
                $scope.countries = countries;
            });

        $scope.create = function ()
        {
            if ($processing.is($scope.client)) {
                return;
            }

            $processing.start($scope.client);

            $scope.client.errors = {};

            $scope.client.phones = $scope.client.phones.filter(function (v) {
                return v ? true : false
            });

            $scope.client.emails = $scope.client.emails.filter(function (v) {
                return v ? true : false;
            });

            $apInternalApi.clientCreate($scope.client)
                .then(
                    function () {
                        $processing.end($scope.client);
                        $state.go('client.search')
                            .then(function () {
                                Notification.success({
                                    message: 'Successfully create client.'
                                });
                            });
                    },

                    function (response) {
                        $processing.end($scope.client);
                        if (response.isRequestNotValid()) {
                            $scope.client.errors = response.errorData;
                        }
                    }
                );
        };

        $scope.addPhone = function ()
        {
            $scope.client.phones.push(null);
        };

        $scope.removePhone = function (index)
        {
            $scope.client.phones.splice(index, 1);
        };

        $scope.addEmail = function ()
        {
            $scope.client.emails.push(null);
        };

        $scope.removeEmail = function (index)
        {
            $scope.client.emails.splice(index, 1);
        };
    }

    function ClientEditController($scope, $apInternalApi, $apExternalApi, $state, $stateParams, $processing, Notification)
    {
        var clientId = $stateParams.client,
            loadCountries = function () {
                $apExternalApi.countries()
                    .then(function (countries) {
                        $scope.countries = countries;
                        var i, country;

                        for (i in countries) {
                            if (countries.hasOwnProperty(i)) {
                                country = countries[i];

                                if ($scope.client.countryCode == country.code) {
                                    $scope.client.country = country;
                                }
                            }
                        }
                    });
            };

        $scope.update = function ()
        {
            if ($processing.is($scope.client)) {
                return;
            }

            $processing.start($scope.client);

            $scope.client.errors = {};

            $scope.client.phones = $scope.client.phones.filter(function (v) {
                return v ? true : false
            });

            $scope.client.emails = $scope.client.emails.filter(function (v) {
                return v ? true : false;
            });

            $apInternalApi.clientUpdate($scope.client)
                .then(
                    function () {
                        $processing.end($scope.client);
                        $state.go('client.search')
                            .then(function () {
                                Notification.success({
                                    message: 'Successfully edit client.'
                                });
                            });
                    },

                    function (response) {
                        $processing.end($scope.client);
                        if (response.isRequestNotValid()) {
                            $scope.client.errors = response.errorData;
                        }
                    }
                );
        };

        $scope.addPhone = function ()
        {
            $scope.client.phones.push(null);
        };

        $scope.removePhone = function (index)
        {
            $scope.client.phones.splice(index, 1);
        };

        $scope.addEmail = function ()
        {
            $scope.client.emails.push(null);
        };

        $scope.removeEmail = function (index)
        {
            $scope.client.emails.splice(index, 1);
        };

        $apInternalApi.client(clientId)
            .then(function (client) {
                $scope.client = client;
                loadCountries();
            });
    }

})(window.angular);