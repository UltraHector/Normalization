<div id="subFindMinimalCover" class="mainContentRightColumn" ng-controller="subFindMinimalCoverCtrl">
    <h2 style="border-bottom: dotted 1px;" >Find Minimal Cover</h2>
    <br/>
    <br/>
    <div class="normalizedTablesDiv">
        <span ng-repeat="functionalDependency in minimalCover" >
            <div class="normalizedTableAttributeSpan"> 
                <span ng-repeat="attribute in functionalDependency.ls">
                     {{attribute}} &nbsp;&nbsp;
                 </span>
                 &nbsp;
                 <img src="images/icon_arrow_right.png" width="16" height="16">
                 &nbsp;
                 <span ng-repeat="attribute in functionalDependency.rs">
                     {{attribute}} &nbsp;&nbsp;
                 </span>
            </div>
        </span>
    </div>
    
    <br/>
    <div>
        <h2 style="border-bottom: dotted 1px; padding-bottom: 5px;" >
            Show Steps 
            <label class="switch">
                <input type="checkbox" id="findMinimalCoverSwitchButton">
                <div class="slider round"></div>
            </label>
        </h2>
        <div class="normalizedTablesDiv" id="findMinimalCoverDivId" style="display: none;">
            <br> Step 1: Rewrite the FD into those with only one attribute on RHS. We obtain: <br>
            
            <span ng-repeat="functionalDependency in steps['step1']" >
                <div class="normalizedTableAttributeSpan"> 
                    <span ng-repeat="attribute in functionalDependency.ls">
                        {{attribute}} &nbsp;&nbsp;
                    </span>
                    &nbsp;
                    <img src="images/icon_arrow_right.png" width="16" height="16">
                    &nbsp;
                    <span ng-repeat="attribute in functionalDependency.rs">
                        {{attribute}} &nbsp;&nbsp;
                    </span>
                </div>
            </span>
          
            <br> Step 2: Remove trivial FDs (those where the RHS is also in the LHS). We obtain: <br>
            <span ng-repeat="functionalDependency in steps['step2']" >
                <div class="normalizedTableAttributeSpan"> 
                    <span ng-repeat="attribute in functionalDependency.ls">
                        {{attribute}} &nbsp;&nbsp;
                    </span>
                    &nbsp;
                    <img src="images/icon_arrow_right.png" width="16" height="16">
                    &nbsp;
                    <span ng-repeat="attribute in functionalDependency.rs">
                        {{attribute}} &nbsp;&nbsp;
                    </span>
                </div>
            </span>
            
            <br> Step 3: Minimize LHS of each FD. We obtain: <br>
            <span ng-repeat="functionalDependency in steps['step3']" >
                <div class="normalizedTableAttributeSpan"> 
                    <span ng-repeat="attribute in functionalDependency.ls">
                        {{attribute}} &nbsp;&nbsp;
                    </span>
                    &nbsp;
                    <img src="images/icon_arrow_right.png" width="16" height="16">
                    &nbsp;
                    <span ng-repeat="attribute in functionalDependency.rs">
                        {{attribute}} &nbsp;&nbsp;
                    </span>
                </div>
            </span>
            
            <br> Step 4: Remove redundant FDs (those that are implied by others). We obtain: <br>
            <span ng-repeat="functionalDependency in steps['step4']" >
                <div class="normalizedTableAttributeSpan"> 
                    <span ng-repeat="attribute in functionalDependency.ls">
                        {{attribute}} &nbsp;&nbsp;
                    </span>
                    &nbsp;
                    <img src="images/icon_arrow_right.png" width="16" height="16">
                    &nbsp;
                    <span ng-repeat="attribute in functionalDependency.rs">
                        {{attribute}} &nbsp;&nbsp;
                    </span>
                </div>
            </span>
        </div>
    </div>

</div>