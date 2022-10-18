<?php

class mapFilterItemGetProcessor extends modObjectGetProcessor
{
    public $objectType = 'mapFilterItem';
    public $classKey = 'mapFilterItem';
    public $languageTopics = ['mapfilter:manager'];
    //public $permission = 'view';


    /**
     * We doing special check of permission
     * because of our objects is not an instances of modAccessibleObject
     *
     * @return mixed
     */
    public function process()
    {
        if (!$this->checkPermissions()) {
            return $this->failure($this->modx->lexicon('access_denied'));
        }

        return parent::process();
    }

}

return 'mapFilterItemGetProcessor';