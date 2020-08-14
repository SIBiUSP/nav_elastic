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
                if(isset($_POST["nome"]) && isset($_POST["email"]) && isset($_POST["mensagem"]) && isset($_POST["cidade"])){
                    $nome = input_sanitize($_POST["nome"]);
                    $email_cliente = input_sanitize($_POST["email"], true);
                    $cidade = input_sanitize($_POST["cidade"]);
                    $estado = input_sanitize($_POST["estado"]);
                    $pais = input_sanitize($_POST["pais"]);
                    $mensagem = input_sanitize($_POST["mensagem"]);
                    $mensagem_email = "Nome: {$nome}\n";
                    $mensagem_email .= "E-mail: {$email_cliente}\n";
                    $mensagem_email .= "Cidade/UF: {$cidade}/{$estado}\n";
                    $mensagem_email .= "País: {$pais}\n";
                    $mensagem_email .= "Mensagem:\n{$mensagem}\n";
                    $email_remetente = "Atendimento AGUIA <atendimento@aguia.usp.br>";
                    $email_destinatario = $email_remetente;
                    $assunto = "Contato do site do Repositório - $nome";
                    $headers = "MIME-Version: 1.1\n";
                    $headers .= "Content-type: text/plain; charset=UTF-8\n";
                    $headers .= "From: $email_remetente\n";
                    $headers .= "Return-Path: $email_destinatario\n";
                    $headers .= "Reply-To: $nome <$email_cliente>\n";
                    if(filter_var($email_cliente, FILTER_VALIDATE_EMAIL) && mail($email_destinatario, $assunto, $mensagem_email, $headers)){
                        $notification = $t->gettext('A sua mensagem foi enviada com sucesso!');
                        echo "<div class=\"uk-alert-success\" uk-alert>
                            <a class=\"uk-alert-close\" uk-close></a>
                            <p>{$notification}</p>
                        </div>";
                    } else{
                        $notification = $t->gettext('A sua mensagem não foi enviada! Tente novamente.');
                        echo "<div class=\"uk-alert-danger\" uk-alert>
                            <a class=\"uk-alert-close\" uk-close></a>
                            <p>{$notification}</p>
                        </div>";
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
                                <label class="uk-form-label"><?php echo $t->gettext('Cidade'); ?></label>
                                <div class="uk-width-1-2@s">
                                    <input class="uk-input" name="cidade" type="text" placeholder="<?php echo $t->gettext('Digite a sua cidade'); ?>">
                                </div>
                                <label class="uk-form-label"><?php echo $t->gettext('Estado'); ?></label>
                                <div class="uk-width-1-4@s">
                                    <input class="uk-input" name="estado" type="text" placeholder="<?php echo $t->gettext('Digite o seu estado'); ?>">
                                </div>
                                <label class="uk-form-label"><?php echo $t->gettext('País'); ?></label>
                                <div class="uk-width-1-4@s">
                                    <input class="uk-input" name="pais" type="text" placeholder="<?php echo $t->gettext('Digite o seu país'); ?>">
                                </div>
                            </div>

                            <div class="uk-form-row">
                                <label class="uk-form-label"><?php echo $t->gettext('Mensagem'); ?></label>
                                <div class="uk-form-controls">
                                    <textarea name="mensagem" placeholder="<?php echo $t->gettext('Digite a sua mensagem'); ?>" class="uk-textarea uk-width-1-1" id="form-h-t" cols="100" rows="9"></textarea>
                                </div>
                            </div>
			    <div class="uk-form-row">
			    <div class="g-recaptcha" data-sitekey="<?php echo $captcha_key; ?>"></div>
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
        <div style="position: relative; max-width: initial;">
            <?php require 'inc/footer.php'; ?>
        </div>

<?php include('inc/offcanvas.php'); ?>


    </body>
</html>
