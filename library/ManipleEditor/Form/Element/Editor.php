<?php

/**
 * TinyMCE rich text editor
 *
 * @method Zefram_View_Abstract getView()
 */
class ManipleEditor_Form_Element_Editor extends Zend_Form_Element_Textarea
{
    protected $_tinyMce = array(
        'height'    => 140,
        'plugins'   => array(
            'link'       => true,
            'autolink'   => true,
            'autoresize' => true,
        ),
        'menubar'   => false,
        'statusbar' => false,
        'toolbar'   => 'bold italic underline strikethrough | subscript superscript | link blockquote | undo redo',
        'convert_urls' => false,
        // 'content_css' => '',
        'formats'  => array(
            'underline'     => array('inline' => 'u', 'exact' => true),
            'strikethrough' => array('inline' => 's', 'exact' => true),
        ),
    );

    /**
     * @param string|array $name
     * @param array $options
     * @throws Zend_Form_Exception
     */
    public function __construct($name, array $options = array())
    {
        if (is_array($name)) {
            $options = $name;
        } else {
            $options['name'] = (string) $name;
        }

        $this->addFilter(new ManipleEditor_Filter_HtmlPurifier());
        $this->setAttrib('cols', '80');
        $this->setAttrib('rows', '10');
        $this->setAttrib('data-element', 'maniple-editor');
        $this->setAttrib('data-tinymce', new ManipleEditor_Utils_ToStringWrapper(array($this, 'getTinyMceJson')));

        parent::__construct($options);
    }

    /**
     * Sets TinyMCE options
     *
     * @param array $options
     * @return $this
     */
    public function setTinyMce(array $options)
    {
        if (isset($options['plugins'])) {
            $options['plugins'] = $this->_normalizeTinyMcePlugins($options['plugins']);
        }

        $this->_tinyMce = Zefram_Stdlib_ArrayUtils::merge($this->_tinyMce, $options);
        return $this;
    }

    /**
     * Gets TinyMCE options
     *
     * @return array
     */
    public function getTinyMce()
    {
        return $this->_tinyMce;
    }

    /**
     * @param array|string $plugins
     * @return array
     */
    protected function _normalizeTinyMcePlugins($plugins)
    {
        $normalizedPlugins = array();

        if (is_string($plugins)) {
            $plugins = array_map('trim', explode(' ', $plugins));
            foreach ($plugins as $plugin) {
                $normalizedPlugins[$plugin] = true;
            }
        } else {
            $plugins = (array) $plugins;
            foreach ($plugins as $plugin => $enabled) {
                if (is_int($plugin)) {
                    $plugin = $enabled;
                    $enabled = true;
                }
                if (is_string($plugin) && $enabled) {
                    $normalizedPlugins[$plugin] = true;
                }
            }
        }

        return $normalizedPlugins;
    }

    /**
     * @return string
     * @throws Zend_View_Exception
     * @internal
     */
    public function getTinyMceJson()
    {
        $options = $this->getTinyMce();

        // convert enabled plugins to space-separated list
        $options['plugins'] = implode(' ', array_keys(array_filter($options['plugins'])));

        if (empty($options['language']) && ($view = $this->getView())) {
            $locale = (string) $view->translate()->getLocale();
            if ($locale && $locale !== 'en' && $locale !== 'en_US' && preg_match('/^[_a-zA-Z]+$/', $locale)) {
                $options['language'] = $locale;
                $options['language_url'] = $view->baseUrl('bower_components/tinymce-langs/' . $locale . '.js');
            }
        }

        return Zefram_Json::encode($options);
    }

    /**
     * @param array $options
     * @return $this
     */
    public function setHtmlPurifier(array $options)
    {
        /** @var ManipleEditor_Filter_HtmlPurifier $filter */
        $filter = $this->getFilter(ManipleEditor_Filter_HtmlPurifier::className);
        $filter->setConfig($options);
        return $this;
    }

    /**
     * @param Zend_Filter_Interface $filter
     * @return $this
     */
    public function prependFilter(Zend_Filter_Interface $filter)
    {
        $this->_filters = array(get_class($filter) => $filter) + $this->_filters;
        return $this;
    }

    public function setView(Zend_View_Interface $view = null)
    {
        if ($view) {
            $view->headScript()->appendFile($view->baseUrl('bower_components/tinymce/tinymce.min.js'));
            $view->headScript()->appendFile($view->assetUrl('editor.js', 'maniple-editor'));
        }
        return parent::setView($view);
    }
}
