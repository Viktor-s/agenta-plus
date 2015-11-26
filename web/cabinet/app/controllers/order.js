;(function (angular) {
    "use strict";

    var orderModule = angular.module('ap.controller.order', [
        'ap.auth',
        'ap.api.internal',
        'ap.api.external',
        'ap.theme',
        'ui.router',
        'processing',
        'angularFileUpload'
    ]);

    orderModule.config(function ($stateProvider, $apThemeProvider) {
        $stateProvider
            .state('order', {
                url: '/order',
                templateUrl: $apThemeProvider.layoutUrl,
                pageTitle: 'Orders'
            })
            .state('order.search', {
                url: '/search?id',
                templateUrl: '/cabinet/views/order/search.html',
                pageTitle: 'Search',
                controller: OrderSearchController
            })
            .state('order.create', {
                url: '/create?from',
                templateUrl: '/cabinet/views/order/create.html',
                controller: OrderCreateController,
                pageTitle: 'Create'
            })
            .state('order.edit', {
                url: '/:order/edit?from',
                templateUrl: '/cabinet/views/order/edit.html',
                controller: OrderEditController,
                pageTitle: 'Edit'
            })
            .state('order.view', {
                url: '/:order',
                templateUrl: '/cabinet/views/order/view.html',
                controller: OrderViewController,
                pageTitle: 'View'
            });
    });

    function OrderSearchController($scope, $apInternalApi, $processing, $state, $location, $apAuth)
    {
        var
            query = {
                page: $location.search().page ? $location.search().page : 1,
                limit: $location.search().limit ? $location.search().limit : 50,
                types: [],
                creators: [],
                factories: [],
                clients: [],
                stages: [],
                cities: [],
                countries: [],
                createdFrom: null,
                createdTo: null
            };

        $scope.search = {
            types: [],
            creators: [],
            factories: [],
            clients: [],
            stages: [],
            cities: [],
            countries: [],
            createdFrom: null,
            createdTo: null
        };

        $scope.accesses = $apAuth.isAccesses({
            orderCreate: 'ORDER_CREATE'
        });

        $scope.accesses.orders = {};

        $scope.activeId = $location.search().id;

        var
            updateByQuery = function ()
            {
                $apInternalApi.orders(query)
                    .then(function (pagination) {
                        var i, order;

                        for (i in pagination.storage) {
                            if (pagination.storage.hasOwnProperty(i)) {
                                order = pagination.storage[i];

                                $scope.accesses.orders[order.id] = {
                                    edit: false,
                                    view: false
                                };

                                (function (i, order) {
                                    $apAuth.isGranted('ORDER_EDIT', order)
                                        .then(function (status) {
                                            $scope.accesses.orders[order.id].edit = status;
                                        });

                                    $apAuth.isGranted('ORDER_VIEW', order)
                                        .then(function (status) {
                                            $scope.accesses.orders[order.id].view = status;
                                        });
                                })(i, order);
                            }
                        }

                        $scope.pagination = pagination;
                    });
            },

            loadFactories = function()
            {
                $apInternalApi.factories()
                    .then(function (factories) {
                        $scope.factories = factories;
                        $scope.search.factories = factories.findByIds(query.factories);
                    });
            },

            loadClients = function ()
            {
                $apInternalApi.clients()
                    .then(function (clients) {
                        $scope.clients = clients;
                        $scope.search.clients = clients.findByIds(query.clients);
                    });
            },

            loadStages = function ()
            {
                $apInternalApi.stages()
                    .then(function (stages) {
                        $scope.stages = stages;
                        $scope.search.stages = stages.findByIds(query.stages);
                    });
            },

            loadCreators = function ()
            {
                $apInternalApi.orderCreators()
                    .then(function (creators) {
                        $scope.creators = creators;
                        $scope.search.creators = creators.findByIds(query.creators);
                    });
            },

            loadCities = function ()
            {
                $apInternalApi.clientCities()
                    .then(function (cities) {
                        $scope.cities = cities;
                        $scope.search.cities = cities.findByValue(query.cities);
                    });
            },

            loadCountries = function ()
            {
                $apInternalApi.clientCountries()
                    .then(function (countries) {
                        $scope.countries = countries;
                        $scope.search.countries = countries.findByValue(query.countries, 'code');
                    });
            },

            initializeSearchForMultiple = function (name, field)
            {
                if (typeof field == 'undefined') {
                    field = 'id';
                }

                var inQuery = $location.search().hasOwnProperty(name) ? $location.search()[name] : null;

                if (inQuery) {
                    query[name] = inQuery.split(',');
                }

                $scope.$watch('search.' + name, function (newValue, oldValue) {
                    if (newValue == oldValue) {
                        return;
                    }

                    if (Array.isArray(newValue)) {
                        if (Array.isArray(oldValue)) {
                            if (newValue.length == oldValue.length) {
                                return;
                            }
                        }

                        if (!newValue.length && !oldValue) {
                            return;
                        }
                    }

                    var ids = [],
                        i;

                    for (i in newValue) {
                        if (newValue.hasOwnProperty(i)) {
                            if (field) {
                                ids.push(newValue[i][field]);
                            } else {
                                ids.push(newValue[i]);
                            }
                        }
                    }

                    if (ids.length > 0) {
                        $location.search(name, ids.join(','));
                        query[name] = ids;
                    } else {
                        $location.search(name, null);
                        delete query[name];
                    }

                    updateByQuery();
                });
            },

            initializeSearchForDate = function (name)
            {
                var inQuery = $location.search().hasOwnProperty(name) ? $location.search()[name] : null;

                if (inQuery) {
                    query[name] = new Date(inQuery);
                }

                $scope.$watch('search.' + name, function (newValue, oldValue) {
                    if (newValue == oldValue) {
                        return;
                    }

                    if ($scope.search[name]) {
                        query[name] = $scope.search[name];
                        $location.search(name, $scope.search[name].format('yyyy-mm-dd'));
                    } else {
                        query[name] = null;
                        $location.search(name, null);
                    }

                    updateByQuery();
                });
            };

        $scope.search = {
            page: 1,
            limit: 50
        };

        /**
         * Change page callback
         */
        $scope.changePage = function ()
        {
            query.page = $scope.pagination.page;
            updateByQuery();
            $location.search('page', query.page);
        };

        /**
         * Edit order callback
         */
        $scope.edit = function (id)
        {
            $state.go('order.edit', {order: id, from: 'order.search'});
        };

        /**
         * View order callback
         */
        $scope.view = function (id)
        {
            $state.go('order.view', {order: id});
        };

        $scope.dtPicker = {
            createdFrom: {opened: false},
            createdTo: {opened: false}
        };

        $scope.dtPickerOpen = function (field)
        {
            $scope.dtPicker[field].opened = true;
        };

        initializeSearchForMultiple('creators');
        initializeSearchForMultiple('stages');
        initializeSearchForMultiple('clients');
        initializeSearchForMultiple('factories');
        initializeSearchForMultiple('cities', null);
        initializeSearchForMultiple('countries', 'code');
        initializeSearchForDate('createdFrom');
        initializeSearchForDate('createdTo');

        loadFactories();
        loadClients();
        loadStages();
        loadCreators();
        loadCities();
        loadCountries();

        updateByQuery();
    }

    function OrderCreateController($scope, $apInternalApi, $apExternalApi, $stateParams, $processing, $state, FileUploader, Notification)
    {
        $scope.order = {
            client: null,
            factory: null,
            stage: null,
            money: {
                amount: null,
                currency: null
            },
            comment: null,
            documentNumber: null,
            attachments: []
        };

        $scope.uploader = new FileUploader({
            url: document.getElementsByTagName('html')[0].getAttribute('data-cabinet-upload-url'),
            autoUpload: true,
            onCompleteItem: function (item, response, status) {
                if (status == 200) {
                    $scope.order.attachments.push(response);
                }
            }
        });

        $scope.removeAttachment = function (item)
        {
            var index = $scope.uploader.getIndexOfItem(item);
            item.remove();
            $scope.order.attachments.splice(index, 1);
        };

        var
            loadFactories = function ()
            {
                $apInternalApi.factories()
                    .then(function (factories) {
                        $scope.factories = factories;
                    });
            },

            loadClients = function ()
            {
                $apInternalApi.clients()
                    .then(function (clients) {
                        $scope.clients = clients;
                    });
            },

            loadCurrencies = function ()
            {
                $apExternalApi.currencies()
                    .then(function (currencies) {
                        $scope.currencies = currencies;
                    });
            },

            loadStages = function ()
            {
                $apInternalApi.stages()
                    .then(function (stages) {
                        $scope.stages = stages;
                    });
            };

        $scope.create = function ()
        {
            if ($processing.is($scope.order)) {
                // Now processing
                return;
            }

            $scope.order.errors = null;

            $processing.start($scope.order);

            $apInternalApi.orderCreate($scope.order)
                .then(
                    function (order) {
                        $processing.end($scope.order);
                        var _from = $stateParams.from,
                            success = function () {
                                Notification.success({
                                    message: 'Successfully create order.'
                                });
                            };

                        if (_from) {
                            $state.go('order.search', {id: order.id}).then(success);
                        } else {
                            $state.go('diary.search').then(success);
                        }
                    },

                    function (response) {
                        $processing.end($scope.order);

                        if (response.isRequestNotValid()) {
                            $scope.order.errors = response.errorData;
                        }
                    }
                );
        };

        loadFactories();
        loadClients();
        loadCurrencies();
        loadStages();
    }

    function OrderEditController($scope, $apInternalApi, $apExternalApi, $processing, $state, $stateParams, FileUploader, Notification)
    {
        var orderId = $stateParams.order;

        $scope.uploader = new FileUploader({
            url: document.getElementsByTagName('html')[0].getAttribute('data-cabinet-upload-url'),
            autoUpload: true,
            onCompleteItem: function (item, response, status) {
                if (status == 200) {
                    $scope.order.attachments.push(response);
                }
            }
        });

        var
            loadOrder = function ()
            {
                $apInternalApi.order(orderId)
                    .then(function (order) {
                        // Fix amount
                        order.money.amount = parseFloat(parseFloat(order.money.amount).toFixed(2));
                        order.attachments = [];

                        $scope.order = order;

                        loadFactories();
                        loadClients();
                        loadCurrencies();
                        loadStages();
                    });
            },

            loadFactories = function ()
            {
                $apInternalApi.factories()
                    .then(function (factories) {
                        $scope.factories = factories;
                    });
            },

            loadClients = function ()
            {
                $apInternalApi.clients()
                    .then(function (clients) {
                        $scope.clients = clients;
                    });
            },

            loadCurrencies = function ()
            {
                $apExternalApi.currencies()
                    .then(function (currencies) {
                        $scope.currencies = currencies;
                    });
            },

            loadStages = function ()
            {
                $apInternalApi.stages()
                    .then(function (stages) {
                        $scope.stages = stages;
                    });
            };

        $scope.update = function ()
        {
            if ($processing.is($scope.order)) {
                return;
            }

            $processing.start($scope.order);

            $apInternalApi.orderUpdate($scope.order)
                .then(
                    function (order) {
                        $processing.end(order);

                        var _from = $stateParams.from,
                            success = function () {
                                Notification.success({
                                    message: 'Successfully update order.'
                                });
                            };

                        if (_from) {
                            $state.go(_from, {id: order.id}).then(success);
                        } else {
                            $state.go('order.search', {id: order.id}).then(success);
                        }
                    },

                    function (response) {
                        if (response.isRequestNotValid()) {
                            $scope.order.errors = response.errorData;
                        }

                        $processing.end($scope.order);
                    }
                );
        };

        loadOrder();
    }

    function OrderViewController($scope, $apInternalApi, $stateParams, $state, $apAuth)
    {
        var orderId = $stateParams.order;

        $scope.accesses = {
            edit: false
        };

        var
            loadOrder = function ()
            {
                $apInternalApi.order(orderId)
                    .then(function (order) {
                        // Fix amount
                        order.money.amount = parseFloat(parseFloat(order.money.amount).toFixed(2));
                        $scope.order = order;

                        $apAuth.isGranted('ORDER_EDIT', order)
                            .then(function (status) {
                                $scope.accesses.edit = status;
                            });

                        loadDiaries();
                    });
            },
            loadDiaries = function ()
            {
                $apInternalApi.orderDiaries(orderId)
                    .then(function (diaries) {
                        console.log(diaries);
                        $scope.diaries = diaries;
                    });
            };

        $scope.edit = function (id) {
            $state.go('order.edit', {order: id})
        };

        loadOrder();
    }
})(window.angular);