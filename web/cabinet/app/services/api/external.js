;(function (angular) {
    "use strict";

    var externalApi = angular.module('ap.api.external', ['jsonRpc']);

    externalApi.provider('$apExternalApiConfig', ExternalApiConfig);
    externalApi.service('$apExternalApi', ExternalApi);

    /**
     * External API configuration
     *
     * @constructor
     */
    function ExternalApiConfig ()
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

    function ExternalApi ($q, $jsonRpc, $apExternalApiConfig)
    {
        var
            getUrl = function ()
            {
                var u = $apExternalApiConfig.getUrl();

                if (!u) {
                    throw new Error('Missing URL in AgentPlus external API configuration.');
                }

                return u;
            };

        /**
         * Get countries
         *
         * @returns {*}
         */
        this.countries = function ()
        {
            var d = $q.defer();

            $jsonRpc.request(getUrl(), 'countries')
                .then(
                    function (response) {d.resolve(response.result);},
                    function (response) {d.reject(response);}
                );

            return d.promise;
        };
    }
})(window.angular);