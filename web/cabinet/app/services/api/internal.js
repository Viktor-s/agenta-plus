;(function (angular) {
    "use strict";

    var internalApi = angular.module('ap.api.internal', [, 'ap.auth', 'jsonRpc']);

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
            var deferred = $q.defer(),
                headers = getHeaders();

            if (username) {
                headers['Authorization'] = 'Basic ' + window.btoa(username + ':' + password)
            }

            $jsonRpc.request(getUrl(), 'ping', null, null, headers)
                .then(
                function () { deferred.resolve(); },
                function (response) { deferred.reject(response); }
            );

            return deferred.promise;
        };
    }
})(window.angular);
