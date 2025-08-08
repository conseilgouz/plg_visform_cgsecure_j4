<?php
/**
 * @component     Plugin Visforms CG Secure
 * @license https://www.gnu.org/licenses/gpl-3.0.html GNU/GPL
 * @copyright (c) 2025 ConseilGouz. All Rights Reserved.
 * @author ConseilGouz
**/

namespace ConseilGouz\Plugin\Visforms\Cgsecure\Extension;

// No direct access.
defined('_JEXEC') or die();
use Joomla\CMS\Plugin\CMSPlugin;
use Joomla\Event\SubscriberInterface;
use ConseilGouz\CGSecure\Cgipcheck;
use Visolutions\Component\Visforms\Site\Event\Visforms\VisformsFormPrepareEvent;
use Visolutions\Component\Visforms\Site\Event\Visforms\VisformsBeforeFormSaveEvent;

class Cgsecure extends CMSPlugin implements SubscriberInterface
{
    public $myname = 'VisFormsCGSecure';
    public $mymessage = '';
    public $errtype = 'w';	 // warning
    public $cgsecure_params;
    public function __construct(&$subject, $config)
    {
        parent::__construct($subject, $config);
        $this->autoloadLanguage = true;
        $this->cgsecure_params = Cgipcheck::getParams();
    }
    public static function getSubscribedEvents(): array
    {
        return [
            'onVisformsFormPrepare' => 'onVisformsFormPrepare',
            'onVisformsBeforeFormSave' => 'onVisformsBeforeFormSave',
        ];
    }
    // Check IP on prepare Forms
    public function onVisformsFormPrepare(VisformsFormPrepareEvent $event): void
    {
        $prefixe = $_SERVER['SERVER_NAME'];
        $prefixe = substr(str_replace('www.', '', $prefixe), 0, 2);
        $this->mymessage = '(visforms) : try to access forms...';
        $this->mymessage = $prefixe.$this->errtype.'-'.$this->mymessage;
        Cgipcheck::check_ip($this, $this->myname);
    }
    // Check message's anguage in Form
    // look for a textarea and check its language
    public function onVisformsBeforeFormSave(VisformsBeforeFormSaveEvent $event): void
    {
        if (!isset($this->cgsecure_params->checkcontact) ||
            ($this->cgsecure_params->checkcontact == 0)) {
            return;
        }
        $context = $event->getContext();
        $form = $event->getForm();
        $fields = $event->getFields();
        $this->loadLanguage('plg_visforms_cgsecure');
        $text = "";
        foreach ($fields as $field) {
            if ($field->typefield == "textarea") {
                $text .= $field->userInput;
            }
        }
        $prefixe = $_SERVER['SERVER_NAME'];
        $prefixe = substr(str_replace('www.', '', $prefixe), 0, 2);
        $this->mymessage = '(visforms) : wrong language...';
        $this->mymessage = $prefixe.$this->errtype.'-'.$this->mymessage;
        $ret = Cgipcheck::check_language($this, $form, $text);
        if ($ret) { // ok
            return ;
        }
        if (isset($this->cgsecure_params->contactaction)) {
            if ($this->cgsecure_params->contactaction == "spam") { // add spam in title
                $form->subject = '[---SPAM---]  '.$form->subject;
                // $event->updateData($data);
            } elseif ($this->cgsecure_params->contactaction == "block") { // display error message
                // @todo : Joomla 5 specific
                $event->stopPropagation();
            }
        }

    }
}
