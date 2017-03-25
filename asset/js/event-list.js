angular.module('event')
    .config(['$routeProvider', 'piProvider', 'config',
        function ($routeProvider, piProvider, config) {

            //Get template url
            function tpl(name) {
                return config.assetRoot + name + '.html';
            }

            function resolve(action) {
                return {
                    data: ['$q', '$route', '$rootScope', '$location', 'server',
                        function ($q, $route, $rootScope, $location, server) {
                            var deferred = $q.defer();
                            var params = $route.current.params;
                            $('.ajax-spinner').show();

                            if (config.pageType == 'category') {
                                $location.search('category', config.categorySlug);
                            } else if (config.pageType == 'tag') {
                                $location.search('tag', config.tagTerm);
                            }

                            $rootScope.alert = 2;
                            server.get(action, params).success(function (data) {
                                data.filter = params;
                                deferred.resolve(data);
                                $rootScope.alert = '';
                            });
                            return deferred.promise;
                        }
                    ]
                };
            }

            $routeProvider.when('/search', {
                templateUrl: tpl('event-list'),
                controller: 'ListCtrl',
                resolve: resolve('search')
            }).otherwise({
                redirectTo: '/search'
            });

            piProvider.setHashPrefix();
            piProvider.addTranslations(config.t);
            piProvider.addAjaxInterceptors();
        }
    ])
    .service('server', ['$http', '$cacheFactory', 'config',
        function ($http, $cacheFactory, config) {

            var urlRoot = config.urlRoot;

            this.get = function (action, params) {
                return $http.get(urlRoot + action, {
                    params: params
                });
            }

            this.filterEmpty = function (obj) {
                var search = {};
                for (var i in obj) {
                    if (obj[i]) {
                        search[i] = obj[i];
                    }
                }
                return search;
            }

        }
    ])
    .controller('ListCtrl', ['$scope', '$location', 'data', 'config', 'server',
        function ($scope, $location, data, config, server) {

            angular.extend($scope, data);

            $scope.$watch('paginator.page', function (newValue, oldValue) {
                if (newValue === oldValue) return;
                $location.search('page', newValue);
            });

            $scope.filterAction = function () {
                $location.search(server.filterEmpty($scope.filter));
                $location.search('page', null);
            }

            $('.ajax-spinner').hide();
        }
    ]);