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
</div>