<?php

class ManipleEditor_Bootstrap extends Maniple_Application_Module_Bootstrap
{
    public function getModuleDependencies()
    {
        return array();
    }

    public function getResourcesConfig()
    {
        return require __DIR__ . '/configs/resources.config.php';
    }

    public function getAutoloaderConfig()
    {
        return array(
            'Zend_Loader_StandardAutoloader' => array(
                'prefixes' => array(
                    'ManipleEditor_' => __DIR__ . '/library/ManipleEditor/',
                ),
            ),
        );
    }

    protected function _initZeframForm()
    {
        Zefram_Form::addDefaultPrefixPath(
            'ManipleEditor_Form_Element_',
            __DIR__ . '/library/ManipleEditor/Form/Element/',
            Zefram_Form::ELEMENT
        );
    }
}
