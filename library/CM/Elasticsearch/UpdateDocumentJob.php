<?php

class CM_Elasticsearch_UpdateDocumentJob extends CM_Jobdistribution_Job_Abstract {

    protected function _execute(CM_Params $params) {
        $indexClassName = $params->getString('indexClassName');
        $id = $params->getString('id');
        $client = CM_Service_Manager::getInstance()->getElasticsearch()->getClient();

        /** @var CM_Elasticsearch_Type_Abstract $index */
        $index = new $indexClassName($client);

        $index->updateDocuments(array($id));
        $index->refreshIndex();
    }
}
