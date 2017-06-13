<!DOCTYPE html>
<html lang="en">
    <head>
        <title>Home</title>

        <meta name="viewport" content="width=device-width, initial-scale=1">

        <!-- Bootstrap -->
        <link rel="stylesheet" href="bower_components/bootstrap/3.3.7/css/bootstrap.min.css">
        
        <!-- Tagit for tags management -->
        <link href="bower_components/tag-it/css/jquery.tagit.css" rel="stylesheet" type="text/css">
        <link href="bower_components/tag-it/css/tagit.ui-zendesk.css" rel="stylesheet" type="text/css">

        <!-- App'own java scripts/css -->
        <link rel='stylesheet' href='css/style.css'/>

    </head>
    <body ng-app="normalizationApp">
        <div id="header" class="container-fluid">
            <div class="headerGriffithPortalLinkBar">
                <div style="width:60%; text-align:right; margin: auto;">
                    <a target="_blank" href="http://www.griffith.edu.au/intranet?src=hp&p=gnav">Griffith Portal</a>
                </div>
            </div>
            <img src="images/gu-header-logo.png" width="200" height="91" style="position:relative; float:left; left:100px;">
            <div class="headerBannerTitle">
                <h2>Normalization Tool</h2>
            </div>
        </div>
        <div id="mainContent">
            <div id="mainContentLeftColumn">
                <ul class="navMainContent">
                    <br/><br/>
                    <li id="editAttibutesLink">
                        <img src="images/nav_icon_edit.png" width="16" height="16">
                        <a href="#editAttibutes" data-toggle="tooltip" data-placement="right" title="Edit the table attributes!">Edit Attributes</a>
                    </li>
                    <li id="learningResourcesLink">
                        <img src="images/nav_icon_resources.png" width="16" height="16">
                        <a href="#resources" data-toggle="tooltip" data-placement="right" title="Check some learning resources about database!">Learning Resources</a>
                    </li>
                    <li id="loadExampleLink">
                        <img src="images/nav_icon_example.png" width="16" height="19">
                        <a href="#loadExample" data-toggle="tooltip" data-placement="right" title="Use this feature to load an example set of attributes and functional dependencies!">Load Example</a>
                    </li>
                    
                    <h3 class="subTitle">Functions</h3>
                    <li id="findMinimalCoverLink">
                        <img src="images/nav_icon_bulb.png" width="16" height="16">
                        <a href="#findMinimalCover" data-toggle="tooltip" data-placement="right" title="Find the minimal cover">Find a minimal cover</a>
                    </li>
                    <li id="findCandidateKeysLink">
                        <img src="images/nav_icon_bulb.png" width="16" height="16">
                        <a href="#findCandidateKeys" data-toggle="tooltip" data-placement="right" title="Find all candidate keys">Find all Candidate Keys</a>
                    </li>
                    <li id="checkNormalFormLink">
                        <img src="images/nav_icon_bulb.png" width="16" height="16">
                        <a href="#checkNormalForm" data-toggle="tooltip" data-placement="right" title="Check which normal form the table is in">Check normal form</a>
                    </li>
                    <li id="normalize2NFLink">
                        <img src="images/nav_icon_bulb.png" width="16" height="16">
                        <a href="#normalize2NF" data-toggle="tooltip" data-placement="right" title="Normalize this table to 2NF">Normalize to 2NF</a>
                    </li>
                    <li id="normalize3NFLink">
                        <img src="images/nav_icon_bulb.png" width="16" height="16">
                        <a href="#normalize3NF" data-toggle="tooltip" data-placement="right" title="Normalize this table to 3NF preserving FDs">Normalize to 3NF method 1</a>
                    </li>
                    
                    <li id="1NFTo3NFLink">
                        <img src="images/nav_icon_bulb.png" width="16" height="16">
                        <a href="#1NFTo3NF" data-toggle="tooltip" data-placement="right" title="Normalize to 3NF by removing partial and transitive dependencies">Normalize to 3NF method 2</a>
                    </li>
                    
                    <li id="normalizeBCNFLink">
                        <img src="images/nav_icon_bulb.png" width="16" height="16">
                        <a href="#normalizeBCNF" data-toggle="tooltip" data-placement="right" title="Normalize this table to BCNF">Normalize to BCNF</a>
                    </li>
                    
          
                    <!--
                    <h3 class="subTitle">Contact</h3>
                    <div class="stepSettings">
                        <img src="images/nav_icon_function.png" width="16" height="16">
                        <span class="navMainContentItemText">Show Steps</span>
                        <div class="onoffswitch">
                            <input type="checkbox" name="onoffswitch" class="onoffswitch-checkbox" id="myonoffswitch" checked>
                            <label class="onoffswitch-label" for="myonoffswitch">
                                <span class="onoffswitch-inner"></span>
                                <span class="onoffswitch-switch"></span>
                            </label>
                        </div>
                    </div> -->
                    
                </ul>
                
            </div>

            <?php include 'views/subEditAttributes.php' ?>
            <?php include 'views/subCheckNormalForm.php' ?>
            <?php include 'views/subFindMinimalCover.php' ?>
            <?php include 'views/subFindCandidateKeys.php' ?>
            <?php include 'views/subLearningResource.php' ?>
            <?php include 'views/subNormalize2NF.php' ?>
            <?php include 'views/subNormalize3NF.php' ?>
            <?php include 'views/subNormalizeBCNF.php' ?>
            <?php include 'views/sub1NFTo3NF.php' ?>

        </div>

        <div id="footer">
            <div style="color:#8F8F8F; font-size:12px; ">© ICT of Griffith University 2015</div>
        </div>
        
        <!-- The loading example take the full screen -->
        <?php include 'views/subLoadExample.php' ?>
        
		
        <script src="bower_components/jquery/dist/jquery.min.js" type="text/javascript" charset="utf-8"></script>
        <script src="bower_components/jquery/dist/jquery-ui.min.js" type="text/javascript" charset="utf-8"></script>
        <script src="bower_components/bootstrap/3.3.7/js/bootstrap.min.js"></script>
		<!-- Angular JS -->
        <script src="bower_components/angularjs/1.2.26/angular.js"></script>
        <script src="bower_components/angularjs/1.2.26/angular-sanitize.js"></script>
		
        <script src="bower_components/tag-it/js/tag-it.js" type="text/javascript" charset="utf-8"></script>
		
        <script src="js/main.js" type="text/javascript" charset="utf-8"></script>

    </body>

</html>