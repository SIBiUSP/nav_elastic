    <?php require_once('functions.php');?>

    <link rel="shortcut icon" href="<?=$url_base?>/inc/images/faviconUSP.ico" type="image/x-icon">

    <script src="<?=$url_base?>/inc/js/jquery.min.js"></script>
    <script src="<?=$url_base?>/inc/js/jquery-ui.js"></script>
    <link rel="stylesheet" href="<?=$url_base?>/inc/js/jquery-ui.css">
    <script src="<?=$url_base?>/inc/js/jquery.form-validator.min.js"></script>

    <!-- Uikit - Local -->
    <link rel="stylesheet" href="<?=$url_base?>/inc/uikit/css/uikit.min.css" />
    <script src="<?=$url_base?>/inc/uikit/js/uikit.min.js"></script>
    <script src="<?=$url_base?>/inc/uikit/js/uikit-icons.min.js"></script>

    <!-- USP Custom -->
    <link rel="stylesheet" href="<?=$url_base?>/inc/css/style.css">

    <meta name="viewport" content="width=device-width, initial-scale=1">

    <script src="https://js.hcaptcha.com/1/api.js" async defer></script>
    
    <?php if(!is_bdta()): ?>
        <!-- Perto Digital -->
        <script>
            !function
            e(){"complete"===document.readyState?window.setTimeout((function(){
            var
            e,n,t;e="https://cdn.pertoplugin.link/plugin/perto.js.gz",n=5e3,t=
            "https://pertocdn.pertoplugin.link/plugin/perto.js.gz",new
            Promise((function(o,i){var
            c=document.createElement("script");function
            r(){if(document.head.removeChild(c),t){var
            e=document.createElement("script");e.src=t,document.head.appendChild(
            e),e.onload=function(){o(!0)},e.onerror=function(){o(!1)}}else
            o(!1)}c.src=e,document.head.appendChild(c);var
            u=setTimeout((function(){r()}),n);c.onload=function(){clearTimeout(u)
            ,o(!0)},c.onerror=function(){clearTimeout(u),r()}}))}),2e3):
            window.setTimeout((function(){e()}),1e3)}();
        </script>
    <?php endif;?>
