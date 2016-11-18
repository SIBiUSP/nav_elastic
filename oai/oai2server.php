<?php
require_once('oai2exception.php');
require_once('oai2xml.php');

/**
 * This is an implementation of OAI Data Provider version 2.0.
 * @see http://www.openarchives.org/OAI/2.0/openarchivesprotocol.htm
 */
class OAI2Server {

    public $errors = array();
    private $args = array();
    private $verb = '';
    private $token_prefix = '/tmp/oai_pmh-';
    private $token_valid = 86400;

    function __construct($uri, $args, $identifyResponse, $callbacks) {

        $this->uri = $uri;

        if (!isset($args['verb']) || empty($args['verb'])) {
            $this->errors[] = new OAI2Exception('badVerb');
        } else {
            $verbs = array('Identify', 'ListMetadataFormats', 'ListSets', 'ListIdentifiers', 'ListRecords', 'GetRecord');
            if (in_array($args['verb'], $verbs)) {

                $this->verb = $args['verb'];

                unset($args['verb']);

                $this->args = $args;

                $this->identifyResponse = $identifyResponse;

                $this->listMetadataFormatsCallback = $callbacks['ListMetadataFormats'];
                $this->listSetsCallback = $callbacks['ListSets'];
                $this->listRecordsCallback = $callbacks['ListRecords'];
                $this->getRecordCallback = $callbacks['GetRecord'];

                $this->response = new OAI2XMLResponse($this->uri, $this->verb, $this->args);

                call_user_func(array($this, $this->verb));

            } else {
                $this->errors[] = new OAI2Exception('badVerb');
            }
        }

    }

    public function response() {
        if (empty($this->errors)) {
            return $this->response->doc;
        } else {
            $errorResponse = new OAI2XMLResponse($this->uri, $this->verb, $this->args);
            $oai_node = $errorResponse->doc->documentElement;
            foreach($this->errors as $e) {
                $node = $errorResponse->addChild($oai_node,"error",$e->getMessage());
                $node->setAttribute("code",$e->getOAI2Code());
            }
            return $errorResponse->doc;
        }
    }

    public function Identify() {

        if (count($this->args) > 0) {
            foreach($this->args as $key => $val) {
                $this->errors[] = new OAI2Exception('badArgument');
            }
        } else {
            foreach($this->identifyResponse as $key => $val) {
                $this->response->addToVerbNode($key, $val);
            }
        }
    }

    public function ListMetadataFormats() {

        foreach ($this->args as $argument => $value) {
            if ($argument != 'identifier') {
                $this->errors[] = new OAI2Exception('badArgument');
            }
        }
        if (isset($this->args['identifier'])) {
            $identifier = $this->args['identifier'];
        } else {
            $identifier = '';
        }
        if (empty($this->errors)) {
            try {
                if ($formats = call_user_func($this->listMetadataFormatsCallback, $identifier)) {
                    foreach($formats as $key => $val) {
                        $cmf = $this->response->addToVerbNode("metadataFormat");
                        $this->response->addChild($cmf,'metadataPrefix',$key);
                        $this->response->addChild($cmf,'schema',$val['schema']);
                        $this->response->addChild($cmf,'metadataNamespace',$val['metadataNamespace']);
                    }
                } else {
                    $this->errors[] = new OAI2Exception('noMetadataFormats');
                }
            } catch (OAI2Exception $e) {
                $this->errors[] = $e;
            }
        }
    }

    public function ListSets() {

        if (isset($this->args['resumptionToken'])) {
            if (count($this->args) > 1) {
                $this->errors[] = new OAI2Exception('badArgument');
            } else {
                if ((int)$val+$this->token_valid < time()) {
                    $this->errors[] = new OAI2Exception('badResumptionToken');
                }
            }
            $resumptionToken = $this->args['resumptionToken'];
        } else {
            $resumptionToken = null;
        }
        if (empty($this->errors)) {
            if ($sets = call_user_func($this->listSetsCallback, $resumptionToken)) {

                foreach($sets as $set) {

                    $setNode = $this->response->addToVerbNode("set");

                    foreach($set as $key => $val) {
                        if($key=='setDescription') {
                            $desNode = $this->response->addChild($setNode,$key);
                            $des = $this->response->doc->createDocumentFragment();
                            $des->appendXML($val);
                            $desNode->appendChild($des);
                        } else {
                            $this->response->addChild($setNode,$key,$val);
                        }
                    }
                }
            } else {
                $this->errors[] = new OAI2Exception('noSetHierarchy');
            }
        }
    }

    public function GetRecord() {

        if (!isset($this->args['metadataPrefix'])) {
            $this->errors[] = new OAI2Exception('badArgument');
        } else {
            $metadataFormats = call_user_func($this->listMetadataFormatsCallback);
            if (!isset($metadataFormats[$this->args['metadataPrefix']])) {
                $this->errors[] = new OAI2Exception('cannotDisseminateFormat');
            }
        }
        if (!isset($this->args['identifier'])) {
            $this->errors[] = new OAI2Exception('badArgument');
        }

        if (empty($this->errors)) {
            try {
                if ($record = call_user_func($this->getRecordCallback, $this->args['identifier'], $this->args['metadataPrefix'])) {

                    $identifier = $record['identifier'];

                    $datestamp = $this->formatDatestamp($record['datestamp']);

                    $set = $record['set'];

                    $status_deleted = (isset($record['deleted']) && ($record['deleted'] == 'true') &&
                                       (($this->identifyResponse['deletedRecord'] == 'transient') ||
                                        ($this->identifyResponse['deletedRecord'] == 'persistent')));

                    $cur_record = $this->response->addToVerbNode('record');
                    $cur_header = $this->response->createHeader($identifier, $datestamp, $set, $cur_record);
                    if ($status_deleted) {
                        $cur_header->setAttribute("status","deleted");
                    } else {
                        $this->add_metadata($cur_record, $record);
                    }
                } else {
                    $this->errors[] = new OAI2Exception('idDoesNotExist');
                }
            } catch (OAI2Exception $e) {
                $this->errors[] = $e;
            }
        }
    }

    public function ListIdentifiers() {
        $this->ListRecords();
    }

    public function ListRecords() {

        $maxItems = 50;
        $deliveredRecords = 0;
        if (!empty($this->args['metadataPrefix'])){
            $metadataPrefix = $this->args['metadataPrefix'];
        } else {
            $metadataPrefix = 'oai_dc';
        }
        
        $from = isset($this->args['from']) ? $this->args['from'] : '';
        $until = isset($this->args['until']) ? $this->args['until'] : '';
        $set = isset($this->args['set']) ? $this->args['set'] : '';
        
        
        if (isset($this->args['resumptionToken'])) {  
//            if (count($this->args['resumptionToken']) > 1) {                
//                $this->errors[] = new OAI2Exception('badArgument');
//            } else {
//                if ((int)$val+$this->token_valid < time()) {
//                    $this->errors[] = new OAI2Exception('badResumptionToken');
//                } else {
//                    if (!file_exists($this->token_prefix.$this->args['resumptionToken'])) {
//                        $this->errors[] = new OAI2Exception('badResumptionToken');
//                    } else {
//                        if (
                            $readings = $this->readResumptionToken($this->token_prefix.$this->args['resumptionToken']);
                            list($deliveredRecords, $metadataPrefix, $from, $until, $set) = $readings;
//                        } else {
//                            $this->errors[] = new OAI2Exception('badResumptionToken');
//                        }
//                    }
//                }
//            }
        } else {
            if (!isset($this->args['metadataPrefix'])) {
                $this->errors[] = new OAI2Exception('badArgument');
            } else {
                $metadataFormats = call_user_func($this->listMetadataFormatsCallback);
                if (!isset($metadataFormats[$this->args['metadataPrefix']])) {
                    $this->errors[] = new OAI2Exception('cannotDisseminateFormat');
                }
            }
            if (isset($this->args['from'])) {
                if(!$this->checkDateFormat($this->args['from'])) {
                    $this->errors[] = new OAI2Exception('badArgument');
                }
            }
            if (isset($this->args['until'])) {
                if(!$this->checkDateFormat($this->args['until'])) {
                    $this->errors[] = new OAI2Exception('badArgument');
                }
            }
        }

        if (empty($this->errors)) {
            try {

                $records_count = call_user_func($this->listRecordsCallback, $metadataPrefix, $from, $until, $set, true);

                $records = call_user_func($this->listRecordsCallback, $metadataPrefix, $from, $until, $set, false, $deliveredRecords, $maxItems);

                foreach ($records as $record) {

                    $identifier = $record['identifier'];
                    $datestamp = $this->formatDatestamp($record['datestamp']);
                    $setspec = $record['set'];

                    $status_deleted = (isset($record['deleted']) && ($record['deleted'] === true) &&
                                        (($this->identifyResponse['deletedRecord'] == 'transient') ||
                                         ($this->identifyResponse['deletedRecord'] == 'persistent')));

                    if($this->verb == 'ListRecords') {
                        $cur_record = $this->response->addToVerbNode('record');
                        $cur_header = $this->response->createHeader($identifier, $datestamp,$setspec,$cur_record);
                        if (!$status_deleted) {
                            $this->add_metadata($cur_record, $record);
                        }	
                    } else { // for ListIdentifiers, only identifiers will be returned.
                        $cur_header = $this->response->createHeader($identifier, $datestamp,$setspec);
                    }
                    if ($status_deleted) {
                        $cur_header->setAttribute("status","deleted");
                    }
                }

                // Will we need a new ResumptionToken?
                if ($records_count - $deliveredRecords > $maxItems) {

                    $deliveredRecords +=  $maxItems;
                    $restoken = $this->createResumptionToken($deliveredRecords,$from,$until,$set);

                    $expirationDatetime = gmstrftime('%Y-%m-%dT%TZ', time()+$this->token_valid);	

                } elseif (isset($args['resumptionToken'])) {
                    // Last delivery, return empty ResumptionToken
                    $restoken = null;
                    $expirationDatetime = null;
                }

                if (isset($restoken)) {
                    $this->response->createResumptionToken($restoken,$expirationDatetime,$records_count,$deliveredRecords);
                }

            } catch (OAI2Exception $e) {
                $this->errors[] = $e;
            }
        }
    }

    private function add_metadata($cur_record, $record) {

        $meta_node =  $this->response->addChild($cur_record ,"metadata");

        $schema_node = $this->response->addChild($meta_node, $record['metadata']['container_name']);
        foreach ($record['metadata']['container_attributes'] as $name => $value) {
            $schema_node->setAttribute($name, $value);
        }
        foreach ($record['metadata']['fields'] as $name => $value) {
            $pattern = '/_(\d)/i';
            $replacement = '';
            $name = preg_replace($pattern, $replacement, $name);
            $this->response->addChild($schema_node, $name, $value);
        }
    }

    private function createResumptionToken($delivered_records,$from,$until,$set) {

        list($usec, $sec) = explode(" ", microtime());
        $token = ((int)($usec*1000) + (int)($sec*1000));

        $fp = fopen ($this->token_prefix.$token, 'w');
        if($fp==false) {
            exit("Cannot write. Writer permission needs to be changed.");
        }
        
        global $metadataPrefix;        
        
        fputs($fp, "$delivered_records#");
        fputs($fp, "$metadataPrefix#");
        fputs($fp, "$from#");
        fputs($fp, "$until#");
        fputs($fp, "$set#");
        fclose($fp);
        return $token;
    }

    private function readResumptionToken($resumptionToken) {
        $rtVal = false;
        $fp = fopen($resumptionToken, 'r');
        if ($fp != false) {
            $filetext = fgets($fp, 255);
            $textparts = explode('#', $filetext);
            fclose($fp);
            unlink($resumptionToken);
            $rtVal = array_values($textparts);
        }
        return $rtVal;
        
    }

    /**
     * All datestamps used in this system are GMT even
     * return value from database has no TZ information
     */
    private function formatDatestamp($datestamp) {
        return date("Y-m-d\TH:i:s\Z",strtotime($datestamp));
    }

    /**
     * The database uses datastamp without time-zone information.
     * It needs to clean all time-zone informaion from time string and reformat it
     */
    private function checkDateFormat($date) {
        $date = str_replace(array("T","Z")," ",$date);
        $time_val = strtotime($date);
        if(!$time_val) return false;
        if(strstr($date,":")) {
            return date("Y-m-d H:i:s",$time_val);
        } else {
            return date("Y-m-d",$time_val);
        }
    }
}
