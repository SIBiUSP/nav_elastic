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
            <iframe src="http://<?php echo $kibana; ?>:5601/app/kibana#/dashboard/50a6aea0-7dd0-11e7-a13b-f3030e4f028e?embed=true&_g=()&_a=(description:'',filters:!(),options:(darkTheme:!f),panels:!((col:7,id:e6405ed0-7dcf-11e7-a13b-f3030e4f028e,panelIndex:1,row:7,size_x:6,size_y:3,type:visualization),(col:1,id:db468c00-7dd1-11e7-a13b-f3030e4f028e,panelIndex:3,row:1,size_x:12,size_y:3,type:visualization),(col:1,id:'2dc2ebd0-7dd3-11e7-a829-39cc191835f6',panelIndex:4,row:4,size_x:12,size_y:3,type:visualization),(col:1,id:c9ff70c0-7dd0-11e7-a13b-f3030e4f028e,panelIndex:5,row:7,size_x:6,size_y:3,type:visualization),(col:1,id:'3baa3fa0-7de7-11e7-98a0-05834c1ed1a6',panelIndex:6,row:10,size_x:12,size_y:3,type:visualization),(col:1,id:'36cd84e0-7de9-11e7-98a0-05834c1ed1a6',panelIndex:7,row:42,size_x:12,size_y:7,type:visualization),(col:1,id:ab52e170-7df3-11e7-a795-efa721aa7d5f,panelIndex:8,row:49,size_x:12,size_y:5,type:visualization),(col:1,id:'17a7bad0-7df4-11e7-a795-efa721aa7d5f',panelIndex:9,row:54,size_x:12,size_y:5,type:visualization),(col:1,id:e7903790-7df4-11e7-a795-efa721aa7d5f,panelIndex:10,row:37,size_x:12,size_y:5,type:visualization),(col:1,id:'385dca10-81d4-11e7-987a-0fff09323176',panelIndex:11,row:13,size_x:12,size_y:5,type:visualization),(col:1,id:'581c3430-81d5-11e7-987a-0fff09323176',panelIndex:12,row:18,size_x:12,size_y:5,type:visualization),(col:1,id:f3e06480-8285-11e7-8b10-3d31de956e05,panelIndex:13,row:59,size_x:12,size_y:6,type:visualization),(col:1,id:'3744f980-a904-11e7-92c4-a3f3afbc7051',panelIndex:14,row:65,size_x:6,size_y:7,type:visualization),(col:7,id:'517a2720-a905-11e7-92c4-a3f3afbc7051',panelIndex:15,row:65,size_x:6,size_y:7,type:visualization),(col:1,id:d9d81d10-a901-11e7-92c4-a3f3afbc7051,panelIndex:16,row:23,size_x:12,size_y:7,type:visualization),(col:1,id:'0b4e46d0-aa7e-11e7-9fc3-5912ca344fb9',panelIndex:17,row:72,size_x:6,size_y:8,type:visualization),(col:7,id:bdf1e4f0-aa7d-11e7-9fc3-5912ca344fb9,panelIndex:18,row:72,size_x:6,size_y:8,type:visualization),(col:1,id:'11a25710-a902-11e7-92c4-a3f3afbc7051',panelIndex:19,row:30,size_x:12,size_y:7,type:visualization)),query:(query_string:(analyze_wildcard:!t,query:'*')),timeRestore:!f,title:'Panorama+da+Unidade',uiState:(P-10:(vis:(params:(sort:(columnIndex:!n,direction:!n)))),P-12:(vis:(colors:('Quantidade+de+artigos':%23052B51))),P-14:(vis:(params:(sort:(columnIndex:!n,direction:!n)))),P-15:(vis:(params:(sort:(columnIndex:!n,direction:!n)))),P-17:(vis:(params:(sort:(columnIndex:!n,direction:!n)))),P-18:(vis:(params:(sort:(columnIndex:!n,direction:!n)))),P-3:(vis:(defaultColors:('0+-+100':'rgb(0,104,55)'))),P-6:(vis:(defaultColors:('0+-+100':'rgb(0,104,55)'))),P-8:(vis:(params:(sort:(columnIndex:!n,direction:!n)))),P-9:(vis:(params:(sort:(columnIndex:!n,direction:!n))))),viewMode:view)" height="8500" width="100%"></iframe>
            <?php include('inc/footer.php'); ?>

        </div>
        
    <?php include('inc/offcanvas.php'); ?>
        
    </body>
</html>




