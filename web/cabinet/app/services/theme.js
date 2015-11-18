;(function (angular) {
    "use strict";

    var themeModule = angular.module('ap.theme', []);

    themeModule.provider('$apTheme', function () {
        var $this = this;

        Object.defineProperty(this, 'name', {
            get: function () {
                return window.document.getElementsByTagName('html')[0].getAttribute('data-theme')
            }
        });

        Object.defineProperty(this, 'layoutUrl', {
            get: function () {
                return '/cabinet/theme/' + $this.name + '/views/layout.html'
            }
        });

        this.resolveTemplate = function (template)
        {
            return '/cabinet/theme/' + $this.name + '/views/' + template;
        };

        this.resolveDirectiveTemplate = function (directive, name)
        {
            var template = 'directives/' + directive + '/' + name + '.html';

            return this.resolveTemplate(template);
        };

        this.$get = function ()
        {
            return this;
        }
    });
})(window.angular);
