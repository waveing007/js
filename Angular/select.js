var app = angular.module('myApp', []);
app.controller('customersCtrl', function($scope, $http) {
   $http.get("fetch_data.php")
   .then(function (response) {$scope.names = response.data;});
});