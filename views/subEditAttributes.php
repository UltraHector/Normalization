<div id="subEditAttributes" class="mainContentRightColumn">
    <section class="mainContentAttributes">
        <h2 style="width:400px; border-bottom: dotted 1px;" data-toggle="tooltip" data-placement="right" title="These are the atributes in your table.">Attributes in Table</h2>
        <img src="images/icon_alert.jpg" with="16" height="16" ><i> Separate attributes using a comma (  ,  )</i>
        <br/>
        <textarea style="margin-top:5px;" id="attributesTextArea" rows="4" cols="85" name="attributes" placeholder="Please enter attributes to use within your table">a, b, c, d</textarea>
    </section>
    <br/>

    <div id="mainContentFunctionalDependency">
        <h2 style="width:400px; border-bottom: dotted 1px;" data-toggle="tooltip" data-placement="right" title="Enter the functional dependencies that are present in your table.">Functional Dependencies</h2>
        <br/><br/><br/>
        <div class="dynamicInput row" id="functionDependency_0">
            <div class="leftDependencyInputDivClass col-md-4">
                <input type="text" id="leftFunctionDependencyInput_functionDependency_0" autocomplete="on"/>
            </div>
            <div class="col-md-1">
                <img src="images/icon_arrow_right.png"  style="border-bottom: dotted 1px;" data-toggle="tooltip" title="Value on the right is dependent on the value on the left !">
            </div>
            <div class="rightDependencyInputDivClass col-md-4">
                <input type="text" id="rightFunctionDependencyInput_functionDependency_0"/>
            </div>
            <input type="button" class="functionalDependencyButtonClass col-md-1" value="Delete" onClick="deleteInput('functionDependency_0')" />
            <br/>
        </div>
        <div class="dynamicInput row" id="functionDependency_1">
            <div class="leftDependencyInputDivClass col-md-4">
                <input type="text" id="leftFunctionDependencyInput_functionDependency_1" autocomplete="on"/>
            </div>
            <div class="col-md-1">
                <img src="images/icon_arrow_right.png">
            </div>
            <div class="rightDependencyInputDivClass col-md-4">
                <input type="text" id="rightFunctionDependencyInput_functionDependency_1"/>
            </div>
            <input type="button" class="functionalDependencyButtonClass col-md-1" value="Delete" onClick="deleteInput('functionDependency_1')" />
            <br/>
        </div>
        <div class="dynamicInput row" id="functionDependency_2">
            <div class="leftDependencyInputDivClass col-md-4">
                <input type="text" id="leftFunctionDependencyInput_functionDependency_2" autocomplete="on"/>
            </div>
            <div class="col-md-1">
                <img src="images/icon_arrow_right.png">
            </div>
            <div class="rightDependencyInputDivClass col-md-4">
                <input type="text" id="rightFunctionDependencyInput_functionDependency_2"/>
            </div>
            <input type="button" class="functionalDependencyButtonClass col-md-1" value="Delete" onClick="deleteInput('functionDependency_2')" />
            <br/>
        </div>
    </div>
    <input type="button" class="functionalDependencyButtonClass" value="Add another dependency" onClick="addInput('mainContentFunctionalDependency');">
</div>