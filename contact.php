<!DOCTYPE html>
<html lang="pt-br" dir="ltr">
    <head>
        <?php
            require 'inc/config.php'; 
            require 'inc/meta-header.php';
        ?>
        <title>BDPI USP - <?php echo $t->gettext('Contato'); ?></title>
    </head>

    <body style="height: 100vh; min-height: 45em; position: relative;">
        <?php
        if (file_exists('inc/analyticstracking.php')) {
            include_once 'inc/analyticstracking.php';
        }
        ?>
        <?php require 'inc/navbar.php'; ?>
        <div class="uk-container uk-margin-large-top" style="position: relative; padding-bottom: 15em;">
            <?php
                if(isset($_POST["nome"]) && isset($_POST["email"]) && isset($_POST["mensagem"])){
                    $nome = $_POST["nome"];
                    $email = $_POST["email"];
                    $email_remetente = "Repositorio da Produção USP <repositorio@aguia.usp.br>";
                    $mensagem = $_POST["mensagem"];
                    $para = "alander.machado@aguia.usp.br";
                    $assunto = "Contato do site do Repositório";
                    $headers = "MIME-Version: 1.1\n";
                    $headers .= "Content-type: text/plain; charset=UTF-8\n";
                    $headers .= "From: $email_remetente\n";
                    $headers .= "Return-Path: $email_remetente\n";
                    $headers .= "Reply-To: $nome <$email>\n";
                    if(mail($para, $assunto, $mensagem, $headers)){
                        echo $t->gettext('A sua mensagem foi enviada com sucesso!');
                    } else{
                        echo $t->gettext('A sua mensagem não foi enviada! Tente novamente.');
                    }
                }
            ?>
            <div class="uk-grid" uk-grid>

                <div class="uk-width-2-3@m">
                    <div class="uk-panel uk-panel-header">

                        <h3 class="uk-panel-title">Entre em contato</h3>

                        <form class="uk-form uk-form-stacked" method="post" action="">

                            <div class="uk-form-row">
                                <label class="uk-form-label"><?php echo $t->gettext('Nome'); ?></label>
                                <div class="uk-form-controls">
                                    <input type="text" name="nome" placeholder="<?php echo $t->gettext('Digite o seu nome'); ?>" class="uk-input uk-width-1-1">
                                </div>
                            </div>

                            <div class="uk-form-row">
                                <label class="uk-form-label">E-mail</label>
                                <div class="uk-form-controls">
                                    <input type="email" name="email" placeholder="<?php echo $t->gettext('Digite o seu e-mail'); ?>" class="uk-input uk-width-1-1">
                                </div>
                            </div>

                            <div class="uk-form-row">
                                <label class="uk-form-label"><?php echo $t->gettext('Mensagem'); ?></label>
                                <div class="uk-form-controls">
                                    <textarea name="mensagem" placeholder="<?php echo $t->gettext('Digite a sua mensagem'); ?>" class="uk-textarea uk-width-1-1" id="form-h-t" cols="100" rows="9"></textarea>
                                </div>
                            </div>

                            <div class="uk-form-row">
                                <div class="uk-form-controls">
                                    <button class="uk-button uk-button-primary"><?php echo $t->gettext('Enviar'); ?></button>
                                </div>
                            </div>

                        </form>

                    </div>
                </div>

                <div class="uk-width-1-3@m">
                    <div class="uk-panel uk-panel-box uk-panel-box-secondary">
                        <h3 class="uk-panel-title"><?php echo $t->gettext('Contato'); ?></h3>
                        <p>
                            <strong><?php echo $t->gettext('Agência USP de Gestão da Informação Acadêmica'); ?></strong>
                            <br>Rua da Praça do Relógio, 109 - Bloco L  Térreo
                            <br>05508-050 - Cidade Universitária, São Paulo, SP - Brasil
                        </p>
                        <p>
                            <a>atendimento@aguia.usp.br</a>
                            <br>Tel: (0xx11) 2648-0948
                        </p>
                        <h3 class="uk-h4"><?php echo $t->gettext('Redes sociais'); ?></h3>
                        <p>
                            <a href="https://www.facebook.com/sibiusp/?fref=ts" uk-icon="icon: facebook" target="_blank" rel="noopener noreferrer"></a>
                            <a href="https://twitter.com/sibiusp" uk-icon="icon: twitter" target="_blank" rel="noopener noreferrer"></a>
                            <a href="https://github.com/sibiusp" uk-icon="icon: github" target="_blank" rel="noopener noreferrer"></a>
                        </p>
                    </div>
                </div>
            </div>

        <hr class="uk-grid-divider">

        </div>
        <?php require 'inc/footer.php'; ?>

<?php include('inc/offcanvas.php'); ?>


    </body>
</html>
