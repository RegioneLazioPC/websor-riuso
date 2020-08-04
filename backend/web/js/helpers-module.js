/*
 HELPER SERVICE
 */

angular.module('region.helpers', [])

    .config(['$httpProvider', function($httpProvider) {
        $httpProvider.defaults.headers.post['Content-Type'] = 'application/x-www-form-urlencoded;charset=utf-8';

        /**
         * The workhorse; converts an object to x-www-form-urlencoded serialization.
         * @param {Object} obj
         * @return {String}
         */
        var param = function(obj) {
            var query = '', name, value, fullSubName, subName, subValue, innerObj, i;

            for(name in obj) {
                value = obj[name];

                if(value instanceof Array) {
                    for(i=0; i<value.length; ++i) {
                        subValue = value[i];
                        fullSubName = name + '[' + i + ']';
                        innerObj = {};
                        innerObj[fullSubName] = subValue;
                        query += param(innerObj) + '&';
                    }
                }
                else if(value instanceof Object) {
                    for(subName in value) {
                        subValue = value[subName];
                        fullSubName = name + '[' + subName + ']';
                        innerObj = {};
                        innerObj[fullSubName] = subValue;
                        query += param(innerObj) + '&';
                    }
                }
                else if(value !== undefined && value !== null)
                    query += encodeURIComponent(name) + '=' + encodeURIComponent(value) + '&';
            }

            return query.length ? query.substr(0, query.length - 1) : query;
        };

        // Override $http service's default transformRequest
        $httpProvider.defaults.transformRequest = [function(data) {
            return angular.isObject(data) && String(data) !== '[object File]' ? param(data) : data;
        }];

    }])

    .constant("appConfig", {
        //'SERVICES_URL': 'stubs/'
        //'SERVICES_URL': 'http://5.249.128.23:8080/rest/',
        'SERVICES_URL': '',
        "MAP":{
            "version" : "3.29",
            "lat" : 38.88247,
            "lon" : 16.60086
        }
    })


    .directive('productElement', [function() {
        return {
            scope: {
                product: '@'
            },
            template: '<span>{{product}}</span>{{product.name}}'
        };
    }])

    .directive('backImg', function() {
        return function(scope, element, attrs) {
            var url = attrs.backImg;
            var shadow = '';
            if (attrs.shadow) {
                var direction = attrs.shadow || "left";
                shadow = 'linear-gradient(to ' + direction + ', rgb(0, 0, 0) 0%, rgba(0, 0, 0, 0.3) 50%, rgba(0, 0, 0, 0) 100%),';
            }
            attrs.$observe('backImg', function(value) {
                element.css({
                    'background-image': shadow + 'url(' + value + ')',
                    'background-size': 'cover'
                });
            });
        }
    })
    .directive('cover', [function() {
        return {
            restrict: 'ACE',
            link: function(scope, element, attrs) {
                scope.$watch(attrs.cover, function(value) {
                    element.css('background-image', (value));
                    element.css('background-color', "red");
                });
            }
        }
    }])

    .filter('data', function($filter) {
        return function(input) {
            if (input == null) {
                return "";
            }

            var _date = $filter('date')(new Date(input), 'dd MMM yyyy');

            return _date.toUpperCase();

        };
    })

