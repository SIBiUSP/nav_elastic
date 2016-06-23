<!DOCTYPE html>
<html>
    <head>
        <title>Dados de uso</title>
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
                
<iframe id="graph" src="http://<?php echo $_SERVER['HTTP_HOST']; ?>:5601/app/kibana#/visualize/create?embed=true&type=histogram&indexPattern=logstash-*&_g=(refreshInterval:(display:Off,pause:!f,value:0),time:(from:now-7d,mode:quick,to:now))&_a=(filters:!(),linked:!f,query:(query_string:(analyze_wildcard:!t,query:'*')),uiState:(),vis:(aggs:!((id:'1',params:(),schema:metric,type:count),(id:'2',params:(customInterval:'2h',extended_bounds:(),field:'@timestamp',interval:h,min_doc_count:1),schema:segment,type:date_histogram)),listeners:(),params:(addLegend:!t,addTimeMarker:!f,addTooltip:!t,defaultYExtents:!f,mode:stacked,scale:linear,setYExtents:!f,shareYAxis:!t,times:!(),yAxis:()),title:'New%20Visualization',type:histogram))" height="400" scrolling="no" frameborder="0" seamless="seamless"></iframe>
                

<iframe id="graph" src="http://<?php echo $_SERVER['HTTP_HOST']; ?>:5601/app/kibana#/visualize/create?embed=true&type=tile_map&indexPattern=logstash-*&_g=(refreshInterval:(display:Off,pause:!f,value:0),time:(from:now-7d,mode:quick,to:now))&_a=(filters:!(),linked:!f,query:(query_string:(analyze_wildcard:!t,query:'*')),uiState:(),vis:(aggs:!((id:'1',params:(),schema:metric,type:count),(id:'2',params:(autoPrecision:!t,field:geoip.location,precision:2),schema:segment,type:geohash_grid)),listeners:(),params:(addTooltip:!t,heatBlur:15,heatMaxZoom:16,heatMinOpacity:0.1,heatNormalizeData:!t,heatRadius:25,isDesaturated:!t,mapType:'Scaled%20Circle%20Markers',wms:(enabled:!f,options:(attribution:'Maps%20provided%20by%20USGS',format:image%2Fpng,layers:'0',styles:'',transparent:!t,version:'1.3.0'),url:'https:%2F%2Fbasemap.nationalmap.gov%2Farcgis%2Fservices%2FUSGSTopo%2FMapServer%2FWMSServer')),title:'New%20Visualization',type:tile_map))" height="400" scrolling="no" frameborder="0" seamless="seamless"></iframe>
                
                
<iframe id="graph" src="http://<?php echo $_SERVER['HTTP_HOST']; ?>:5601/app/kibana#/visualize/create?embed=true&type=histogram&indexPattern=logstash-*&_g=(refreshInterval:(display:Off,pause:!f,value:0),time:(from:now-7d,mode:quick,to:now))&_a=(filters:!(),linked:!f,query:(query_string:(analyze_wildcard:!t,query:'*')),uiState:(),vis:(aggs:!((id:'1',params:(),schema:metric,type:count),(id:'2',params:(field:geoip.country_name.raw,order:desc,orderBy:'1',size:50),schema:segment,type:terms)),listeners:(),params:(addLegend:!t,addTimeMarker:!f,addTooltip:!t,defaultYExtents:!f,mode:stacked,scale:linear,setYExtents:!f,shareYAxis:!t,times:!(),yAxis:()),title:'New%20Visualization',type:histogram))" height="400" scrolling="no" frameborder="0" seamless="seamless"></iframe>                
                

<iframe id="graph" src="http://<?php echo $_SERVER['HTTP_HOST']; ?>:5601/app/kibana#/visualize/create?embed=true&type=table&indexPattern=logstash-*&_g=(refreshInterval:(display:Off,pause:!f,value:0),time:(from:now-7d,mode:quick,to:now))&_a=(filters:!(),linked:!f,query:(query_string:(analyze_wildcard:!t,query:'*')),uiState:(),vis:(aggs:!((id:'1',params:(),schema:metric,type:count),(id:'2',params:(field:request.raw,order:desc,orderBy:'1',size:20),schema:bucket,type:terms)),listeners:(),params:(perPage:10,showMeticsAtAllLevels:!f,showPartialRows:!f),title:'New%20Visualization',type:table))" height="600" scrolling="no" frameborder="0" seamless="seamless"></iframe>                
 
          
                
              
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