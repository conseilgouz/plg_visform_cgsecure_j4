<?php
/**
 * @component     Plugin Visforms CG Secure
 * Version			: 3.1.0
 * @license https://www.gnu.org/licenses/gpl-3.0.html GNU/GPL
 * @copyright (c) 2025 ConseilGouz. All Rights Reserved.
 * @author ConseilGouz
**/

namespace ConseilGouz\Plugin\Visforms\Cgsecure\Extension;

// No direct access.
defined('_JEXEC') or die();
use Joomla\CMS\Plugin\CMSPlugin;
use Joomla\Event\SubscriberInterface;
use ConseilGouz\CGSecure\Helper\Cgipcheck;
use Visolutions\Component\Visforms\Site\Event\Visforms\VisformsFormPrepareEvent;

class Cgsecure extends CMSPlugin implements SubscriberInterface
{
    public $myname = 'VisFormsCGSecure';
    public $mymessage = '(visforms) : try to access forms...';
    public $errtype = 'w';	 // warning
    public $cgsecure_params;
    public function __construct(&$subject, $config)
    {
        parent::__construct($subject, $config);
        $this->cgsecure_params = Cgipcheck::getParams();
        $prefixe = $_SERVER['SERVER_NAME'];
        $prefixe = substr(str_replace('www.', '', $prefixe), 0, 2);
        $this->mymessage = $prefixe.$this->errtype.'-'.$this->mymessage;
    }
    public static function getSubscribedEvents(): array
    {
        return [
            'onVisformsFormPrepare' => 'onVisformsFormPrepare',
        ];
    }
    // Check IP on prepare Forms
    public function onVisformsFormPrepare(VisformsFormPrepareEvent $event): void
    {
        Cgipcheck::check_ip($this, $this->myname);
    }
}
