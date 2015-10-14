angular.module('app.services').service('ProjectTask',['$resource','$filter','appConfig',
        function($resource,$filter,appConfig) {
            
            function transformData(data) {
                if(angular.isObject(data)) {
                    var o = angular.copy(data);
                    if (data.hasOwnProperty('start_date')) {
                        o.start_date = $filter('date')(data.start_date,'yyyy-MM-dd');                        
                    }
                    if (data.hasOwnProperty('due_date')) {
                        o.due_date = $filter('date')(data.due_date,'yyyy-MM-dd');                        
                    }
                    return appConfig.utils.transformRequest(o);
                }
                return data;                
            };
            
            return $resource(appConfig.baseUrl + '/project/:id/task/:idTask', {idTask: '@idTask'}, {
                save: {
                    method: 'POST',
                    transformRequest: transformData
                },
                get: {
                    method: 'GET',
                    transformResponse: function(data,headers) {
                        var o = appConfig.utils.transformResponse(data, headers);
                        if(angular.isObject(o)) { 
                            if (o.hasOwnProperty('start_date') && o.start_date) {
                                var arrayStartDate = o.start_date.split('-'),
                                        month = parseInt(arrayStartDate[1]) - 1;
                                o.start_date = new Date(arrayStartDate[0],month,arrayStartDate[2]);
                            }
                            if (o.hasOwnProperty('due_date') && o.due_date) {
                                var arrayDueDate = o.due_date.split('-'),
                                        month = parseInt(arrayDueDate[1]) - 1;
                                o.due_date = new Date(arrayDueDate[0],month,arrayDueDate[2]);
                            }
                        }
                        return o;
                    }
                },
                update: {
                    method: 'PUT',
                    transformRequest: transformData
                },
                delete: {
                    method: 'DELETE'
                }
        });
}]);