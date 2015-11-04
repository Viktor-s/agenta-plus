;(function (angular) {
    "use strict";

    var diaryModule = angular.module('ap.controller.diary', [
        'ap.auth',
        'ap.api.internal',
        'ap.api.external',
        'ap.api.external',
        'ui.router',
        'ap.loading',
        'angularFileUpload'
    ]);

    diaryModule.config(function ($stateProvider) {
        $stateProvider
            .state('diary', {
                url: '/diary',
                templateUrl: '/cabinet/views/diary/main.html',
                pageTitle: 'Diaries'
            })
            .state('diary.search', {
                url: '/search',
                templateUrl: '/cabinet/views/diary/search.html',
                controller: DiarySearchController,
                pageTitle: 'Search'
            })
            .state('diary.create', {
                url: '/create',
                templateUrl: '/cabinet/views/diary/create.html',
                controller: DiaryCreateController,
                pageTitle: 'Create'
            })
            .state('diary.edit', {
                url: '/:diary/edit',
                templateUrl: '/cabinet/views/diary/edit.html',
                controller: DiaryEditController,
                pageTitle: 'Edit'
            });
    });

    function DiarySearchController($scope, $apAuth, $apInternalApi, $location, $state, $apLoading)
    {
        $scope.accesses = $apAuth.isAccesses({
            diaryCreate: 'DIARY_CREATE',
            orderCreate: 'ORDER_CREATE'
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
                                    remove: false,
                                    restore: false
                                };

                                (function (i, diary) {
                                    $apAuth.isGranted('DIARY_EDIT', diary)
                                        .then(function (status) {
                                            $scope.accesses.diaries[diary.id].edit = status;
                                        });

                                    $apAuth.isGranted('DIARY_REMOVE', diary)
                                        .then(function (status) {
                                            $scope.accesses.diaries[diary.id].remove = status;
                                        });

                                    $apAuth.isGranted('DIARY_RESTORE', diary)
                                        .then(function (status) {
                                            $scope.accesses.diaries[diary.id].restore = status;
                                        });
                                })(i, diary);
                            }
                        }

                        $scope.pagination = pagination;
                    });
            };

        $scope.remove = function (id)
        {
            var diary = $scope.pagination.storage.findById(id);

            if (diary && $apLoading.isNotProcessed(diary)) {
                $apLoading.startProcess(diary);

                $apInternalApi.diaryRemove(id)
                    .then(
                        function (_diary) {
                            $apLoading.endProcess(diary);
                            $scope.pagination.storage.replaceObjectById(id, _diary);
                        },

                        function () {
                            $apLoading.endProcess(diary);
                        }
                    );
            }
        };

        $scope.restore = function (id)
        {
            var diary = $scope.pagination.storage.findById(id);

            if (diary && $apLoading.isNotProcessed(diary)) {
                $apLoading.startProcess(diary);

                $apInternalApi.diaryRestore(id)
                    .then(
                        function (_diary) {
                            $apLoading.endProcess(diary);
                            $scope.pagination.storage.replaceObjectById(id, _diary);
                        },

                        function () {
                            $apLoading.endProess(diary);
                        }
                    );
            }
        };

        $scope.edit = function (id)
        {
            $state.go('diary.edit', {diary: id});
        };

        loadDiariesByQuery();
    }

    function DiaryCreateController($scope, $apInternalApi, $apExternalApi, $apLoading, $state, FileUploader)
    {
        $scope.diary = {
            client: null,
            factories: [],
            money: {
                amount: null,
                currency: null
            },
            comment: null,
            attachments: []
        };

        $scope.uploader = new FileUploader({
            url: document.getElementsByTagName('html')[0].getAttribute('data-cabinet-upload-url'),
            autoUpload: true,
            onCompleteItem: function (item, response, status) {
                if (status == 200) {
                    $scope.diary.attachments.push(response);
                }
            }
        });

        $scope.removeAttachment = function (item)
        {
            var index = $scope.uploader.getIndexOfItem(item);
            item.remove();
            $scope.diary.attachments.splice(index, 1);
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
                // Now processing
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