;(function (angular, $) {
    "use strict";

    var internalApi = angular.module('ap.api.internal', ['ap.auth', 'jsonRpc']);

    internalApi.provider('$apInternalApiConfig', InternalApiConfig);
    internalApi.service('$apInternalApi', InternalApi);

    /**
     * Internal API configuration
     *
     * @constructor
     */
    function InternalApiConfig ()
    {
        var _url = null;

        /**
         * Set URL
         *
         * @param {String} url
         *
         * @returns {*}
         */
        this.setUrl = function (url)
        {
            _url = url;

            return this;
        };

        /**
         * Get URL
         *
         * @returns {String}
         */
        this.getUrl = function ()
        {
            return _url;
        };

        /**
         * Gets the configuration
         *
         * @returns {*}
         */
        this.$get = function ()
        {
            return this;
        };
    }

    /**
     * Internal API
     *
     * @param {*} $jsonRpc
     * @param {*} $q
     * @param {*} $apInternalApiConfig
     * @param {*} $apAuthStorage
     *
     * @constructor
     */
    function InternalApi($jsonRpc, $q, $apInternalApiConfig, $apAuthStorage)
    {
        var
            getHeaders = function ()
            {
                var h = {};

                if ($apAuthStorage.has()) {
                    var _auth = $apAuthStorage.get();
                    h['Authorization'] = 'Basic ' + window.btoa(_auth.username + ':' + _auth.password);
                }

                h['X-Client-Timezone'] = jstz.determine().name();

                return h;
            },

            getUrl = function ()
            {
                var u = $apInternalApiConfig.getUrl();

                if (!u) {
                    throw new Error('Missing URL in AgentPlus internal API configuration.');
                }

                return u;
            };

        /**
         * Ping method. For check auth info, you must send username and password
         *
         * @param {String} [username]
         * @param {String} [password]
         *
         * @returns {*}
         */
        this.ping = function (username, password)
        {
            var d = $q.defer(),
                headers = getHeaders();

            if (username) {
                headers['Authorization'] = 'Basic ' + window.btoa(username + ':' + password)
            }

            $jsonRpc.request(getUrl(), 'ping', null, null, headers)
                .then(
                function () { d.resolve(); },
                function (response) { d.reject(response); }
            );

            return d.promise;
        };

        /**
         * Get active profile
         *
         * @returns {Object}
         */
        this.profileActive = function ()
        {
            var d = $q.defer();

            $jsonRpc.request(getUrl(), 'profile', null, null, getHeaders())
                .then(
                    function (response) {d.resolve(response.result); },
                    function (response) {d.reject(response); }
                );

            return d.promise;
        };

        /**
         * Get all clients
         *
         * @param {Object} query
         *
         * @returns {*}
         */
        this.clients = function (query)
        {
            query = $.extend({
                page: null
            }, query);

            var d = $q.defer(),
                params = {
                    page: query.page
                };

            $jsonRpc.request(getUrl(), 'client.search', params, null, getHeaders())
                .then(
                    function (response) {d.resolve(response.result);},
                    function (response) {d.reject(response);}
                );

            return d.promise;
        };

        /**
         * Load client
         *
         * @param {String} id
         *
         * @returns {*}
         */
        this.client = function (id)
        {
            var d = $q.defer(),
                params = {
                    id: id
                };

            $jsonRpc.request(getUrl(), 'client', params, null, getHeaders())
                .then(
                    function (response) {d.resolve(response.result);},
                    function (response) {d.reject(response);}
                );

            return d.promise;
        };

        /**
         * Update client
         *
         * @param {Object} client
         *
         * @returns {*}
         */
        this.clientUpdate = function (client)
        {
            var d = $q.defer(),
                params = {
                    id: client.id,
                    name: client.name,
                    city: client.city,
                    address: client.address,
                    notes: client.notes,
                    phones: client.phones,
                    emails: client.emails
                };

            if (client.hasOwnProperty('country') && client.country && client.country.hasOwnProperty('code')) {
                params.country = client.country.code;
            } else if (client.hasOwnProperty('country')) {
                params.country = client.country;
            } else if (client.hasOwnProperty('countryCode')) {
                params.country = client.countryCode;
            }

            $jsonRpc.request(
                getUrl(),
                'client.update',
                params,
                null,
                getHeaders()
            ).then(
                function (response) {d.resolve(response.result);},
                function (response) {d.reject(response);}
            );

            return d.promise;
        };

        /**
         * Create client
         *
         * @param client
         */
        this.clientCreate = function (client)
        {
            var d = $q.defer(),
                params = {
                    name: client.name,
                    city: client.city,
                    address: client.address,
                    notes: client.notes,
                    phones: client.phones,
                    emails: client.emails
                };

            if (client.hasOwnProperty('country') && client.country && client.country.hasOwnProperty('code')) {
                params.country = client.country.code;
            } else if (client.hasOwnProperty('country')) {
                params.country = client.country;
            } else if (client.hasOwnProperty('countryCode')) {
                params.country = client.countryCode;
            }

            $jsonRpc.request(
                getUrl(),
                'client.create',
                params,
                null,
                getHeaders()
            ).then(
                function (response) {d.resolve(response.result);},
                function (response) {d.reject(response);}
            );

            return d.promise;
        };

        /**
         * View factories
         *
         * @param {Object} query
         *
         * @returns {*}
         */
        this.factories = function (query)
        {
            query = $.extend({
                page: null
            }, query);

            var d = $q.defer(),
                params = {
                    page: query.page
                };

            $jsonRpc.request(getUrl(), 'factory.search', params, null, getHeaders())
                .then(
                    function (response) {d.resolve(response.result);},
                    function (response) {d.reject(response);}
                );

            return d.promise;
        };

        /**
         * Create factory
         *
         * @param {Object} factory
         *
         * @returns {*}
         */
        this.factoryCreate = function (factory)
        {
            var d = $q.defer(),
                params = {
                    name: factory.name
                };

            $jsonRpc.request(getUrl(), 'factory.create', params, null, getHeaders())
                .then(
                    function (response) {d.resolve(response.result);},
                    function (response) {d.reject(response);}
                );

            return d.promise;
        };

        /**
         * Update factory
         *
         * @param {Object} factory
         *
         * @returns {*}
         */
        this.factoryUpdate = function (factory)
        {
            var d = $q.defer(),
                params = {
                    id: factory.id,
                    name: factory.name
                };

            $jsonRpc.request(getUrl(), 'factory.update', params, null, getHeaders())
                .then(
                    function (response) {d.resolve(response.result);},
                    function (response) {d.reject(response);}
                );

            return d.promise;
        };

        /**
         * Get all stages
         *
         * @returns {*}
         */
        this.stages = function ()
        {
            var d = $q.defer();

            $jsonRpc.request(getUrl(), 'stage.search', null, null, getHeaders())
                .then(
                    function (response) {d.resolve(response.result);},
                    function (response) {d.reject(response);}
                );

            return d.promise;
        };

        /**
         * Create stage
         *
         * @param {Object} stage
         *
         * @returns {*}
         */
        this.stageCreate = function (stage)
        {
            var d = $q.defer(),
                params = {
                    label: stage.label,
                    position: stage.position
                };

            $jsonRpc.request(getUrl(), 'stage.create', params, null, getHeaders())
                .then(
                    function (response) {d.resolve(response.result);},
                    function (response) {d.reject(response);}
                );

            return d.promise;
        };

        /**
         * Update stage
         *
         * @param {Object} stage
         *
         * @returns {*}
         */
        this.stageUpdate = function (stage)
        {
            var d = $q.defer(),
                params = {
                    id: stage.id,
                    name: stage.name,
                    position: stage.position
                };

            $jsonRpc.request(getUrl(), 'stage.update', params, null, getHeaders())
                .then(
                    function (response) {d.resolve(response.result);},
                    function (response) {d.reject(response);}
                );

            return d.promise;
        };

        /**
         * Get all diaries
         *
         * @param {Object} query
         *
         * @returns {*}
         */
        this.diaries = function (query)
        {
            query = $.extend({
                page: 1,
                limit: 50
            });

            var d = $q.defer(),
                params = {
                    page: query.page,
                    limit: query.limit
                };

            $jsonRpc.request(getUrl(), 'diary.search', params, null, getHeaders())
                .then(
                    function (response) {d.resolve(response.result); },
                    function (response) {d.reject(response);}
                );

            return d.promise;
        };

        /**
         * Get one diary
         *
         * @param {String} id
         *
         * @returns {*}
         */
        this.diary = function (id)
        {
            var d = $q.defer();

            $jsonRpc.request(getUrl(), 'diary', {id: id}, null, getHeaders())
                .then(
                    function (response) {d.resolve(response.result);},
                    function (response) {d.reject(response);}
                );

            return d.promise;
        };

        var __diaryGetRequestParams = function (diary)
        {
            diary = $.extend({
                comment: null,
                client: null,
                factories: [],
                money: null,
                attachments: []
            }, diary);

            var
                factory, i,
                params = {
                    comment: diary.comment,
                    documentNumber: diary.documentNumber
                };

            if (diary.id) {
                params.id = diary.id;
            }

            if (diary.client) {
                if (typeof diary.client == 'object') {
                    params.client = diary.client.id
                } else {
                    params.client = diary.client;
                }
            }

            if (diary.factories && diary.factories.length > 0) {
                params.factories = [];

                for (i in diary.factories) {
                    if (diary.factories.hasOwnProperty(i)) {
                        factory = diary.factories[i];

                        if (typeof factory == 'object') {
                            params.factories.push(factory.id);
                        } else {
                            params.factories.push(factory);
                        }
                    }
                }
            }

            if (diary.money && (diary.money.amount || diary.money.currency)) {
                params.money = {};

                if (diary.money.amount) {
                    params.money.amount = diary.money.amount;
                }

                if (diary.money.currency) {
                    params.money.currency = diary.money.currency;
                }
            }

            if (diary.attachments && diary.attachments.length > 0) {
                params.attachments = diary.attachments;
            }

            return params;
        };

        /**
         * Create diary
         *
         * @param {Object} diary
         *
         * @returns {*}
         */
        this.diaryCreate = function (diary)
        {
            var d = $q.defer(),
                params = __diaryGetRequestParams(diary);

            $jsonRpc.request(getUrl(), 'diary.create', params, null, getHeaders())
                .then(
                    function (response) {d.resolve(response.result);},
                    function (response) {d.reject(response);}
                );

            return d.promise;
        };

        /**
         * Update diary
         *
         * @param {Object} diary
         *
         * @returns {*}
         */
        this.diaryUpdate = function (diary)
        {
            var d = $q.defer(),
                params = __diaryGetRequestParams(diary);

            $jsonRpc.request(getUrl(), 'diary.update', params, null, getHeaders())
                .then(
                    function (response) {d.resolve(response.result);},
                    function (response) {d.reject(response);}
                );

            return d.promise;
        };

        /**
         * Remove diary
         *
         * @param {String} id
         *
         * @returns {*}
         */
        this.diaryRemove = function (id)
        {
            var d = $q.defer();

            $jsonRpc.request(getUrl(), 'diary.remove', {id: id}, null, getHeaders())
                .then(
                    function (response) {d.resolve(response.result);},
                    function (response) {d.reject(response);}
                );

            return d.promise;
        };

        /**
         * Restore diary
         *
         * @param {String} id
         *
         * @returns {*}
         */
        this.diaryRestore = function (id)
        {
            var d = $q.defer();

            $jsonRpc.request(getUrl(), 'diary.restore', {id: id}, null, getHeaders())
                .then(
                    function (response) {d.resolve(response.result);},
                    function (response) {d.reject(response);}
                );

            return d.promise;
        };

        var __orderGetRequestParams = function (order)
        {
            var params = __diaryGetRequestParams(order);

            if (order.stage) {
                if (typeof order.stage == 'object') {
                    params.stage = order.stage.id;
                } else {
                    params.stage = order.stage;
                }
            }

            return params;
        };

        /**
         * Create order
         *
         * @param {Object} order
         *
         * @returns {*}
         */
        this.orderCreate = function (order)
        {
            var d = $q.defer(),
                params = __orderGetRequestParams(order);

            $jsonRpc.request(getUrl(), 'order.create', params, null, getHeaders())
                .then(
                    function (r) {d.resolve(r.result);},
                    function (r) {d.reject(r);}
                );

            return d.promise;
        }
    }
})(window.angular, jQuery);
