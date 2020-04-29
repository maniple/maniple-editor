<?php

class ManipleEditor_Form_Decorator_EditorHeadScripts extends Zend_Form_Decorator_Abstract
{
    const className = __CLASS__;

    /**
     * @param string $content
     * @return string
     * @throws Zend_Form_Decorator_Exception
     */
    public function render($content)
    {
        $element = $this->getElement();

        /** @var Zefram_View_Abstract $view */
        $view = $element->getView();
        if (!$view) {
            throw new Zend_Form_Decorator_Exception('Element does not have a view');
        }

        $view->headScript()->appendFile($view->baseUrl('bower_components/tinymce/tinymce.min.js'));
        $view->headScript()->appendFile($view->assetUrl('editor.js', 'maniple-editor'));

        return $content;
    }
}
