;(function (angular) {
    "use strict";

    var diaryModule = angular.module('ap.controller.diary', ['ap.auth', 'ap.api.internal', 'ap.api.external', 'ap.api.external', 'ui.router', 'ap.loading']);

    diaryModule.config(function ($stateProvider) {
        $stateProvider
            .state('diary', {
                url: '/diary',
                templateUrl: '/cabinet/views/diary/main.html'
            })
            .state('diary.search', {
                url: '/search',
                templateUrl: '/cabinet/views/diary/search.html',
                controller: DiarySearchController
            })
            .state('diary.create', {
                url: '/create',
                templateUrl: '/cabinet/views/diary/create.html',
                controller: DiaryCreateController
            })
            .state('diary.edit', {
                url: '/:diary/edit',
                templateUrl: '/cabinet/views/diary/edit.html',
                controller: DiaryEditController
            });
    });

    function DiarySearchController($scope, $apAuth, $apInternalApi, $location, $state)
    {
        $scope.accesses = $apAuth.isAccesses({
            diaryCreate: 'DIARY_CREATE'
        });

        $scope.accesses.diaries = {};

        var
            query = {
                page: $location.search().page ? $location.search().page : 1,
                limit: $location.search().limit ? $location.search().limit : 50
            },

            loadDiariesByQuery = function ()
            {
                $apInternalApi.diaries(query)
                    .then(function (pagination) {
                        var i, diary;

                        for (i in pagination.storage) {
                            if (pagination.storage.hasOwnProperty(i)) {
                                diary = pagination.storage[i];

                                $scope.accesses.diaries[diary.id] = {
                                    edit: false,
                                    remove: false
                                };

                                (function (i, diary) {
                                    $apAuth.isGranted('DIARY_EDIT', diary)
                                        .then(function (status) {
                                            $scope.accesses.diaries[diary.id].edit = status;
                                        });
                                })(i, diary);
                            }
                        }

                        $scope.pagination = pagination;
                    });
            };

        $scope.edit = function (id)
        {
            $state.go('diary.edit', {diary: id});
        };

        loadDiariesByQuery();
    }

    function DiaryCreateController($scope, $apInternalApi, $apExternalApi, $apLoading, $state)
    {
        $scope.diary = {
            client: null,
            factories: [],
            money: {
                amount: null,
                currency: null
            },
            comment: null
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
            };

        $scope.create = function ()
        {
            if ($apLoading.isProcessed($scope.diary)) {
                // Not processing
                return;
            }

            $scope.diary.errors = null;

            $apLoading.startProcess($scope.diary);

            $apInternalApi.diaryCreate($scope.diary)
                .then(
                    function () {
                        $apLoading.endProcess($scope.diary);
                        $state.go('diary.search')
                    },

                    function (response) {
                        $apLoading.endProcess($scope.diary);

                        if (response.isRequestNotValid()) {
                            $scope.diary.errors = response.errorData;
                            console.log($scope.diary);
                        }
                    }
                );
        };

        loadFactories();
        loadClients();
        loadCurrencies();
    }

    function DiaryEditController($scope, $apInternalApi, $apLoading, $stateParams, $state)
    {
        var
            diaryId = $stateParams.diary,

            loadDiary = function () {
                $apInternalApi.diary(diaryId)
                    .then(
                        function (diary) {
                            $scope.diary = diary;

                            loadClients();
                            loadFactories();
                        },

                        function (r) {console.log(r); /** @todo control error */}
                    );
            },

            loadClients = function ()
            {
                $apInternalApi.clients()
                    .then(function (clients) {
                        $scope.clients = clients;
                    });
            },

            loadFactories = function ()
            {
                $apInternalApi.factories()
                    .then(function (factories) {
                        $scope.factories = factories;
                    });
            };

        $scope.update = function ()
        {
            if ($apLoading.isProcessed($scope.diary)) {
                return;
            }

            $apLoading.startProcess($scope.diary);
            $scope.diary.errors = null;

            $apInternalApi.diaryUpdate($scope.diary)
                .then(
                    function () {
                        $apLoading.endProcess($scope.diary);
                        $state.go('diary.search');
                    },

                    function (response) {
                        $apLoading.endProcess($scope.diary);

                        if (response.isRequestNotValid()) {
                            $scope.diary.errors = response.errorData;
                        }
                    }
                );
        };

        loadDiary();
    }
})(window.angular);