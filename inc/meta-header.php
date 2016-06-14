<!-- Javascripts -->
<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.4/jquery.min.js"></script>
<script src="http://semantic-ui.com/dist/semantic.min.js"></script>

 <script>
  $(document)
    .ready(function() {

      // fix menu when passed
      $('.menuprincipal')
        .visibility({
          once: false,
          onBottomPassed: function() {
            $('.fixed.menu').transition('fade in');
          },
          onBottomPassedReverse: function() {
            $('.fixed.menu').transition('fade out');
          }
        })
      ;

      // create sidebar and attach to menu open
      $('.ui.sidebar')
        .sidebar('attach events', '.toc.item')
      ;

    })
  ;
  </script>

<!-- CSS -->
<link rel="stylesheet" type="text/css" href="http://semantic-ui.com/dist/semantic.min.css">
<link rel="stylesheet" type="text/css" href="inc/css/style_sibi.css">

<!-- Favicon -->
<link type="image/x-icon" href="inc/images/faviconUSP.ico" rel="icon" />
<link type="image/x-icon" href="inc/images/faviconUSP.ico" rel="shortcut icon" />