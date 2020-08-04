angular.module('allerta', ['region.helpers'])
.controller("allertaController", function($scope) {

    var selfScope = this;
    selfScope.cale={
        today:{
            cala1:{
                id:null,
                color:'#70d66e',
                temporali:0,
                idro:0
            },
            cala2:{id:null, color:'#70d66e'},
            cala3:{id:null, color:'#70d66e'},
            cala4:{id:null, color:'#70d66e'},
            cala5:{id:null, color:'#70d66e'},
            cala6:{id:null, color:'#70d66e'},
            cala7:{id:null, color:'#70d66e'},
            cala8:{id:null, color:'#70d66e'}
        },
        tomorrow:{
            cala1:{id:null, color:'#70d66e'},
            cala2:{id:null, color:'#70d66e'},
            cala3:{id:null, color:'#70d66e'},
            cala4:{id:null, color:'#70d66e'},
            cala5:{id:null, color:'#70d66e'},
            cala6:{id:null, color:'#70d66e'},
            cala7:{id:null, color:'#70d66e'},
            cala8:{id:null, color:'#70d66e'}
        }
    };

    selfScope.changeMap = function (day) {

        if(day == 'today'){
            arrayCale = selfScope.cale.today;
        }else{
            arrayCale = selfScope.cale.tomorrow;
        }

        angular.forEach(arrayCale, function (cala, key) {
            //debugger;
            console.log();
            if(arrayCale[key].temporali >= arrayCale[key].idro){
                checkId = arrayCale[key].temporali;
            }else{
                checkId = arrayCale[key].idro;
            }
            //debugger;
            switch(checkId){
                case "1":
                    arrayCale[key].color = '#70d66e';
                    break;
                case "2":
                    arrayCale[key].color = '#f4fb35';
                    break;
                case "3":
                    arrayCale[key].color = '#ffc73c';
                    break;
                case "4":
                    arrayCale[key].color = '#ff2d3c';
                    break;
            }
            //debugger;
            //console.log(cala);
        });
    }

})
.directive('svgMapToday', ['$compile', function ($compile) {
    return {
        restrict: 'A',
        templateUrl: '../images/allertaMapToday.svg',
        link: function (scope, element, attrs) {
            var regions = element[0].querySelectorAll('path');
            var count=1;
            angular.forEach(regions, function (path, key) {
                var regionElement = angular.element(path);
                // regionElement.attr("region", "");
                // console.log(regionElement.attr('fill'));
                // if(!regionElement.attr('fill')){
                //     regionElement.attr("ng-model", "ctrl.cala"+count);
                //     count++;
                // }
                //$compile(regionElement)(scope);
            })
        }
    }
}])
.directive('svgMapTomorrow', ['$compile', function ($compile) {
    return {
        restrict: 'A',
        templateUrl: '../images/allertaMapTomorrow.svg',
        link: function (scope, element, attrs) {
            var regions = element[0].querySelectorAll('path');
            var count=1;
            angular.forEach(regions, function (path, key) {
                var regionElement = angular.element(path);
                // regionElement.attr("region", "");
                // console.log(regionElement.attr('fill'));
                // if(!regionElement.attr('fill')){
                //     regionElement.attr("ng-model", "ctrl.cala"+count);
                //     count++;
                // }
                //$compile(regionElement)(scope);
            })
        }
    }
}])
.directive('svgMapNumbers', ['$compile', function ($compile) {
    return {
        restrict: 'A',
        templateUrl: '../images/allertaMapNumbers.svg',
        link: function (scope, element, attrs) {
            var regions = element[0].querySelectorAll('path');
            var count=1;
            angular.forEach(regions, function (path, key) {
                var regionElement = angular.element(path);
                // regionElement.attr("region", "");
                // console.log(regionElement.attr('fill'));
                // if(!regionElement.attr('fill')){
                //     regionElement.attr("ng-model", "ctrl.cala"+count);
                //     count++;
                // }
                //$compile(regionElement)(scope);
            })
        }
    }
}])
;