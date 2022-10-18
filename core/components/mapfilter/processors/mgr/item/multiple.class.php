<?php

class mapFilterMultipleProcessor extends modProcessor
{


    /**
     * @return array|string
     */
    public function process()
    {
        if (!$method = $this->getProperty('method', false)) {
            return $this->failure();
        }
        $ids = json_decode($this->getProperty('ids'), true);
        if (empty($ids)) {
            return $this->success();
        }

        /** @var mapFilter $mapFilter */
        $mapFilter = $this->modx->getService('mapFilter');
        foreach ($ids as $id) {
            /** @var modProcessorResponse $response */
            $response = $mapFilter->runProcessor('mgr/item/' . $method, array('id' => $id), array(
                'processors_path' => MODX_CORE_PATH . 'components/mapfilter/processors/mgr/'
            ));
            if ($response->isError()) {
                return $response->getResponse();
            }
        }

        return $this->success();
    }


}

return 'mapFilterMultipleProcessor';