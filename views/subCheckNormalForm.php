<div id="subCheckNormalForm" class="mainContentRightColumn" ng-controller="subCheckNormalFormCtrl" style="display:none;">
    <h2 style="border-bottom: dotted 1px;">Check Normal Form</h2>
    <br/>
    <br/>
    <div id="2NF">
        <div id="is2NFDiv" class="row checkNormalFormDiv" ng-if='is2NFNormalized'>
            <div class="col-md-4">
                <img src="images/icon_tick.png" width="64" height="64">
            </div>
            <div class="col-md-8">
                <h3><b>2NF</b></h3>
                <h4>The table is in 2NF</h4>
            </div>
        </div>
        <div id="isNot2NFDiv" class="row checkNormalFormDiv" ng-if='!is2NFNormalized'>
            <div class="col-md-4">
                <img src="images/icon_cross.png" width="64" height="64">
            </div>
            <div class="col-md-8">
                <h3><b>2NF</b></h3>
                <h4>The table is not in 2NF.</h4>
            </div>
        </div>
    </div>

    <div id="3NF">
        <div id="is3NFDiv" class="row checkNormalFormDiv" ng-if='is3NFNormalized'>
            <div class="col-md-4">
                <img src="images/icon_tick.png" width="64" height="64">
            </div>
            <div class="col-md-8">
                <h3><b>3NF</b></h3>
                <h4>The table is in 3NF</h4>
            </div>
        </div>
        <div id="isNot3NFDiv" class="row checkNormalFormDiv" ng-if='!is3NFNormalized'>
            <div class="col-md-4">
                <img src="images/icon_cross.png" width="64" height="64">
            </div>
            <div class="col-md-8">
                <h3><b>3NF</b></h3>
                <h4>The table is not in 3NF.</h4>
            </div>
        </div>
    </div>

    <div id="BCNF">
        <div id="isBCNFDiv" class="row checkNormalFormDiv" ng-if='isBCNFNormalized'>
            <div class="col-md-4">
                <img src="images/icon_tick.png" width="64" height="64">
            </div>
            <div class="col-md-8">
                <h3><b>BCNF</b></h3>
                <h4>The table is in BCNF</h4>
            </div>
        </div>
        <div id="isNotBCNFDiv" class="row checkNormalFormDiv" ng-if='!isBCNFNormalized'>
            <div class="col-md-4">
                <img src="images/icon_cross.png" width="64" height="64">
            </div>
            <div class="col-md-8">
                <h3><b>BCNF</b></h3>
                <h4>The table is not in BCNF.</h4>
            </div>
        </div>
    </div>

    <div>
        <h2 style="border-bottom: dotted 1px; padding-bottom: 5px;" >
            Show Steps 
            <label class="switch">
                <input type="checkbox" id="testNFSwitchButton">
                <div class="slider round"></div>
            </label>
        </h2>

        <div class="normalizedTablesDiv" id="testNFStepsDiv" style="display: none;">
            <h4>2NF</h4>
            <div class="normalizedTableAttributeSpan"> 
                <div ng-bind-html="steps2NF | unsafe"></div>
            </div>
            
            <h4>3NF</h4>
            <div class="normalizedTableAttributeSpan"> 
                <div ng-bind-html="steps3NF | unsafe"></div>
            </div>

            <h4>BCNF</h4>
            <div class="normalizedTableAttributeSpan"> 
                <div ng-bind-html="stepsBCNF | unsafe"></div>
            </div>
        </div>
    </div>
</div>