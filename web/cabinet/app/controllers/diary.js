;(function (angular) {
    "use strict";

    var diaryModule = angular.module('ap.controller.diary', [
        'ap.auth',
        'ap.api.internal',
        'ap.api.external',
        'ap.api.external',
        'ui.router',
        'processing',
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

    function DiarySearchController($scope, $apAuth, $apInternalApi, $location, $state, $processing, Notification)
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

            if (diary && $processing.isNot(diary)) {
                $processing.start(diary);

                $apInternalApi.diaryRemove(id)
                    .then(
                        function (_diary) {
                            $processing.end(diary);
                            $scope.pagination.storage.replaceObjectById(id, _diary);
                            Notification.success({
                                message: 'Successfully remove diary.'
                            });
                        },

                        function () {
                            $processing.end(diary);
                        }
                    );
            }
        };

        $scope.restore = function (id)
        {
            var diary = $scope.pagination.storage.findById(id);

            if (diary && $processing.isNot(diary)) {
                $processing.start(diary);

                $apInternalApi.diaryRestore(id)
                    .then(
                        function (_diary) {
                            $processing.end(diary);
                            $scope.pagination.storage.replaceObjectById(id, _diary);
                            Notification.success({
                                message: 'Successfully restore diary.'
                            });
                        },

                        function () {
                            $processing.end(diary);
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

    function DiaryCreateController($scope, $apInternalApi, $apExternalApi, $processing, $state, FileUploader, Notification)
    {
        $scope.diary = {
            client: null,
            factories: [],
            money: {
                amount: null,
                currency: null
            },
            comment: null,
            documentNumber: null,
            attachments: [],
            catalogs: []
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
            },

            loadCatalogs = function ()
            {
                $apInternalApi.catalogs()
                    .then(function (catalogs) {
                        $scope.catalogs = catalogs.storage;
                    });
            };

        $scope.create = function ()
        {
            if ($processing.is($scope.diary)) {
                // Now processing
                return;
            }

            $scope.diary.errors = null;

            $processing.start($scope.diary);

            $apInternalApi.diaryCreate($scope.diary)
                .then(
                    function () {
                        $processing.end($scope.diary);
                        $state.go('diary.search')
                            .then(function () {
                                Notification.success({
                                    message: 'Successfully create diary.'
                                });
                            });
                    },

                    function (response) {
                        $processing.end($scope.diary);

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
        loadCatalogs();
    }

    function DiaryEditController($scope, $apInternalApi, $processing, $stateParams, $state, Notification)
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
                            loadGotCatalogs();
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
            },

            loadGotCatalogs = function ()
            {
                $apInternalApi.diaryGotCatalogs(diaryId)
                    .then(function (gotCatalogs) {
                        $scope.gotCatalogs = gotCatalogs;
                    });
            };

        $scope.update = function ()
        {
            if ($processing.is($scope.diary)) {
                return;
            }

            $processing.start($scope.diary);
            $scope.diary.errors = null;

            $apInternalApi.diaryUpdate($scope.diary)
                .then(
                    function () {
                        $processing.end($scope.diary);
                        $state.go('diary.search')
                            .then(function () {
                                Notification.success({
                                    message: 'Successfully edit diary.'
                                });
                            });
                    },

                    function (response) {
                        $processing.end($scope.diary);

                        if (response.isRequestNotValid()) {
                            $scope.diary.errors = response.errorData;
                        }
                    }
                );
        };

        loadDiary();
    }
})(window.angular);