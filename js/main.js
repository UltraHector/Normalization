/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
var tagInputCounter = 3;

$(document).ready(function () {
    /* Tool tips widget when use */
    $('[data-toggle="tooltip"]').tooltip();

    /* Input field values */
    $(".dynamicInput").find(".leftDependencyInputDivClass").find("input").tagit({
        singleField: true,
        beforeTagAdded: function(event, ui){
            return addBeforeTagChecker(event, ui);
        }
    });
    $(".dynamicInput").find(".rightDependencyInputDivClass").find("input").tagit({
        singleField: true,
        beforeTagAdded: function(event, ui){
            return addBeforeTagChecker(event, ui);
        }
    });
    
    $("#attributesTextArea").blur(function () {
        updateAutocomplete();
    });


    // If find candidate keys button is pressed - Generate candidate key results and display them on the page
    $('#editAttibutesLink').click(function () {
        showSubPage("#subEditAttributes");
    });
    
    $('#learningResourcesLink').click(function () {
        showSubPage("#subLearningResources");
    });
	
	$('#closeLoadExample').click(function () {
       $('#subLoadExample').hide();
    });
    
    $("#attributesTextArea").keypress(function () {
        $('.leftDependencyInputDivClass').each(function (i, obj) {
            var leftFunctionDependencyInputId = "#leftFunctionDependencyInput_" + $(this).parent().get(0).id;
            try{
                $(leftFunctionDependencyInputId).tagit('removeAll');
            }catch(err){
                //
            }
        });
        $('.rightDependencyInputDivClass').each(function (i, obj) {
            var rightFunctionDependencyInputId = "#rightFunctionDependencyInput_" + $(this).parent().get(0).id;
            try{
                $(rightFunctionDependencyInputId).tagit('removeAll');
            }catch(err){
                //
            }
        });
    });
    
       
    /*******************************/
    /* Immidiate execute codes */
    /*******************************/
    updateAutocomplete();
    showSubPage("#subEditAttributes");
	
	$('#subLoadExample').hide();
});

    
var normalizationApp = angular.module('normalizationApp', []);
normalizationApp.controller('subFindCandidateKeysCtrl', function ($scope, $http) {
    $('#findCandidateKeysLink').click(function () {
        var inputData = getInputDataInJson();
        $http.post('normalize.php?normalizeOption=findCandidateKeys', inputData).then(
                function (data, status, jqXHR) {
                    showSubPage("#subFindCandidateKeys");
                    //2NF
                    $scope.status = true;
                    if(data['data']['status'] !== undefined){
                        $scope.status = false;
                    }
                    if(!$scope.status){
                        $scope.status = data['data']['status'];
                        $scope.errorMessage = data['data']['errorMessage'];   
                    }else{
                        $scope.candidateKeys = data['data'];
                    }
                },
                function (response) {
                    // Error happened, try again
                    // TODO
                });
    });
});
    
    
/* Check the normalized form */
normalizationApp.controller('subCheckNormalFormCtrl', function ($scope, $http) {
    $('#checkNormalFormLink').click(function () {
        var inputData = getInputDataInJson();
        $http.post('normalize.php?normalizeOption=checkNormalForm', inputData).then(
                function (data, status, jqXHR) {
                    showSubPage("#subCheckNormalForm");
                    //2NF
                    $scope.is2NFNormalized = data['data']['is2NF']['isNormalized'];
                    $scope.steps2NF = data['data']['is2NF']['steps'];
                    if(!$scope.isNormalized){
                        $scope.violationDescription2NF = data['data']['is2NF']['violation']['desctiption'];   
                    }else{
                        $scope.violationDescription2NF = data['data']['is2NF']['violation']['FD'];
                    }
                    //3NF
                    $scope.is3NFNormalized = data['data']['is3NF']['isNormalized'];
                    $scope.steps3NF = data['data']['is3NF']['steps'];
                    if(!$scope.isNormalized){
                        $scope.violationDescription3NF = data['data']['is3NF']['violation']['desctiption'];   
                    }else{
                        $scope.violationDescription3NF = data['data']['is3NF']['violation']['FD'];
                    }
                    //BCNF
                    $scope.isBCNFNormalized = data['data']['isBCNF']['isNormalized'];
                    $scope.stepsBCNF = data['data']['isBCNF']['steps'];
                    if(!$scope.isNormalized){
                        $scope.violationDescriptionBCNF = data['data']['isBCNF']['violation']['desctiption'];   
                    }else{
                        $scope.violationDescriptionBCNF = data['data']['isBCNF']['violation']['FD'];
                    }
                },
                function (response) {
                    // Error happened, try again
                    // TODO
                });
    });
});

normalizationApp.controller('subFindMinimalCoverCtrl', function ($scope, $http) {
    $('#findMinimalCoverLink').click(function () {
        var inputData = getInputDataInJson();
        $http.post('normalize.php?normalizeOption=findMinimalCover', inputData).then(
                function (data, status, jqXHR) {
                    showSubPage("#subFindMinimalCover");
                    $scope.minimalCover = data['data'];
                },
                function (response) {
                    // Error happened, try again
                    // TODO
                });
    });
});



normalizationApp.controller('subNormalize2NFCtrl', function ($scope, $http) {
    $('#normalize2NFLink').click(function () {
        var inputData = getInputDataInJson();
        $http.post('normalize.php?normalizeOption=normalize2NF', inputData).then(
                function (data, status, jqXHR) {
                    showSubPage("#subNormalize2NF");
                    $scope.normalizedTables = data['data'];
                },
                function (response) {
                    // Error happened, try again
                    // TODO
                });
    });
});
normalizationApp.controller('subNormalize3NFCtrl', function ($scope, $http) {
    $('#normalize3NFLink').click(function () {
        var inputData = getInputDataInJson();
        $http.post('normalize.php?normalizeOption=normalize3NF', inputData).then(
                function (data, status, jqXHR) {
                    showSubPage("#subNormalize3NF");
                    $scope.normalizedTables = data['data'];
                },
                function (response) {
                    // Error happened, try again
                   // TODO
                });
    });
});
normalizationApp.controller('subNormalizeBCNFCtrl', function ($scope, $http) {
    $('#normalizeBCNFLink').click(function () {
        var inputData = getInputDataInJson();
        $http.post('normalize.php?normalizeOption=normalizeBCNF', inputData).then(
                function (data, status, jqXHR) {
                    showSubPage("#subNormalizeBCNF");
                    $scope.normalizedTables = data['data'];
                },
                function (response) {
                    // Error happened, try again
                     // TODO
                });
    });
});

normalizationApp.controller('subLoadExampleCtrl', function ($scope, $http) {
    $('#loadExampleLink').click(function () {
        var inputData = getInputDataInJson();
        $http.post('normalize.php?normalizeOption=loadExample', inputData).then(
                function (data, status, jqXHR) {
                   $('#subLoadExample').show();
                   $scope.fdExamples = data['data'];
                },
                function (response) {
                    // Error happened, try again
                    // TODO
                });
    });
	$('#loadExampleConfirmBtn').click(function () {
		for (var index = 0; index < $scope.fdExamples.fdExamples.length; ++index) {
			if($scope.fdExamples.fdExamples[index]['title'] == $("#fdExampleSelect").val()){
				
				/*
				 * load attributes
				 */
				var attributesCommaSeperated = "";
				var attributesNumber = $scope.fdExamples.fdExamples[index]['attributes'].length;
				for(var attributeIndex = 0; attributeIndex < attributesNumber - 1;  ++attributeIndex) {
					attributesCommaSeperated = attributesCommaSeperated.concat($scope.fdExamples.fdExamples[index]['attributes'][attributeIndex]);
					attributesCommaSeperated = attributesCommaSeperated.concat(", ");
				}
				attributesCommaSeperated = attributesCommaSeperated.concat($scope.fdExamples.fdExamples[index]['attributes'][attributesNumber - 1]);
				$("#attributesTextArea").val(attributesCommaSeperated);
				
				
				/*
				 * load fds
				 */
				$(".dynamicInput").remove();
				tagInputCounter = 0;
				var fdsNumber = $scope.fdExamples.fdExamples[index]['fd'].length;
				for(var fdIndex = 0; fdIndex < fdsNumber; ++fdIndex){
					addInput("mainContentFunctionalDependency");
					var newAddedCounter = tagInputCounter - 1;
					var newAddedId = "functionDependency_" + newAddedCounter;
					
					for(var fdAttriIndex = 0; fdAttriIndex < $scope.fdExamples.fdExamples[index]['fd'][fdIndex]['left'].length; fdAttriIndex++){
						$("#" + newAddedId).find(".leftDependencyInputDivClass").find("input").first().tagit("createTag", $scope.fdExamples.fdExamples[index]['fd'][fdIndex]['left'][fdAttriIndex]);
					}
					for(var fdAttriIndex = 0; fdAttriIndex < $scope.fdExamples.fdExamples[index]['fd'][fdIndex]['right'].length; fdAttriIndex++){
						$("#" + newAddedId).find(".rightDependencyInputDivClass").find("input").first().tagit("createTag", $scope.fdExamples.fdExamples[index]['fd'][fdIndex]['right'][fdAttriIndex]);
					}

				}

				$('#subLoadExample').hide();
				break;
			}
		}
	});
	
});


/*******************************/
/* Functions */
/*******************************/
/*This function for adding or deleting input field*/
function addInput(divId) {
    var newdiv = document.createElement('div');

    var newDivId = "functionDependency_" + tagInputCounter;
    var leftDependencyInputId = "leftFunctionDependencyInput_" + newDivId;
    var rightDependencyInputId = "rightFunctionDependencyInput_" + newDivId;

    newdiv.setAttribute("class", "dynamicInput row");
    newdiv.setAttribute("id", newDivId);


    var divInnerHtml = "<div class=\"leftDependencyInputDivClass col-md-4\">\n\
                            <input type=\"text\" id=\"" + leftDependencyInputId + "\"autocomplete=\"on\"/> \n\
                        </div> \n\
                        <div class=\"col-md-1\"> \n\
                            <img src=\"images/icon_arrow_right.png\"> \n\
                        </div> \n\
                        <div class=\"rightDependencyInputDivClass col-md-4\"> \n\
                            <input type=\"text\" id=\"" + rightDependencyInputId + "\"autocomplete=\"on\"/> \n\
                        </div> \n\
                        <input type=\"button\" class=\"functionalDependencyButtonClass col-md-1\" value=\"Delete\" onClick=\"deleteInput(\'" + newDivId + "\')\" />";

    newdiv.innerHTML = (divInnerHtml);

    document.getElementById(divId).appendChild(newdiv);
    $("#" + newDivId).find(".leftDependencyInputDivClass").find("input").tagit({
        singleField: true,
        beforeTagAdded: function(event, ui){
            return addBeforeTagChecker(event, ui);
        }
    });
    $("#" + newDivId).find(".rightDependencyInputDivClass").find("input").tagit({
        singleField: true,
        beforeTagAdded: function(event, ui){
            return addBeforeTagChecker(event, ui);
        }
    });

    updateAutocomplete();
    tagInputCounter++;
}

/* delete a dynamic input field */
function deleteInput(divId) {
    $('#' + divId).remove();
}

function addBeforeTagChecker(event, ui) {
    // do something special
    var attributesArray = getTextAttributes();
    if(attributesArray.indexOf(ui.tagLabel) === -1){
        return false;
    }
}



function updateAutocomplete() {
    var attributesArray = getTextAttributes();
    $(".dynamicInput").find("input").click(function () {
        $(".dynamicInput").find("input").autocomplete({
            source: attributesArray,
            minLength: 0
                    //TODO only allow input values
        }).focus(function () {
            //Use the below line instead of triggering keydown
            $(this).data("autocomplete").search($(this).val());
        });
    });
    $('input').keydown(function (e) {
        e.preventDefault();
        return false;
    });
}

// Get the input data from attributes field and the functional dependencies
// The input data is ornanized as a multi-dimension array
function getInputDataInJson() {
    var functionalDependencyData = {};

    // fetch the attributes
    functionalDependencyData["attributes"] = getTextAttributes();

    // fetch the dependency data
    var functionDependencies = [];
    $('.leftDependencyInputDivClass').each(function (i, obj) {
        var leftFunctionDependencyInputId = "#leftFunctionDependencyInput_" + $(this).parent().get(0).id;
        var rightFunctionDependencyInputId = "#rightFunctionDependencyInput_" + $(this).parent().get(0).id;
        if($(rightFunctionDependencyInputId).tagit('assignedTags').length > 0 && $(rightFunctionDependencyInputId).tagit('assignedTags').length > 0){
            functionDependencies[i] = {};
            functionDependencies[i]["leftAttributes"] = removeCommaArray($(leftFunctionDependencyInputId).tagit('assignedTags'));   
        }
    });
    $('.rightDependencyInputDivClass').each(function (i, obj) {
        var leftFunctionDependencyInputId = "#leftFunctionDependencyInput_" + $(this).parent().get(0).id;
        var rightFunctionDependencyInputId = "#rightFunctionDependencyInput_" + $(this).parent().get(0).id;
        
        if($(leftFunctionDependencyInputId).tagit('assignedTags').length > 0 && $(rightFunctionDependencyInputId).tagit('assignedTags').length > 0){
            functionDependencies[i]["rightAttributes"] = removeCommaArray($(rightFunctionDependencyInputId).tagit('assignedTags'));
        }
    });
    
    functionalDependencyData["functionalDependencies"] = functionDependencies;
    
    return functionalDependencyData;
}


function removeCommaArray(attributesArray){
    var newArrayValue = [];
    for (var index = 0; index < attributesArray.length; ++index) {
        attributesArray[index] = attributesArray[index].replace(/\s+/g, '');
        if(attributesArray[index].length > 0){
            newArrayValue.push(attributesArray[index]);
        }
    }
    return newArrayValue;
}

function getTextAttributes(){
    var attributesArray = $("#attributesTextArea").val().split(",");
    return removeCommaArray(attributesArray);
}

function showSubPage(subPageId){
    $("[data-toggle='tooltip']").tooltip('hide');
    $("#subEditAttributes").hide();
    $("#subCheckNormalForm").hide();
    $("#subFindCandidateKeys").hide();
    $("#subLearningResources").hide();
    $("#subNormalize2NF").hide();
    $("#subNormalize3NF").hide();
    $("#subNormalizeBCNF").hide();
    $("#subFindMinimalCover").hide();
    $(subPageId).show();
}