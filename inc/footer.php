<div class="uk-container uk-margin" style="position: absolute; bottom: 0px; left: 0px; right: 0px; max-width: initial; background: #f8f8f8; margin-bottom: 0px; padding-bottom: 1em;">
	<div class="uk-grid" style="width: 100vw; max-width: 1200px; margin: 0 auto;">
    <div class="uk-width-1-2@m uk-width-1-2@s">
	    <p id="logos-rodape"class="uk-text-small uk-text-left uk-text-left@m">
	    	<a href="https://www.usp.br" target="_blank" rel="noopener noreferrer" style="font-family: arial; font-size:2.5em; font-weight: bold; line-height: 2.5em; color: #123e72">
    			<img src="<?=$url_base?>/inc/images/usp_90anos_azul.png" style="width: 5em;">
    			<!--<img src="<?=$url_base?>/inc/images/usp-logo-png.png" style="width: 2.5em;">-->
    		</a>
    		<!--<strong>Universidade de São Paulo</strong>-->
		<a href="<?=$institution_site?>" target="_blank" rel="noopener noreferrer" style="font-family: arial; font-size:2.5em; font-weight: bold; line-height: 2.5em; color: #123e72">
    			<img src="<?=$url_base?>/inc/images/ABCD_mini.png" style="height: 1.2em;">
    		</a>
	    </p>
	</div>
	<div class="uk-width-1-2@m uk-width-1-2@s uk-align-center@s uk-padding-small">
		<div id="sub-menu" class="uk-navbar-container" uk-navbar>
		    <div class="uk-navbar-right">
		        <ul class="uk-navbar-nav">
		            <li>
	                        <a class="uk-link-muted" href="<?=$url_base?>/politicas.php"><?=$t->gettext('Política de Privacidade')?></a>
	                    </li>
		            
                            <li>
	                        <a class="uk-link-muted" href="<?=$url_base?>/advanced_search.php"><?=$t->gettext('Busca técnica')?></a>
	                    </li>

	                    <li>
	                        <a href="<?=$url_base?>/contact.php"><?=$t->gettext('Contato')?></a>
	                    </li>
		        </ul>
		    </div>
		</div>


	</div>
	<div class="uk-text-center uk-width-1-1 uk-text-center">
	    <p class="uk-text-small ">
	    	<?=$t->gettext($branch_footer)?> &nbsp;&nbsp;&nbsp; 2012 - <?=date("Y")?>
	    </p>
	</div>
</div>
</div>
<script src="<?=$url_base?>/inc/js/politica.js">
<script>
  $.validate({
      lang : 'pt',
      modules : 'sanitize',
      modules : 'file'
  });  
</script>
