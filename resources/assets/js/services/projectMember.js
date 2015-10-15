angular.module('app.services').service('ProjectMember',['$resource','appConfig',
        function($resource,appConfig) {
        return $resource(appConfig.baseUrl + '/project/:id/member/:idProjectMember', {idProjectMember: '@idProjectMember'}, {
            update: {
                method: 'PUT'
            }
        });
}]);