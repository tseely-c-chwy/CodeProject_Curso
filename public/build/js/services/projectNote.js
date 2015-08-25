angular.module('app.services').service('ProjectNote',['$resource','$routeParams','appConfig',function($resource,$routeParams,appConfig) {
        return $resource(appConfig.baseUrl + '/project/' + $routeParams.id + '/note/:idNote', {idNote: '@idNote'}, {
            update: {
                method: 'PUT'
            }
        });
}]);