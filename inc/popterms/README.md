# popterms
PopTerms is a simple web application that allows to use controlled vocabularies managed by TemaTres and integrate it with any web form or web application.  PopTerms do not requires TemaTres, You dont need to have your own TemaTres installation. You can use any vocabulary available in TemaTres.  PopTerms use the terminological web services provided by TemaTres. There are more than 400 vocabularies http://www.vocabularyserver.com/vocabularies/. You can browse, search and select terms and use them in your tool or system. 

#Config PopTerms
To config PopTerms you need to edit the config file config.ws.php.

##Config the following options:

The sources of terminological webs services provided by any instance of TemaTres. You can define many sources. There are no limited number of sources. To add source, add occurrence to the array $CFG_VOCABS. The first source is the default source ($CFG_VOCABS[1]).

    Example:
    $CFG_VOCABS[1]["URL_BASE"]="http://vocabularyserver.com/unesco/en/services.php";
    $CFG_VOCABS[2]["URL_BASE"]="http://r020.com.ar/tematres/demo/services.php"; :
    Characters to use in alphabetical navigation. You need to define one list to each source.

    Example:
    $CFG_VOCABS[1]["URL_BASE"]="http://vocabularyserver.com/tee/en/services.php";
    $CFG_VOCABS[1]["ALPHA"]=array("A","B","C","D","E","F","G","H","I","J","K","L","M","N","O","P","Q","R","S","T","U","V","W","X","Y","Z");
    Character to use for concatenate terms in the form. This character will be used in all the terminological sources.

    Example:
    $_PARAMS["_STRING_SEPARATOR"]=';

#How to use PopTerms in my web tool?
Code for the web form

First step: include in the header the javascript function to create popups

                    
                    <script>
                    function creaPopup(url)
                    {
                      tesauro=window.open(url, 
                      "Tesauro", 
                      "directories=no, menubar =no,status=no,toolbar=no,location=no,scrollbars=yes,fullscreen=no,height=600,width=450,left=500,top=0"
                      )
                    }
                    </script>
                    
                

Second step: Include link to your PopTerms
The link must to have the following GET values:

    v : URL of the terminological web services provided by TemaTres
    f : DOM identify of the form
    t : DOM identify of the form tag
    loadConfig : instruction to reload values and params, 1 (true) is de default value


                  <a href="#" onclick="creaPopup('index.php?t=cdu&f=forma1&v=http://vocabularyserver.com/tee/en/services.php&loadConfig=1'); return false;">Abrir vocabulario controlado</a>
                
                

