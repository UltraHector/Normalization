<div id="subLoadExample" class="subLoadExample" ng-controller="subLoadExampleCtrl">

	<div style="background:white;margin:auto; width:60%; top: 10%; position:relative; padding-left:30px;padding-right:30px; padding-top:20px;padding-bottom:20px;">
		<a href="" id="closeLoadExample" style="float:right; right:10px;font-size:40px;">&#215;</a>
		<h2 style="border-bottom: dotted 1px;">Load Example</h2>
		<br/>
		<div>
			<select id="fdExampleSelect" style="margin:10px;padding:10px; width:60%;">
				<option ng-repeat="fdExample in fdExamples.fdExamples" value="{{fdExample.title}}" >
					{{fdExample.title}}
				</option>
			</select>
			<input type="button" id="loadExampleConfirmBtn" value="Load" />
		</div>
	</div>
    
    
</div>