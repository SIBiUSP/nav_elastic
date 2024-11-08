<?php
include_once("functions_admin.php");
include_once("../inc/config.php");
include_once("staffAdmins.php");
$path_unidades = "{$_SERVER['DOCUMENT_ROOT']}/inc/unidades.txt";

if(isset($_POST)){
	__htmlspecialchars($_POST);
}
if (!(isset($_SESSION['oauthuserdata']) && in_array($_SESSION['oauthuserdata']->{'loginUsuario'}, $staffAdmins ))){
	header ("location: $url_base");
	die('você não está logado ou não é um administrador do sistema');
}

if(!empty($_POST["sigla_unidade"]) && !empty($_POST["nome_unidade"])){
	echo "aqui <<<<<<";
	set_content($path_unidades, 'unidades');
}

if(!empty($_POST["deleta-unidade"])){
	remove_content($path_unidades,'unidades',$_POST["deleta-unidade"],'sigla_unidade');
}

$unidades = get_content($path_unidades, 'unidades');
?>
<!DOCTYPE html>
<html lang="pt-br" dir="ltr">
    <head>
            <?php
            //require_once '../inc/config.php';
            require_once '../inc/meta-header.php';
	            ?>
        <title><?=$branch?></title>
        <!-- Facebook Tags - START -->
        <!--<meta property="og:locale" content="pt_BR">-->
        <meta property="og:locale" content="<?=$locale?>">
        <meta property="og:url" content="<?=$url_base?>">
        <meta property="og:title" content="<?=$t->gettext($branch)?> - <?=$t->gettext('Gestão de Unidades')?>">
        <meta property="og:site_name" content="<?=$t->gettext($branch)?>">
        <meta property="og:description" content="<?=$t->gettext($branch_description)?>">
        <meta property="og:image" content="http://www.imagens.usp.br/wp-content/uploads/USP.jpg">
        <meta property="og:image:type" content="image/jpeg">
        <meta property="og:image:width" content="800">
        <meta property="og:image:height" content="600">
        <meta property="og:type" content="website">
	<!-- Facebook Tags - END -->

	<script>
		$('#modal-delete').on('show.bs.modal', function (event) {
			var button = $(event.relatedTarget)
				var name = button.data('unidade')
				console.log(name)
			var sigla = button.data('sigla_unidade')
			var modal = $(this)
			modal.find('.uk-modal-title').text('Excluir unidade ' + sigla + ' - ' + name)
			modal.find('#confirmacao').text('Deseja realmente excluir a unidade ' + sigla + ' - ' + name + '?')
	
		})
	</script>

    </head>

    <body style="height: auto; /*min-height: 45em;*/ position: relative;">
        <?php
        if (file_exists("inc/analyticstracking.php")) {
            include_once "inc/analyticstracking.php";
        }
        ?>
        <?php require '../inc/navbar.php'; ?>
	<div class="uk-container uk-margin" style="position: relative;">

	    <div class="uk-width-1-1@s uk-child-width-1-1@m uk-align-center" style="position: relative; margin-bottom:300px" uk-grid>
		<div class="uk-align-center">
			<h2 style="color:#1094ab">Unidades cadastradas</h2>
			<a class="uk-button uk-button-primary" href="#modal-insert" uk-toggle>Incluir Unidade</a>
			<a class="uk-button uk-button-primary uk-align-right" href="gerencia.php" uk-toggle>Voltar</a>
		</div>
		<div class="uk-card uk-card-default uk-card-body">
			<h3 class="uk-card-title">Unidades</h3>
			<table class="uk-table uk-table-striped">
			<?php
			    foreach($unidades as $unidade): ?>
				    <tr>
					<td>
						<?=$unidade["sigla_unidade"]?>
					</td>
					<td>
						<?=$unidade["nome_unidade"]?>
					</td>
					<td>
						<a type="button" uk-toggle="target: #modal-delete-<?=$unidade["sigla_unidade"]?>" data-sigla_unidade="<?=$unidade["sigla_unidade"]?>" data-unidade="<?=$unidade["nome_unidade"]?>" uk-tooltip="Excluir" title="" aria-expanded="false">
							<img class="register-icons" data-src="<?=$url_base?>/inc/images/excluir.svg" alt="Excluir" uk-img="" src="<?=$url_base?>/inc/images/excluir.svg" style="height:24px;">
						</a>
					</td>
				    </tr>
			    <div id="modal-delete-<?=$unidade["sigla_unidade"]?>" uk-modal>
                	                <div class="uk-modal-dialog">
        	                                <button class="uk-modal-close-default" type="button" uk-close></button>
	                                        <div class="uk-modal-header">
                                                	<h2 class="uk-modal-title">Excluir Unidade</h2>
                                        	</div>
                                	        <div class="uk-modal-body">
							<p id="confirmacao">Tem certeza que quer excluir a unidade <?=$unidade["sigla_unidade"]?> - <?=$unidade["nome_unidade"]?>?</p>
                	                                <form action="" method="post">
        	                                        <input type="hidden" name="deleta-unidade" value="<?=$unidade["sigla_unidade"]?>" />
	                                        </div>
                                        	<div class="uk-modal-footer uk-text-right">
                                	                <button class="uk-button uk-button-default uk-modal-close" type="button">Cancelar</button>
                        	                        <button class="uk-button uk-button-danger" name="btn_submit">Excluir</button>
                	                        </div>
        	                                </form>
	                                </div>
                        	</div>

			    <?php endforeach;?>
					</table>
				 </div>

		<div id="modal-insert" uk-modal>
		    <div class="uk-modal-dialog">

			<button class="uk-modal-close-default" type="button" uk-close></button>
                        <div class="uk-modal-header">
                        	<h2 class="uk-modal-title">Incluir unidade</h2>
                        </div>

			<div class="uk-modal-body">
				<form  class="uk-form-stacked" action="" method="post">
					<div class="uk-margin">
                            			<label hidden class="uk-form-label" for="form-stacked-text">Sigla da unidade</label>
                            			<div class="uk-form-controls uk-margin uk-search uk-search-default" style="width: 100%">
                                                	<input class="uk-input" id="form-stacked-text-name" type="text" placeholder="Digite a sigla da unidade" name="sigla_unidade">
                            			</div>
					</div>

					<div class="uk-margin">
                                        	<label hidden class="uk-form-label" for="form-stacked-text">Nome</label>
                                        	<div class="uk-form-controls uk-margin uk-search uk-search-default" style="width: 100%">
							<input class="uk-input" id="form-stacked-text-name" type="text" placeholder="Digite o nome da unidade" name="nome_unidade">
						</div>
	                                </div>				
                        </div>

                        <div class="uk-modal-footer uk-text-right">
                        	<button class="uk-button uk-button-default uk-modal-close" type="button">Cancelar</button>
                        	<button class="uk-button uk-button-primary" name="btn_submit">Incluir</button>
                        </div>
				</form>
			
		    </div>
		</div>

            </div>

</div>
	        <?php require_once('../inc/footer.php'); ?>

    </body>
</html>
