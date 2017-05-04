<?php 

class processaResultados {
    
    /* Recupera os exemplares do DEDALUS */
    static function load_itens_new ($sysno) {
        $xml = simplexml_load_file('http://dedalus.usp.br/X?op=item-data&base=USP01&doc_number='.$sysno.'');
        if ($xml->error == "No associated items"){

        } else {


            echo '<div id="exemplares'.$sysno.'" class="uk-text-small">';
            echo "<table class=\"uk-table uk-table-responsive uk-table-striped\">
                        <caption>Exemplares físicos disponíveis nas Bibliotecas da USP</caption>
                        <thead>
                          <tr>
                            <th><small>Biblioteca</small></th>
                            <th><small>Status</small></th>
                            <th><small>Localização</small></th>";
                            if ($xml->item->{'loan-status'} == "A"){
                            echo "<th><small>Disponibilidade</small></th>
                            <th><small>Data provável de devolução</small></th>";
                          } else {
                            echo "<th><small>Disponibilidade</small></th>";
                          }
                          echo "</tr>
                        </thead>
                      <tbody>";
              foreach ($xml->item as $item) {
                echo '<tr>';
                echo '<td><small>'.$item->{'sub-library'}.'</small></td>';
                if ($item->{'item-status'} == "10"){
                    echo '<td><small>Circula</small></td>';
                } else {
                    echo '<td><small>Não circula</small></td>';
                }  
                echo '<td><small>'.$item->{'call-no-1'}.'</small></td>';
                if ($item->{'loan-status'} == "A"){
                echo '<td><small>Emprestado</small></td>';
                echo '<td><small>'.$item->{'loan-due-date'}.'</small></td>';
              } else {
                echo '<td><small>Disponível</small></td>';
              }
                echo '</tr>';
              }
              echo "</tbody></table></div>";
              }
              flush();
      }
    


    static function get_fulltext_file($id,$session){
        $files_upload = glob('upload/'.$id[0].'/'.$id[1].'/'.$id[2].'/'.$id[3].'/'.$id[4].'/'.$id[5].'/'.$id[6].'/'.$id[7].'/'.$id.'/*.{pdf,pptx}', GLOB_BRACE);    
        $links_upload = "";
        if (!empty($files_upload)){       
            foreach($files_upload as $file) {
                $delete = "";    
                if (!empty($session)){
                    $delete = '<form method="POST" action="single.php?_id='.$id.'">
                                   <input name="delete_file" value="'.$file.'"  type="hidden">
                                   <button class="uk-close uk-close-alt uk-alert-danger" alt="Deletar arquivo"></button>
                               </form>';
                }

                if( strpos( $file, '.pdf' ) !== false ) {
                    $links_upload[] = '<div class="uk-width-1-4@m"><div class="uk-panel"><a onclick="_gaq.push([\'_trackEvent\',\'Download\',\'PDF\',this.href]);" href="'.$file.'" target="_blank"><img src="inc/images/pdf.png"  height="70" width="70"></img></a>'.$delete.'</div></div>';
                } else {
                    $links_upload[] = '<div class="uk-width-1-4@m"><div class="uk-panel"><a onclick="_gaq.push([\'_trackEvent\',\'Download\',\'PDF\',this.href]);" href="'.$file.'" target="_blank"><img src="inc/images/pptx.png"  height="70" width="70"></img></a>'.$delete.'</div></div>';
                }
            }
        }
        return $links_upload;
    }
    
    
}

class USP {

    /* Consulta o Vocabulário Controlado da USP */
    static function consultar_vcusp($termo) {
        echo '<h4>Vocabulário Controlado do SIBiUSP</h4>';
        $xml = simplexml_load_file('http://vocab.sibi.usp.br/pt-br/services.php?task=fetch&arg='.$termo.'');

        if ($xml->{'resume'}->{'cant_result'} != 0) {

            $termo_xml = simplexml_load_file('http://vocab.sibi.usp.br/pt-br/services.php?task=fetchUp&arg='.$xml->{'result'}->{'term'}->{'term_id'}[0].'');
            foreach (($termo_xml->{'result'}->{'term'}) as $string_up) {
                $string_up_array[] = '<a href="result.php?search[]=subject.keyword:&quot;'.$string_up->{'string'}.'&quot;">'.$string_up->{'string'}.'</a>';    
            };
            echo 'Você também pode pesquisar pelos termos mais genéricos: ';
            print_r(implode(" -> ",$string_up_array));
            echo '<br/>';
            $termo_xml_down = simplexml_load_file('http://vocab.sibi.usp.br/pt-br/services.php?task=fetchDown&arg='.$xml->{'result'}->{'term'}->{'term_id'}[0].'');
            if (!empty($termo_xml_down->{'result'}->{'term'})){
                foreach (($termo_xml_down->{'result'}->{'term'}) as $string_down) {
                    $string_down_array[] = '<a href="result.php?search[]=subject.keyword:&quot;'.$string_down->{'string'}.'&quot;">'.$string_down->{'string'}.'</a>';     
                };
                echo 'Ou pesquisar pelo assuntos mais específicos: ';
                print_r(implode(" - ",$string_down_array));            
            }


        } else {
            $termo_naocorrigido[] = $termo_limpo;
        }
    }      
    
}

?>