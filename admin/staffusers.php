<?php
include_once("functions_admin.php");
include_once("../inc/config.php");
include_once("staffAdmins.php");
if(isset($_POST)){
	__htmlspecialchars($_POST);
}
if (!(isset($_SESSION['oauthuserdata']) && in_array($_SESSION['oauthuserdata']->{'loginUsuario'}, $staffAdmins ))){
	header ("location: $url_base");
	die('você não está logado ou não é um administrador do sistema');
}

if(!empty($_POST["nusp"]) && !empty($_POST["nome"]) && !empty($_POST["unidade"])){
	set_user();
}

if(!empty($_POST["deleta-colaborador"])){
	remove_user($_POST["deleta-colaborador"]);
}

$users = get_users();

?>
<!DOCTYPE html>
<html lang="pt-br" dir="ltr">
    <head>
            <?php
            //require_once '../inc/config.php';
            require_once '../inc/meta-header.php';
	            ?>
        <title><?php echo $branch; ?></title>
        <!-- Facebook Tags - START -->
        <!--<meta property="og:locale" content="pt_BR">-->
        <meta property="og:locale" content="<?php echo $locale ?>">
        <meta property="og:url" content="<?php echo $url_base; ?>">
        <meta property="og:title" content="<?php echo $t->gettext(''.$branch.''); ?> - <?php echo $t->gettext('Página Principal'); ?>">
        <meta property="og:site_name" content="<?php echo $t->gettext(''.$branch.''); ?>">
        <meta property="og:description" content="<?php echo $t->gettext(''.$branch_description.''); ?>">
        <meta property="og:image" content="http://www.imagens.usp.br/wp-content/uploads/USP.jpg">
        <meta property="og:image:type" content="image/jpeg">
        <meta property="og:image:width" content="800">
        <meta property="og:image:height" content="600">
        <meta property="og:type" content="website">
	<!-- Facebook Tags - END -->

	<script>
		$('#modal-delete').on('show.bs.modal', function (event) {
			var button = $(event.relatedTarget)
				var name = button.data('colaborador')
				console.log(name)
			var unidade = button.data('unidade')
			var id = button.data('colaboradorid')
			var modal = $(this)
			modal.find('.uk-modal-title').text('Excluir colaborador ' + name + ' - ' + unidade)
			modal.find('#confirmacao').text('Deseja realmente excluir o colaborador ' + name + '- ' + unidade + '?')
	
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
			<h2 style="color:#1094ab">Colaboradores habilitados para fazer upload</h2>
			<a class="uk-button uk-button-primary" href="#modal-insert" uk-toggle>Incluir Colaborador</a>
		</div>

		<!--<div class="uk-margin-xlarge-bottom" style="padding-bottom: 200px">
		<table class="uk-table uk-table-striped uk-margin-xlarge-bottom">
    			<thead>
        			<tr>
			        	<th>Número USP</th>
			        	<th>Nome</th>
					<th>Unidade USP</th>
					<th>Excluir</th>
			        </tr>
			</thead>
			<tbody>-->
			<?php
			    $unidade_anterior = "";
			    foreach($users as $user): ?>
				<?php if($unidade_anterior != $user['unidade']):?>
					<?php if($unidade_anterior == ""):?>
        					<div class="uk-card uk-card-default uk-card-body">
							<h3 class="uk-card-title"><?php echo $user["unidade"];?></h3>
							<table class="uk-table uk-table-striped">
					<?php else: ?>
							</table>
                                        	</div>
						<div class="uk-card uk-card-default uk-card-body">
                                                        <h3 class="uk-card-title"><?php echo $user["unidade"];?></h3>
							<table class="uk-table uk-table-striped">
					<?php endif; ?>
				<?php endif; ?>
				    <tr>
					<td>
						<?php echo $user["nusp"];?>
					</td>
					<td>
						<?php echo $user["nome"];?>
					</td>
					<!--<td>
						<?php echo $user["unidade"];?>
					</td>-->
					<td>
						<a type="button" uk-toggle="target: #modal-delete-<?php echo $user["nusp"]; ?>" data-colaboradorid="<?php echo $user["nusp"];?>" data-colaborador="<?php echo $user["nome"];?>" data-unidade="<?php echo $user["unidade"];?>" uk-tooltip="Excluir" title="" aria-expanded="false">
							<img class="register-icons" data-src="<?php echo $url_base; ?>/inc/images/excluir.svg" alt="Excluir" uk-img="" src="<?php echo $url_base;?>/inc/images/excluir.svg" style="height:24px;">
						</a>
					</td>
				    </tr>
				<?php $unidade_anterior = $user["unidade"];?>
			    <div id="modal-delete-<?php echo $user["nusp"];?>" uk-modal>
                	                <div class="uk-modal-dialog">
        	                                <button class="uk-modal-close-default" type="button" uk-close></button>
	                                        <div class="uk-modal-header">
                                                	<h2 class="uk-modal-title">Excluir colaborador</h2>
                                        	</div>
                                	        <div class="uk-modal-body">
                        	                	<p id="confirmacao">Tem certeza que quer excluir o colaborador <?php echo $user["nome"] . " - " . $user["nusp"]; ?>?</p>
                	                                <form action="" method="post">
        	                                        <input type="hidden" name="deleta-colaborador" value="<?php echo $user["nusp"]; ?>" />
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
                        	<h2 class="uk-modal-title">Incluir colaborador</h2>
                        </div>

			<div class="uk-modal-body">
				<form  class="uk-form-stacked" action="" method="post">
				<!--<form  class="uk-form-stacked" action="<?php echo $url_base.'/admin/staffusers.php';?>" method="post">-->
					<div class="uk-margin">
                            			<label hidden class="uk-form-label" for="form-stacked-text">Número USP</label>
                            			<div class="uk-form-controls uk-margin uk-search uk-search-default" style="width: 100%">
                                                	<input class="uk-input" id="form-stacked-text-name" type="number" placeholder="Digite o número USP do colaborador" name="nusp">
                            			</div>
					</div>

					<div class="uk-margin">
                                        	<label hidden class="uk-form-label" for="form-stacked-text">Nome</label>
                                        	<div class="uk-form-controls uk-margin uk-search uk-search-default" style="width: 100%">
							<input class="uk-input" id="form-stacked-text-name" type="text" placeholder="Digite o nome do colaborador" name="nome">
						</div>
	                                </div>				

        	                	<div class="uk-margin">
                	            		<label hidden class="uk-form-label" for="form-stacked-select">Selecione a unidade USP onde o colaborador é vinculado</label>
                        	    		<div class="uk-form-controls">
							<select class="uk-select" id="form-stacked-select" name="unidade">
								<option value="<?php echo "AGUIA"; ?>" style="color:#333"><?php echo "Agência USP de Gestão da Informação Acadêmica (AGUIA)"; ?></option>
								<?php foreach($unidades as $key => $value ): ?>
									<?php if(!in_array($key, $schoolsManageUsersRemove)): ?>
                                        				<option value="<?php echo $key; ?>" style="color:#333"><?php echo $value; ?></option>
                                    				<?php endforeach; ?>
	                                		</select>
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

		<!--<div id="modal-delete" uk-modal>
			<div class="uk-modal-dialog">
				<button class="uk-modal-close-default" type="button" uk-close></button>
				<div class="uk-modal-header">
					<h2 class="uk-modal-title">Excluir colaborador</h2>
				</div>
				<div class="uk-modal-body">
					<p id="confirmacao">Tem certeza que quer excluir o colaborador?</p>
					<form action="" method="post">
						<input type="hidden" name="delete-colaborador" value="" />
				</div>
				
				<div class="uk-modal-footer uk-text-right">
					<button class="uk-button uk-button-default uk-modal-close" type="button">Cancelar</button>
					<button class="uk-button uk-button-danger" name="btn_submit">Excluir</button>
				</div>
				</form>
			</div>
		</div>-->


            </div>
	        <?php require_once('../inc/footer.php'); ?>

        </div>
    </body>
</html>
