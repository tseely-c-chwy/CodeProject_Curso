angular.module('app.services').service('ProjectNote',['$resource','appConfig',
        function($resource,appConfig) {
        return $resource(appConfig.baseUrl + '/project/:id/note/:idNote', {idNote: '@idNote'}, {
            update: {
                method: 'PUT'
            }
        });
}]);