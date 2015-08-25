angular.module('app.controllers')
        .controller('ProjectNoteRemoveController', [
            '$scope','$location','$routeParams','ProjectNote', 
            function ($scope, $location, $routeParams, ProjectNote) {
                $scope.projectNote = ProjectNote.get({idNote: $routeParams.idNote});
               
                $scope.remove = function() {
                    $scope.projectNote.$delete({idNote: $routeParams.idNote}).then(function(){
                       $location.path('/project/' + $routeParams.id + '/notes'); 
                    });
                };
        }]);