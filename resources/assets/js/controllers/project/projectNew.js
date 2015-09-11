angular.module('app.controllers')
        .controller('ProjectNewController', ['$scope','$location','$cookies','Client','Project','appConfig',
        function ($scope,$location,$cookies,Client,Project,appConfig) {
                $scope.clients = Client.query();
                $scope.project = new Project();
                $scope.status = appConfig.project.status;

                $scope.save = function() {
                    if($scope.form.$valid) {
                        var user = $cookies.getObject('user');
                        $scope.project.owner_id = user.id;
                        $scope.project.$save().then(function() {
                            $location.path('/projects');
                        });
                    }
                };
        }]);