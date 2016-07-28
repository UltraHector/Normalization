<div id="subFindCandidateKeys" class="mainContentRightColumn" ng-controller="subFindCandidateKeysCtrl">
    <h2 style="border-bottom: dotted 1px;" >Find Candidate Keys</h2>
    <br/>
    <br/>
    <div class="candidateKeysListDiv">
        <h4>Candidate Keys Found</h4>
        <div ng-if='status'>
            <ul>
                <li ng-repeat="candidateKey in candidateKeys">
                    <span class="candidateKeysAttributeSpan" ng-repeat="attribute in candidateKey">
                        {{attribute}}
                    </span>
                    <br/><br/>
                </li>
            </ul>
        </div>
        <div ng-if='!status'>
            <h4>{{errorMessage}}</h4>
        </div>
    </div>
    
    <div>
        <h2 style="border-bottom: dotted 1px; padding-bottom: 5px;" >
            Show Steps 
            <label class="switch">
                <input type="checkbox" id="findCandidatekeysSwitchButton">
                <div class="slider round"></div>
            </label>
        </h2>
        <div class="normalizedTablesDiv" id="findCandidateKeysStepsDivId" style="display: none;">
            <span ng-repeat="step in steps" >
                <div class="normalizedTableAttributeSpan"> 
                    <div ng-bind-html="step | unsafe"></div>
                </div>
            </span>
            
        </div>
    </div>
</div>