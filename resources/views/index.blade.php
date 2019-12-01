<!doctype html>
<html>
<head>
	<title>REST API</title>
	<!-- Latest compiled and minified CSS -->
	<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css" integrity="sha384-HSMxcRTRxnN+Bdg0JdbxYKrThecOKuH5zCYotlSAcp1+c8xmyTe9GYg1l9a69psu" crossorigin="anonymous">

	<!-- Optional theme -->
	<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap-theme.min.css" integrity="sha384-6pzBo3FDv/PJ8r2KRkGHifhEocL+1X2rVCTTkUfGk7/0pbek5mMa1upzvWbrUbOZ" crossorigin="anonymous">

	<style type="text/css">

		pre {
		    white-space: pre-wrap;       /* Since CSS 2.1 */
		    white-space: -moz-pre-wrap;  /* Mozilla, since 1999 */
		    white-space: -pre-wrap;      /* Opera 4-6 */
		    white-space: -o-pre-wrap;    /* Opera 7 */
		    word-wrap: break-word;       /* Internet Explorer 5.5+ */
		}

	</style>

</head>
<body ng-app="rest">

<div class="row" ng-controller="HttpController">

	<div class="col-md-6">

		<div class="form-group">
		
			<p>End-point:</p>
			<input type="text" class="form-control" ng-model="request.url" />

		</div>

		<div class="form-group">
		
			<p>Method:</p>
			<select class="form-control" ng-model="request.method">
				<option value="GET">GET</option>
				<option value="POST">POST</option>
				<option value="PUT">PUT</option>
				<option value="PATCH">PATCH</option>
				<option value="DELETE">DELETE</option>
			</select>

		</div>

		<div class="form-group">

			<p>Authorisation:</p>

			<select class="form-control" ng-model="request.authorisation">
				<option value="">None</option>
				<option value="token">Bearer Token</option>
				<option value="basic">Basic Auth</option>
			</select>

		</div>

		<div class="form-group" ng-if="request.authorisation == 'token'">
			<p>Token:</p>
			<input type="text" class="form-control" ng-model="request.token" />
		</div>
		
		<div class="form-group" ng-if="request.authorisation == 'basic'">
			<p>Username:</p>
			<input type="text" class="form-control" ng-model="request.username" />
			<p>Password:</p>
			<input type="password" class="form-control" ng-model="request.password" />
		</div>

		<div class="form-group">

			<p>Body:</p>

			<select class="form-control" ng-model="request.body_type">
				<option value="">None</option>
				<option value="form">Form/Url Encoded</option>
				<option value="json">JSON</option>
			</select>
			
			<textarea ng-model="request.body_form" class="form-control" rows="6"></textarea>

		</div>

		<div class="form-group">
			<button class="btn btn-success" ng-click="send(request)">Send</button>
		</div>

	</div>

	<div class="col-md-6">
		<div id="result" style="height: 90%; overflow-y: scroll">
			<pre id="json_result">@{{result}}</pre>
		</div>
	</div>

</div>


<script src="https://ajax.googleapis.com/ajax/libs/angularjs/1.2.14/angular.min.js"></script>

<script>
var app = angular.module("rest", []);
app.controller("HttpController", [ '$scope', '$http', function ($scope, $http) {

	$scope.request = { 
						'method': 'GET',
						'url' : 'http://bayshore.grape.loancirrus.com:92/api/v1/clients/4471/loans',
						'authorisation' : 'token',
						'token' : 'qtjJrpcnoskomQx0I62BXZuJcTwhkW6GGXzV6ecDS3V6Y1Zo9aFcShTOnfWN' 
					};

	$scope.send = function (request) {

		url = '/request/send';

		$http.post(url, { request : request }).success(
			function(response){
				$scope.result = response;
				document.getElementById("json_result").innerHTML = JSON.stringify(response, undefined, 2);
			}
		).error(
			function(error){
				console.log(error)
			}
		);
	};

}]);
</script>

</body>
</html>