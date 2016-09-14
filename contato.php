<!DOCTYPE html>
<html lang="pt-br" dir="ltr">
    <head>
        <?php 
            include('inc/config.php'); 
            include('inc/functions.php');
            include('inc/meta-header.php'); 
        ?>
        <title>BDPI USP - Contato</title>
    </head>

    <body>
        <?php include_once("inc/analyticstracking.php") ?>
        <?php include('inc/navbar.php'); ?>
        <div class="uk-container uk-container-center uk-margin-large-top">
    
            <div class="uk-grid" data-uk-grid-margin>

                <div class="uk-width-medium-2-3">
                    <div class="uk-panel uk-panel-header">

                        <h3 class="uk-panel-title">Entre em contato</h3>

                        <form class="uk-form uk-form-stacked">

                            <div class="uk-form-row">
                                <label class="uk-form-label">Seu nome</label>
                                <div class="uk-form-controls">
                                    <input type="text" placeholder="" class="uk-width-1-1" disabled>
                                </div>
                            </div>

                            <div class="uk-form-row">
                                <label class="uk-form-label">Seu e-mail</label>
                                <div class="uk-form-controls">
                                    <input type="text" placeholder="" class="uk-width-1-1" disabled>
                                </div>
                            </div>

                            <div class="uk-form-row">
                                <label class="uk-form-label">Sua mensagem</label>
                                <div class="uk-form-controls">
                                    <textarea class="uk-width-1-1" id="form-h-t" cols="100" rows="9" disabled></textarea>
                                </div>
                            </div>

                            <div class="uk-form-row">
                                <div class="uk-form-controls">
                                    <button class="uk-button uk-button-primary" disabled>Enviar</button>
                                </div>
                            </div>

                        </form>

                    </div>
                </div>

                


                
                <div class="uk-width-medium-1-3">
                    <div class="uk-panel uk-panel-box uk-panel-box-secondary">
                        <h3 class="uk-panel-title">Contato</h3>
                        <p>
                            <strong>Departamento Técnico do Sistema Integrado de Bibliotecas da Universidade de São Paulo</strong>
                            <br>Rua da Biblioteca, S/N - Complexo Brasiliana
                            <br>05508-050 - Cidade Universitária, São Paulo, SP - Brasil
                        </p>
                        <p>
                            <a>atendimento@dt.sibi.usp.br</a>
                            <br>Tel: (0xx11) 2648-0948 e 3091-4439
                        </p>
                        <h3 class="uk-h4">Redes sociais</h3>
                        <p>
                            <a href="https://www.facebook.com/sibiusp/?fref=ts" class="uk-icon-button uk-icon-facebook"></a>
                            <a href="https://twitter.com/sibiusp" class="uk-icon-button uk-icon-twitter"></a>                           
                            <a href="https://github.com/sibiusp" class="uk-icon-button uk-icon-github"></a>
                        </p>
                    </div>
                </div>
            </div>
        
        
        
        <hr class="uk-grid-divider">
            
     
                        
<?php include('inc/footer.php'); ?>

        </div>
        
        
<?php include('inc/offcanvas.php'); ?>
            
        
    </body>
</html>