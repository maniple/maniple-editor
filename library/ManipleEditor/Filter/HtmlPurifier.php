<?php

class ManipleEditor_Filter_HtmlPurifier implements Zend_Filter_Interface
{
    const className = __CLASS__;

    /**
     * @var HTMLPurifier
     */
    protected $_htmlPurifier;

    /**
     * @var array
     */
    protected $_config = array(
        'Core.RemoveProcessingInstructions' => true,
        'Cache.DefinitionImpl'              => null,
        'HTML.Allowed'                      => '
            a[href|target],
            strong,em,s,u,
            p,
            br,
            sub,sup,
            blockquote
        ',
        'AutoFormat.RemoveEmpty'            => true,
        'AutoFormat.AutoParagraph'          => true,
        'Attr.AllowedFrameTargets'          => array('_blank'),
    );

    /**
     * Whether to add target="_blank" to all links
     *
     * Its value is controlled by 'HTML.TargetBlankAll' config option
     *
     * @var bool
     */
    protected $_targetBlankAll = false;

    public function __construct(array $config = array())
    {
        $this->setConfig($config);
    }

    /**
     * @param array $config
     * @return $this
     */
    public function setConfig(array $config)
    {
        $this->_htmlPurifier = null;

        if (isset($config['HTML.TargetBlankAll'])) {
            $this->_targetBlankAll = (bool) $config['HTML.TargetBlankAll'];
        }
        unset($config['HTML.TargetBlankAll']);

        $this->_config = Zefram_Stdlib_ArrayUtils::merge($this->_config, $config);
        return $this;
    }

    /**
     * @return HTMLPurifier
     */
    public function getHtmlPurifier()
    {
        if (!$this->_htmlPurifier) {
            $this->_htmlPurifier = new HTMLPurifier(HTMLPurifier_HTML5Config::create($this->_config));
        }

        return $this->_htmlPurifier;
    }

    /**
     * @param string $value
     * @return string
     */
    public function filter($value)
    {
        $html = $this->_targetBlankAll
            ? preg_replace('/<a /', '<a target="_blank" ', $value)
            : $value;

        $html = $this->getHtmlPurifier()->purify($html);
        $html = preg_replace(
            sprintf('#<p>(\s+|&nbsp;|%s)</p>#i', html_entity_decode('&nbsp;', ENT_COMPAT, 'UTF-8')),
            '',
            $html
        );
        return $html;
    }
}
