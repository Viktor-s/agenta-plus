;(function (angular) {
    "use strict";

    var authModule = angular.module('ap.auth', ['ap.api.internal', 'ui.router', 'ngStorage']);

    authModule.service('$apAuthStorage', AuthorizationStorage);
    authModule.service('$apAuth', Authorization);
    authModule.config(function ($httpProvider) {
        $httpProvider.interceptors.push(AuthorizationInterceptors);
    });

    /**
     * Authorization storage
     *
     * @param $localStorage
     *
     * @constructor
     */
    function AuthorizationStorage($localStorage)
    {
        /**
         * Save auth info
         *
         * @param {String} username
         * @param {String} password
         *
         * @returns {AuthorizationStorage}
         */
        this.save = function (username, password)
        {
            $localStorage.apAuth = {
                username: username,
                password: password
            };

            return this;
        };

        /**
         * Get authorization info
         *
         * @returns {{username: null, password: null}}
         */
        this.get = function ()
        {
            if ($localStorage.hasOwnProperty('apAuth')) {
                return {
                    username: $localStorage.apAuth.username,
                    password: $localStorage.apAuth.password
                }
            }

            return {
                username: null,
                password: null
            }
        };

        /**
         * Clear storage
         *
         * @returns AuthorizationStorage
         */
        this.clear = function ()
        {
            delete $localStorage.apAuth;
        };

        /**
         * Has authorization info?
         *
         * @returns {boolean}
         */
        this.has = function ()
        {
            return this.get().username ? true : false;
        }
    }

    /**
     * Authorization service
     *
     * @param {*} $apInternalApi
     * @param {*} $apAuthStorage
     * @param {*} $q
     *
     * @constructor
     */
    function Authorization($apInternalApi, $apAuthStorage, $q)
    {
        /**
         * Check username and password
         *
         * @params {String} username
         * @params {String} password
         *
         * @returns {*}
         */
        this.check = function (username, password)
        {
            var deferred = $q.defer();

            $apInternalApi
                .ping(username, password)
                .then(
                    function () { deferred.resolve(); },
                    function () { deferred.reject(); }
                );

            return deferred.promise;
        };

        /**
         * Auth process
         *
         * @param {String} username
         * @param {String} password
         *
         * @returns {*}
         */
        this.auth = function (username, password)
        {
            var deferred = $q.defer();

            this
                .check(username, password)
                .then(
                    function () {
                        $apAuthStorage.save(username, password);
                        deferred.resolve();
                    },

                    function () {
                        $apAuthStorage.clear();
                        deferred.reject();
                    }
                );

            return deferred.promise;
        }
    }

    /**
     * Authorization interceptor for control 401 error.
     *
     * @param {*} $q
     * @param {*} $injector
     *
     * @returns {Object}
     *
     * @constructor
     */
    function AuthorizationInterceptors($q, $injector)
    {
        return {
            request: function (config)
            {
                return config;
            },

            response: function (response)
            {
                return response;
            },

            responseError: function (rejection)
            {
                if (rejection.status == 401) {
                    // Unauthorized
                    $injector.get('$state').go('login');
                }

                return $q.reject(rejection);
            }
        }
    }

})(window.angular);
