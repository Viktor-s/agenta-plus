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
     * @param {*} $rootScope
     *
     * @constructor
     */
    function Authorization($apInternalApi, $apAuthStorage, $q, $rootScope)
    {
        var voters = [
            ],
            profile = null;

        /**
         * Add voter to storage
         *
         * @param voter
         *
         * @returns {Authorization}
         */
        this.addVoter = function (voter)
        {
            voters.push(voter);

            return this;
        };

        /**
         * Add voters
         *
         * @param {Array} voters
         *
         * @returns {Authorization}
         */
        this.addVoters = function (voters)
        {
            var i;

            for (i in voters) {
                if (voters.hasOwnProperty(i)) {
                    this.addVoter(voters[i]);
                }
            }

            return this;
        };

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
                        profile = null,
                        deferred.reject();
                    }
                );

            return deferred.promise;
        };

        /**
         * Get active profile
         *
         * @returns {*}
         */
        this.profile = function ()
        {
            var d = $q.defer();

            if (profile) {
                d.resolve(profile);
            } else {
                $apInternalApi.profileActive()
                    .then(
                    function (p) {
                        profile = p;
                        $rootScope.activeUser = p;
                        d.resolve(p);
                    },
                    function (r) {
                        d.reject(r);
                    }
                );
            }

            return d.promise;
        };

        /**
         * Is granted to resource
         *
         * @param {String} attribute
         * @param {Object} [object]
         */
        this.isGranted = function (attribute, object)
        {
            var d = $q.defer(),
                runVoters = function (p, a, o) {
                    var i, visitor, access;
                    for (i = 0; i < voters.length; i++) {
                        visitor = voters[i];

                        access = visitor.vote(p, a, o);

                        if (access === -1) {
                            return false;
                        } else if (access === 1) {
                            return true;
                        }
                    }
                };

            this.profile()
                .then(
                    function (p) {
                        var status = runVoters(p, attribute, object);
                        d.resolve(status);
                    },
                    function (r) {
                        // @todo: control error
                    }
                );

            return d.promise;
        };

        /**
         * Is accesses for attributes
         *
         * @param {Object} attributes
         *
         * @returns {Object}
         */
        this.isAccesses = function (attributes)
        {
            var accesses = {},
                i, self = this;

            for (i in attributes) {
                if (attributes.hasOwnProperty(i)) {
                    accesses[i] = false;

                    (function (e) {
                        self.isGranted(attributes[e])
                            .then(function (status) {
                                accesses[e] = status;
                            });
                    })(i);
                }
            }

            return accesses;
        };
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
