//$(document).ready(function()
//{
	function open_cookie_popup(){
	    var lastupd = '15/12/2022';
	    if (getCookie('CookiePolicy') == null || getCookie('CookiePolicy')!=lastupd){
	        $('body').append('<div id="cookiepolicy" class="amarelo uk-grid-column-small uk-grid-row-large uk-child-width-1-3@s uk-text-center" uk-grid><div class="uk-width-4-5@m"><div class="text">A ABCD USP - Agência de Bibliotecas e Coleções Digitais da Universidade de São Paulo utiliza <strong>cookies</strong>, a fim de obter estatísticas para aprimorar a experiência do usuário. A navegação no portal implica concordância com esse procedimento, em linha com a <strong><a href="/politicas.php">Política de Privacidade e Cookies</a></strong>.</div></div><div class="uk-width-1-5@m" style="padding: 1em;" align="center"><button type="button" class="uk-button uk-button-secondary" id="cookiepolicybutton"><a class="uk-text-bold uk-text-decoration-none uk-text-warning">Concordar</a></button></div></div>"');
		$('#cookiepolicybutton').click(function() {
		    setCookie('CookiePolicy',lastupd,365);
			$('#cookiepolicy').remove();
		});
	    }
	}

	function getCookie(name){
	    var value = '; ' + document.cookie;
	    var parts = value.split('; ' + name + '=');
	    if (parts.length == 2) return parts.pop().split(';').shift();
	}

	function setCookie(name, value, days){
            var expires = '';
	    if (days) {
	        var date = new Date();
		date.setTime(date.getTime() + (days*24*60*60*1000));
		expires = '; expires=' + date.toUTCString();
	    }
		document.cookie = name + '=' + (value || '') + expires + '; path=/';
	}
//});
open_cookie_popup();
