<?php
if (!empty($_GET['unidade'])) {
    $key = "unidadeUSPtrabalhos";
    $value = $_GET['unidade'];   
} elseif (!empty($_GET['departamento'])) {
    $key = "departamento";
    $value = $_GET['unidade'];    
} else {
    
}
?>
<!DOCTYPE html>
<html>
    <head>
        <title>Relatório de Unidade USP</title>
        <?php include('inc/meta-header.php'); ?>

        <style>
            #graph {
                width: 100%;
            }        
        </style>    
        
    </head>
    <body>
        <?php include('inc/barrausp.php'); ?>
        <div class="ui main container">
            <?php include('inc/header.php'); ?>
            <?php include('inc/navbar.php'); ?>
            <div id="main">
                <h1>Relatório de registros com a participação da Unidade USP: <?php echo $_GET['unidade']; ?></h1>
                <div class="ui vertical segment">
                    <h3>Totais</h3>
                    <iframe id="graph" src="http://<?php echo $_SERVER['HTTP_HOST']; ?>:5601/app/kibana#/visualize/create?embed=true&type=metric&indexPattern=sibi&_g=(refreshInterval:(display:Off,pause:!f,value:0),time:(from:now-30m,mode:quick,to:now))&_a=(filters:!(),linked:!f,query:(query_string:(analyze_wildcard:!t,query:<?php echo $key; ?>%3D<?php echo $value; ?>)),uiState:(),vis:(aggs:!((id:'1',params:(customLabel:registros),schema:metric,type:count),(id:'2',params:(customLabel:'autores%20USP',field:authorUSP),schema:metric,type:cardinality),(id:'3',params:(customLabel:'unidades%20USP',field:unidadeUSPtrabalhos),schema:metric,type:cardinality),(id:'4',params:(customLabel:pa%C3%ADses,field:country),schema:metric,type:cardinality)),listeners:(),params:(fontSize:60,handleNoResults:!t),title:'New%20Visualization',type:metric))" height="400" scrolling="no" frameborder="0" seamless="seamless"></iframe>
                </div>
                <div class="ui vertical segment">
                    <h3>Por Unidade USP</h3>
                    <iframe id="graph" src="http://<?php echo $_SERVER['HTTP_HOST']; ?>:5601/app/kibana#/visualize/create?embed=true&type=histogram&indexPattern=sibi&_g=(refreshInterval:(display:Off,pause:!f,value:0),time:(from:now-30m,mode:quick,to:now))&_a=(filters:!(),linked:!f,query:(query_string:(analyze_wildcard:!t,query:<?php echo $key; ?>%3D<?php echo $value; ?>)),uiState:(),vis:(aggs:!((id:'1',params:(),schema:metric,type:count),(id:'2',params:(field:unidadeUSPtrabalhos,order:asc,orderBy:'1',size:100),schema:segment,type:terms)),listeners:(),params:(addLegend:!t,addTimeMarker:!f,addTooltip:!t,defaultYExtents:!f,mode:stacked,scale:linear,setYExtents:!f,shareYAxis:!t,times:!(),yAxis:()),title:'New%20Visualization',type:histogram))" height="400" scrolling="no" frameborder="0" seamless="seamless"></iframe>
                </div>                
                <div class="ui vertical segment">
                    <h3>Por ano</h3>
                    <iframe id="graph" src="http://<?php echo $_SERVER['HTTP_HOST']; ?>:5601/app/kibana#/visualize/create?embed=true&type=histogram&indexPattern=sibi&_g=(refreshInterval:(display:Off,pause:!f,value:0),time:(from:now-15m,mode:quick,to:now))&_a=(filters:!(),linked:!f,query:(query_string:(analyze_wildcard:!t,query:<?php echo $key; ?>%3D<?php echo $value; ?>)),uiState:(),vis:(aggs:!((id:'1',params:(),schema:metric,type:count),(id:'2',params:(field:year,order:asc,orderBy:_term,size:100),schema:segment,type:terms)),listeners:(),params:(addLegend:!t,addTimeMarker:!f,addTooltip:!t,defaultYExtents:!f,mode:stacked,scale:linear,setYExtents:!f,shareYAxis:!t,times:!(),yAxis:()),title:'New%20Visualization',type:histogram))" height="400" scrolling="no" frameborder="0" seamless="seamless"></iframe>
                </div>
                
            </div>            
        </div>
        <?php include('inc/footer.php'); ?>
          
        <script type="text/javascript">
                   (function( $ ) {
                      $.fn.keepRatio = function(which) {
                          var $this = $(this);
                          var w = $this.width();
                          var h = $this.height();
                          var ratio = w/h;
                          $(window).resize(function() {
                              switch(which) {
                                  case 'width':
                                      var nh = $this.width() / ratio;
                                      $this.css('height', nh + 'px');
                                      break;
                                  case 'height':
                                      var nw = $this.height() * ratio;
                                      $this.css('width', nw + 'px');
                                      break;
                              }
                          });

                      }
                    })( jQuery );      

                    $(document).ready(function(){
                        $('#graph').keepRatio('width');
                    });
        
           </script>        

        
    </body>
</html>