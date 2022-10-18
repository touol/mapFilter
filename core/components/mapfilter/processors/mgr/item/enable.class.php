<?php
include_once dirname(__FILE__) . '/update.class.php';
class mapFilterItemEnableProcessor extends mapFilterItemUpdateProcessor
{
    public function beforeSet()
    {
        $this->setProperty('active', true);
        return true;
    }
}
return 'mapFilterItemEnableProcessor';