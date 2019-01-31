<!DOCTYPE html>
<html lang="pt-br" dir="ltr">
    <head>
        <?php
            include('inc/config.php');
            include('inc/functions.php');
            include('inc/meta-header.php');

            foreach ($_GET['filter'] as $filter) {
              $filterArray[] = str_replace('"', '', $filter);
            }
            $queryKibana = implode(" AND ", $filterArray);

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
               <iframe src="<?php echo $url_base ?>/app/kibana#/dashboard/57ad5fd0-6f35-11e8-ab16-a9a332435f09?embed=true&_g=()&_a=(description:'',filters:!(),fullScreenMode:!f,options:(darkTheme:!f,hidePanelTitles:!f,useMargins:!t),panels:!((gridData:(h:10,i:'1',w:48,x:0,y:0),id:c604ca80-6f36-11e8-ab16-a9a332435f09,panelIndex:'1',type:visualization,version:'6.3.0'),(embeddableConfig:(vis:(legendOpen:!t)),gridData:(h:18,i:'2',w:48,x:0,y:10),id:'1e449b20-6fb0-11e8-bdaf-e3ac4a7b5382',panelIndex:'2',type:visualization,version:'6.3.0'),(embeddableConfig:(),gridData:(h:15,i:'3',w:24,x:0,y:28),id:a7a9a400-6fb0-11e8-bdaf-e3ac4a7b5382,panelIndex:'3',type:visualization,version:'6.3.0'),(embeddableConfig:(),gridData:(h:15,i:'4',w:24,x:24,y:28),id:e50d2010-6fb0-11e8-bdaf-e3ac4a7b5382,panelIndex:'4',type:visualization,version:'6.3.0'),(embeddableConfig:(),gridData:(h:21,i:'5',w:24,x:0,y:43),id:'61fd60d0-6fb1-11e8-bdaf-e3ac4a7b5382',panelIndex:'5',type:visualization,version:'6.3.0'),(embeddableConfig:(),gridData:(h:21,i:'6',w:24,x:24,y:43),id:'735efe60-6fb1-11e8-bdaf-e3ac4a7b5382',panelIndex:'6',type:visualization,version:'6.3.0'),(embeddableConfig:(),gridData:(h:28,i:'7',w:48,x:0,y:86),id:fdb78f00-6fb1-11e8-bdaf-e3ac4a7b5382,panelIndex:'7',type:visualization,version:'6.3.0'),(embeddableConfig:(),gridData:(h:21,i:'8',w:48,x:0,y:114),id:'72ff83d0-6fb2-11e8-bdaf-e3ac4a7b5382',panelIndex:'8',type:visualization,version:'6.3.0'),(embeddableConfig:(),gridData:(h:22,i:'9',w:24,x:0,y:175),id:'26e7e180-6fb3-11e8-bdaf-e3ac4a7b5382',panelIndex:'9',type:visualization,version:'6.3.0'),(embeddableConfig:(),gridData:(h:22,i:'10',w:24,x:24,y:175),id:'410c14a0-6fb3-11e8-bdaf-e3ac4a7b5382',panelIndex:'10',type:visualization,version:'6.3.0'),(embeddableConfig:(),gridData:(h:23,i:'11',w:48,x:0,y:197),id:fb0befa0-6fb4-11e8-bdaf-e3ac4a7b5382,panelIndex:'11',type:visualization,version:'6.3.0'),(embeddableConfig:(),gridData:(h:22,i:'12',w:48,x:0,y:220),id:'7b077490-6fb5-11e8-bdaf-e3ac4a7b5382',panelIndex:'12',type:visualization,version:'6.3.0'),(embeddableConfig:(),gridData:(h:15,i:'13',w:24,x:0,y:242),id:'071392b0-6fe4-11e8-bdaf-e3ac4a7b5382',panelIndex:'13',type:visualization,version:'6.3.0'),(embeddableConfig:(),gridData:(h:15,i:'14',w:24,x:24,y:242),id:a6971960-6fee-11e8-bdaf-e3ac4a7b5382,panelIndex:'14',type:visualization,version:'6.3.0'),(embeddableConfig:(),gridData:(h:22,i:'15',w:48,x:0,y:257),id:f393b250-72ff-11e8-bdaf-e3ac4a7b5382,panelIndex:'15',type:visualization,version:'6.3.0'),(embeddableConfig:(),gridData:(h:22,i:'16',w:48,x:0,y:294),id:'41857970-796e-11e8-bdaf-e3ac4a7b5382',panelIndex:'16',type:visualization,version:'6.3.0'),(embeddableConfig:(),gridData:(h:15,i:'17',w:48,x:0,y:279),id:'2cee64d0-7aaa-11e8-bdaf-e3ac4a7b5382',panelIndex:'17',type:visualization,version:'6.3.0'),(embeddableConfig:(),gridData:(h:21,i:'18',w:48,x:0,y:154),id:'5348e900-7ef3-11e8-bdaf-e3ac4a7b5382',panelIndex:'18',type:visualization,version:'6.3.0'),(embeddableConfig:(),gridData:(h:22,i:'19',w:48,x:0,y:64),id:'3db9a6f0-b6d0-11e8-bdaf-e3ac4a7b5382',panelIndex:'19',type:visualization,version:'6.3.0'),(embeddableConfig:(),gridData:(h:11,i:'20',w:24,x:0,y:316),id:'5f5eede0-f4a8-11e8-b12c-613bbfcc398b',panelIndex:'20',type:visualization,version:'6.5.1'),(embeddableConfig:(),gridData:(h:11,i:'21',w:24,x:24,y:316),id:'6ae72860-f6f3-11e8-b12c-613bbfcc398b',panelIndex:'21',type:visualization,version:'6.5.1'),(embeddableConfig:(),gridData:(h:19,i:'22',w:48,x:0,y:135),id:'70f3e590-257e-11e9-b12c-613bbfcc398b',panelIndex:'22',type:visualization,version:'6.5.1')),query:(language:kuery,query:'<?php echo $queryKibana ?>'),timeRestore:!f,title:'Dashboard+BDPI',viewMode:view)"?embed=true&_g=()" height="11500" width="100%"></iframe>
        </div>
        <?php include('inc/footer.php'); ?>

    <?php include('inc/offcanvas.php'); ?>

    </body>
</html>
