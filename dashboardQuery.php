<!DOCTYPE html>
<html lang="pt-br" dir="ltr">
    <head>
        <?php
            include('inc/config.php');
            include('inc/meta-header.php');

            foreach ($_GET['filter'] as $filter) {
              $filterArray[] = str_replace('"', '', $filter);
            }
            $queryKibana = implode(" AND ", $filterArray);

        ?>
        <title>BDPI USP - Dashboard</title>
    </head>

    <body style="height: 100vh; min-height: 45em; position: relative;">
        <?php
            if (file_exists("inc/analyticstracking.php")){
                include_once("inc/analyticstracking.php");
            }
        ?>
		<?php include('inc/navbar.php'); ?>
        <div class="uk-container uk-margin-large-top" style="position: relative; padding-bottom: 15em;>
            <h1>Dashboard</h1>
               <iframe src="<?php echo $url_base ?>/app/kibana#/dashboard/<?php echo $dashboardHash; ?>?embed=true&_g=()&_a=(description:'',filters:!(),fullScreenMode:!f,options:(darkTheme:!f,hidePanelTitles:!f,useMargins:!t),panels:!((embeddableConfig:(),gridData:(h:7,i:'1',w:48,x:0,y:9),id:a302c510-313b-11e9-ae1f-69d493aa6a26,panelIndex:'1',type:visualization,version:'6.6.0'),(embeddableConfig:(vis:(legendOpen:!t)),gridData:(h:20,i:'2',w:48,x:0,y:16),id:'2ca488d0-313c-11e9-ae1f-69d493aa6a26',panelIndex:'2',type:visualization,version:'6.6.0'),(embeddableConfig:(),gridData:(h:22,i:'3',w:24,x:0,y:36),id:'338f34a0-313d-11e9-ae1f-69d493aa6a26',panelIndex:'3',type:visualization,version:'6.6.0'),(embeddableConfig:(),gridData:(h:22,i:'4',w:24,x:24,y:36),id:'85d5faf0-313d-11e9-ae1f-69d493aa6a26',panelIndex:'4',type:visualization,version:'6.6.0'),(embeddableConfig:(),gridData:(h:19,i:'5',w:24,x:0,y:58),id:'4247b5c0-313e-11e9-ae1f-69d493aa6a26',panelIndex:'5',type:visualization,version:'6.6.0'),(embeddableConfig:(),gridData:(h:19,i:'6',w:24,x:24,y:58),id:'5d88f1a0-313e-11e9-ae1f-69d493aa6a26',panelIndex:'6',type:visualization,version:'6.6.0'),(embeddableConfig:(),gridData:(h:21,i:'7',w:24,x:0,y:77),id:'5cf372f0-313f-11e9-ae1f-69d493aa6a26',panelIndex:'7',type:visualization,version:'6.6.0'),(embeddableConfig:(),gridData:(h:21,i:'8',w:24,x:24,y:77),id:'993b45d0-313f-11e9-ae1f-69d493aa6a26',panelIndex:'8',type:visualization,version:'6.6.0'),(embeddableConfig:(),gridData:(h:9,i:'9',w:24,x:0,y:0),id:'2527bce0-3140-11e9-ae1f-69d493aa6a26',panelIndex:'9',type:visualization,version:'6.6.0'),(embeddableConfig:(),gridData:(h:9,i:'10',w:24,x:24,y:0),id:'39969a70-3140-11e9-ae1f-69d493aa6a26',panelIndex:'10',type:visualization,version:'6.6.0'),(embeddableConfig:(),gridData:(h:27,i:'11',w:48,x:0,y:98),id:'3a335a30-3141-11e9-ae1f-69d493aa6a26',panelIndex:'11',type:visualization,version:'6.6.0'),(embeddableConfig:(),gridData:(h:20,i:'12',w:48,x:0,y:125),id:'81eaa6e0-3375-11e9-ae1f-69d493aa6a26',panelIndex:'12',type:visualization,version:'6.6.0'),(embeddableConfig:(),gridData:(h:24,i:'13',w:24,x:0,y:145),id:f4660e30-3375-11e9-ae1f-69d493aa6a26,panelIndex:'13',type:visualization,version:'6.6.0'),(embeddableConfig:(),gridData:(h:24,i:'14',w:24,x:24,y:145),id:'1a39e4b0-3376-11e9-ae1f-69d493aa6a26',panelIndex:'14',type:visualization,version:'6.6.0')),query:(language:lucene,query:'<?php echo $queryKibana ?>'),timeRestore:!f,title:'Dashboard+BDPI',viewMode:view)" height="11500" width="100%"></iframe>
        </div>

        <?php echo $queryKibana ?>
        <?php include('inc/footer.php'); ?>

    <?php include('inc/offcanvas.php'); ?>

    </body>
</html>
