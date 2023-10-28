<?php
/**
 * @component     Plugin Visforms CG Secure
 * Version			: 3.0.0
 * @license https://www.gnu.org/licenses/gpl-3.0.html GNU/GPL
 * @copyright (c) 2023 ConseilGouz. All Rights Reserved.
 * @author ConseilGouz 
**/
namespace ConseilGouz\Plugin\Visforms\Cgsecure\Extension;
// No direct access.
defined('_JEXEC') or die();
use Joomla\CMS\Plugin\CMSPlugin;
use Joomla\CMS\Form\Form;
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use ConseilGouz\CGSecure\Helper\Cgipcheck;

class Cgsecure extends CMSPlugin
{
    public $myname='VisFormsCGSecure';
	public $mymessage='(visforms) : try to access forms...';
	public $errtype = 'w';	 // warning 
	public $cgsecure_params;
	public function __construct(&$subject, $config)
	{
		parent::__construct($subject, $config);
		$this->cgsecure_params = Cgipcheck::getParams();
		$prefixe = $_SERVER['SERVER_NAME'];
		$prefixe = substr(str_replace('www.','',$prefixe),0,2);
		$this->mymessage = $prefixe.$this->errtype.'-'.$this->mymessage;
	}
	// Check IP on prepare Forms
	function onVisformsFormPrepare($context,$form,$params)
	{
		Cgipcheck::check_ip($this,$this->myname);
	}
}
