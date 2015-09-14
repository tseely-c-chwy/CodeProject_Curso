angular.module('app.controllers')
        .controller('ProjectEditController', [
            '$scope','$location','$routeParams','$cookies','Client','Project','appConfig',
            function ($scope, $location, $routeParams, $cookies, Client, Project, appConfig) {
                $scope.clients = Client.query();
                $scope.project = Project.get({id: $routeParams.id});
                $scope.status = appConfig.project.status;
                
                $scope.save = function() {
                    if($scope.form.$valid) {
                        var user = $cookies.getObject('user');
                        $scope.project.owner_id = user.id;
                        Project.update({id:$scope.project.id},$scope.project,function() {
                            $location.path('/projects');
                        });
                    }
                };
                
                $scope.formatName = function(id) {
                    if(id) {
                        for(var i in $scope.clients) {
                            if($scope.clients[i].id == id) {
                                return $scope.clients[i].name;
                            }
                        }
                    }
                    return '';
                };
                
                $scope.getClients = function(name) {
                    return Client.query({
                        search: name,
                        searchFields: 'name:like' 
                    }).$promise;
                };
        }]);