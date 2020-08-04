angular.module('AutocompleteModule', ['region.helpers', 'autoCompleteModule'])
.factory('AutocompleteService', ['$http', '$location', '$httpParamSerializer', function( $http, $location, $httpParamSerializer ) {

    var AutocompleteService = {
    };

    AutocompleteService.search = function ( comune, string, csrf ){
        var formData = new FormData;
        formData.append('_csrf-backend', csrf);
        formData.append('id_comune', comune);
        formData.append('string', string);

        return $http({
            url: siteurl+'/evento/search-indirizzo',
            data: formData,
            headers: { 'Content-Type': undefined},
            transformRequest: angular.identity,
            method: "POST"
        });
    };

    return AutocompleteService;

}])
.controller("AutocompleteController", function($scope, appConfig, AutocompleteService) {

    var selfScope = this;
    selfScope.comune = "";

    selfScope.comune = null;
    $scope.csrf = null;

    $scope.autocompleteValues = [];

    $scope.init = function(token) {
        $scope.csrf = token;
    }

    selfScope.autoCompleteOptions = {
        minimumChars: 3,
        itemTemplate : $templateCache.get('color-item-template'),
        data: function (searchText) {
            searchText = searchText.toUpperCase();

            AutocompleteService.search(selfScope.comune, val, $scope.csrf)
            .then(function(res){
                return res.data;
            }).catch(function(err){
                console.warn(err);
                return []
            })
            
        }
    }


});