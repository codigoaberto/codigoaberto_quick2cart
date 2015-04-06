<?php
/**
* @version 			SEBLOD 3.x More
* @package			SEBLOD (App Builder & CCK) // SEBLOD nano (Form Builder)
* @url				http://www.seblod.com
* @editor			Octopoos - www.octopoos.com
* @copyright		Copyright (C) 2013 SEBLOD. All Rights Reserved.
* @license 			GNU General Public License version 2 or later; see _LICENSE.php
**/

defined('_JEXEC') or die('Restricted access');

if (!defined('DS'))
{
	define('DS', '/');
}



jimport('joomla.plugin.plugin');
$lang = JFactory::getLanguage();
$lang->load('plg_content_content_quick2cart', JPATH_ADMINISTRATOR);
$path = JPATH_SITE.DS.'components'.DS.'com_quick2cart'.DS.'helper.php';
//if (! class_exists('comquick2cartHelper'))
//{
	// Require_once $path;
	JLoader::register('comquick2cartHelper', $path);
	JLoader::load('comquick2cartHelper');
//}
//jimport('joomla.plugin.plugin');
// Plugin
class plgCCK_FieldQuick2cart extends JCckPluginField
{
	protected static $type		=	'quick2cart';
	protected static $path;
	
	// -------- -------- -------- -------- -------- -------- -------- -------- // Construct
	
	// onCCK_FieldConstruct
	public function onCCK_FieldConstruct( $type, &$data = array() )
	{
		if ( self::$type != $type ) {
			return;
		}
		parent::g_onCCK_FieldConstruct( $data );
	}
	
	// -------- -------- -------- -------- -------- -------- -------- -------- // Prepare
	
	// onCCK_FieldPrepareContent
	public function onCCK_FieldPrepareContent( &$field, $value = '', &$config = array() )
	{
		if ( self::$type != $field->type ) {
			return;
		}
		parent::g_onCCK_FieldPrepareContent( $field, $config );
		
		// Set
		$field->value	=	$value;
	}
	
	// onCCK_FieldPrepareForm
	public function onCCK_FieldPrepareForm( &$field, $value = '', &$config = array(), $inherit = array(), $return = false )
	{
		if ( self::$type != $field->type ) {
			return;
		}
		self::$path	=	parent::g_getPath( self::$type.'/' );
		parent::g_onCCK_FieldPrepareForm( $field, $config );
		
		// Init
		if ( count( $inherit ) ) {
			$id		=	( isset( $inherit['id'] ) && $inherit['id'] != '' ) ? $inherit['id'] : $field->name;
			$name	=	( isset( $inherit['name'] ) && $inherit['name'] != '' ) ? $inherit['name'] : $field->name;
		} else {
			$id		=	$field->name;
			$name	=	$field->name;
		}
		$value		=	( $value != '' ) ? $value : $field->defaultvalue;
		$value		=	( $value != ' ' ) ? $value : '';
		$value		=	htmlspecialchars( $value );
		
		// Validate
		$validate	=	'';
		if ( $config['doValidation'] > 1 ) {
			plgCCK_Field_ValidationRequired::onCCK_Field_ValidationPrepareForm( $field, $id, $config );
			parent::g_onCCK_FieldPrepareForm_Validation( $field, $id, $config );
			$validate	=	( count( $field->validate ) ) ? ' validate['.implode( ',', $field->validate ).']' : '';
		}
		
		// Prepare
		$class	=	'inputbox text'.$validate . ( $field->css ? ' '.$field->css : '' );
		$maxlen	=	( $field->maxlength > 0 ) ? ' maxlength="'.$field->maxlength.'"' : '';
		$attr	=	'class="'.$class.'" size="'.$field->size.'"'.$maxlen . ( $field->attributes ? ' '.$field->attributes : '' );
		if($field->storage_location) {
		jimport('joomla.filesystem.file');
	
		if( !JFile::exists( JPATH_SITE.'/components/com_quick2cart/quick2cart.php') ){
			return true;
		}
		$name_form = 'com_content.article';
		if(isset($config['storages']['#__content']->title)) JRequest::setVar("qtc_article_name",$config['storages']['#__content']->title);
		$doc = JFactory::getDocument();
		$doc->addStyleSheet(JUri::root(true) .'components/com_quick2cart/assets/css/quick2cart.css');
	   $doc->addStyleDeclaration('.cck_label_quick2cart { display:none;}');
		$doc->addScript(JURI::root(true).'/media/techjoomla_strapper/js/namespace.min.js');
		$doc->addScript(JURI::root(true).'/components/com_quick2cart/assets/js/order.js');
      $doc->addScript(self::$path.'assets/quick2cart.js');

		// Add the registration fields to the form.
	jimport('joomla.form.helper');
       JFormHelper::loadFieldClass('list');
		$lang = JFactory::getLanguage();
		$lang->load('com_quick2cart', JPATH_ADMINISTRATOR);
		$fieldName = $name;
		JHTML::_('behavior.modal', 'a.modal');
		$html =''; 
		$client = "com_content";
		//$pid = JRequest::getVar('id');
		$jinput = JFactory::getApplication()->input;
		$pid=$jinput->get('id');
	//	if($pid) {
		// CHECK for view override	
		/*	$mainframe = JFactory::getApplication();
				$lang = JFactory::getLanguage();
				$lang->load('com_quick2cart');
				$comquick2cartHelper = new comquick2cartHelper();
			$path = $comquick2cartHelper->getBuynow($pid, "com_content", array());
	
	ob_start();
		include($path);
		$html = ob_get_contents();
		ob_end_clean();	*/
				$comquick2cartHelper = new comquick2cartHelper();
	 	$path=$comquick2cartHelper->getViewpath('attributes','',"ADMIN","SITE");
		ob_start();
			include($path);
		$html = ob_get_contents();
		ob_end_clean();					
		//}
	//	else{
	//		$html = "<br/><p style='float: left;'>".JText::_('QTC_ADDATTRI_EMPTY_MSG_DESC')."</p>";
	//	}

		$form =  '<div id="quick2cart_info">'.$html.'</div>';

		}
	//	 $form	= '';
		// Set
		if ( ! $field->variation ) {
			$field->form	=	$form;
			if ( $field->script ) {
				parent::g_addScriptDeclaration( $field->script );
			}
		} else {
			parent::g_getDisplayVariation( $field, $field->variation, $value, $value, $form, $id, $name, '<input', '', '', $config );
		}
		$field->value	=	$value;
		
		// Return
		if ( $return === true ) {
			return $field;
		}
	}
	
	// onCCK_FieldPrepareSearch
	public function onCCK_FieldPrepareSearch( &$field, $value = '', &$config = array(), $inherit = array(), $return = false )
	{
		if ( self::$type != $field->type ) {
			return;
		}
		
		// Prepare
		self::onCCK_FieldPrepareForm( $field, $value, $config, $inherit, $return );
		
		// Return
		if ( $return === true ) {
			return $field;
		}
	}
	
	// onCCK_FieldPrepareStore
	public function onCCK_FieldPrepareStore( &$field, $value = '', &$config = array(), $inherit = array(), $return = false )
	{
		if ( self::$type != $field->type ) {
			return;
		}
		
		// Init
		if ( count( $inherit ) ) {
			$name	=	( isset( $inherit['name'] ) && $inherit['name'] != '' ) ? $inherit['name'] : $field->name;
		} else {
			$name	=	$field->name;
		}
		
		// Validate
		parent::g_onCCK_FieldPrepareStore_Validation( $field, $name, $value, $config );
		
		// Set or Return
		if ( $return === true ) {
			return $value;
		}
		$field->value	=	$value;
		parent::g_onCCK_FieldPrepareStore( $field, $name, $value, $config );
	}
	
	// -------- -------- -------- -------- -------- -------- -------- -------- // Render
	
	// onCCK_FieldRenderContent
	public static function onCCK_FieldRenderContent( $field, &$config = array() )
	{
		return parent::g_onCCK_FieldRenderContent( $field );
	}
	
	// onCCK_FieldRenderForm
	public static function onCCK_FieldRenderForm( $field, &$config = array() )
	{
		return parent::g_onCCK_FieldRenderForm( $field );
	}
	
	// -------- -------- -------- -------- -------- -------- -------- -------- // Special Events
	
	// onCCK_FieldBeforeRenderContent
	public static function onCCK_FieldBeforeRenderContent( $process, &$fields, &$storages, &$config = array() )
	{
	}
	
	// onCCK_FieldBeforeRenderForm
	public static function onCCK_FieldBeforeRenderForm( $process, &$fields, &$storages, &$config = array() )
	{
	}
	
	// onCCK_FieldBeforeStore
	public static function onCCK_FieldBeforeStore( $process, &$fields, &$storages, &$config = array() )
	{
	}
	
	// onCCK_FieldAfterStore
	public static function onCCK_FieldAfterStore( $process, &$fields, &$storages, &$config = array() )
	{
	}
	
	// -------- -------- -------- -------- -------- -------- -------- -------- // Stuff & Script
	
	//
}
?>
