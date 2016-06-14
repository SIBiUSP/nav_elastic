<html>
    <head>
        <title>Entre em contato</title>
        <?php include('inc/meta-header.php'); ?>
    </head>
    <body>
        <div class="ui main container">
            <?php include('inc/header.php'); ?>
            <?php include('inc/navbar.php'); ?>
            <div id="main">
                
                <div class="ui form">
                    <div class="required field">
                      <label>Nome completo</label>
                      <input placeholder="Full Name" type="text">
                    </div>
                      <div class="field">
                        <label>E-mail</label>
                        <input placeholder="joe@schmoe.com" type="email">
                      </div>
                      <div class="field">
                        <label>Mensagem</label>
                        <textarea></textarea>
                      </div>
                    <div class="ui submit button">Submit</div>
                </div>                
            </div>            
        </div>
        <?php include('inc/footer.php'); ?>
    </body>
</html>