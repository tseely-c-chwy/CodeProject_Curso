angular.module('app.controllers')
        .controller('ProjectNewController', ['$scope','$location','$cookies','Client','Project','appConfig',
        function ($scope,$location,$cookies,Client,Project,appConfig) {
                $scope.project = new Project();
                $scope.status = appConfig.project.status;
                
                $scope.due_date = {
                    status: {
                        opened: false
                    }
                };
                
                $scope.open = function($event) {
                    $scope.due_date.status.opened = true;
                };

                $scope.save = function() {
                    if($scope.form.$valid) {
                        var user = $cookies.getObject('user');
                        $scope.project.owner_id = user.id;
                        $scope.project.$save().then(function() {
                            $location.path('/projects');
                        });
                    }
                };
                
                $scope.formatName = function(model) {
                    if(model) {
                        return model.name;
                    }
                    return '';
                };
                
                $scope.getClients = function(name) {
                    return Client.query({
                        search: name,
                        searchFields: 'name:like' 
                    }).$promise;
                };
                
                $scope.selectClient = function(item) {
                    $scope.project.client_id = item.id;
                };
        }]);