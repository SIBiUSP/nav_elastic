<!DOCTYPE html>
<html lang="pt-br" dir="ltr">
    <head>
        <?php 
            include('inc/config.php'); 
            include('inc/functions.php');
            include('inc/meta-header.php'); 
        ?>         
        <title>BDPI USP - Dashboard</title>
    </head>

    <body>
        <?php
            if (file_exists("inc/analyticstracking.php")){
                include_once("inc/analyticstracking.php");
            }
        ?>

		<?php include('inc/navbar.php'); ?>
        <div class="uk-container uk-margin-large-top">
            <h1>Dashboard</h1>
            <iframe src="http://<?php echo $kibana; ?>:5601/app/kibana#/dashboard/50a6aea0-7dd0-11e7-a13b-f3030e4f028e?embed=true&_g=()&_a=(description:'',filters:!(),options:(darkTheme:!f),panels:!((col:7,id:e6405ed0-7dcf-11e7-a13b-f3030e4f028e,panelIndex:1,row:7,size_x:6,size_y:3,type:visualization),(col:1,id:db468c00-7dd1-11e7-a13b-f3030e4f028e,panelIndex:3,row:1,size_x:12,size_y:3,type:visualization),(col:1,id:'2dc2ebd0-7dd3-11e7-a829-39cc191835f6',panelIndex:4,row:4,size_x:12,size_y:3,type:visualization),(col:1,id:c9ff70c0-7dd0-11e7-a13b-f3030e4f028e,panelIndex:5,row:7,size_x:6,size_y:3,type:visualization),(col:1,id:'3baa3fa0-7de7-11e7-98a0-05834c1ed1a6',panelIndex:6,row:10,size_x:12,size_y:3,type:visualization),(col:1,id:'36cd84e0-7de9-11e7-98a0-05834c1ed1a6',panelIndex:7,row:18,size_x:12,size_y:7,type:visualization),(col:1,id:ab52e170-7df3-11e7-a795-efa721aa7d5f,panelIndex:8,row:25,size_x:12,size_y:5,type:visualization),(col:1,id:'17a7bad0-7df4-11e7-a795-efa721aa7d5f',panelIndex:9,row:30,size_x:12,size_y:5,type:visualization),(col:1,id:e7903790-7df4-11e7-a795-efa721aa7d5f,panelIndex:10,row:13,size_x:12,size_y:5,type:visualization)),query:(query_string:(analyze_wildcard:!t,query:'*')),timeRestore:!f,title:'Panorama+da+Unidade',uiState:(P-3:(vis:(defaultColors:('0+-+100':'rgb(0,104,55)'))),P-6:(vis:(defaultColors:('0+-+100':'rgb(0,104,55)'))),P-8:(vis:(params:(sort:(columnIndex:!n,direction:!n))))),viewMode:view)" height="4000" width="100%"></iframe>
            <?php include('inc/footer.php'); ?>

        </div>
        
    <?php include('inc/offcanvas.php'); ?>
        
    </body>
</html>




