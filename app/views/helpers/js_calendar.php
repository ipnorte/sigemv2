<?php
/**
 * 
 * @author ADRIAN TORRES
 * @package general
 */

class JsCalendarHelper extends Helper
{
	var $helpers = array('Head', 'Html','Frm');

    var $_library; //static array of items to be included            
	
	var $calendar_lib_path;
    var $calendar_file;
    var $calendar_setup_file;
    var $calendar_lang_file;
    var $calendar_theme_file;
    var $calendar_options;
	var $headers_included = false;
	var $theme;

	/**
	* Constructor
	* @param string $calendar_lib_path directory in css, js and img
	*	where jscalendar files are stored
	* @param string $lang language
	* @param string $theme look
	* @param boolean $stripped strip spaces
	*/
    function __construct($calendar_lib_path = 'calendar/',
   			  			 $lang              = 'es',
                  		 $theme             = 'skins/aqua/theme',
                  		 $stripped          = false
                  		 )
    {
        static $library=array();  //for php4 compat

        if ($stripped) {
            $this->calendar_file = 'calendar_stripped';
            $this->calendar_setup_file = 'calendar-setup_stripped';
        } else {
            $this->calendar_file = 'calendar';
            $this->calendar_setup_file = 'calendar-setup';
        }

        $this->calendar_lang_file = 'lang/calendar-' . $lang . '';
        $this->calendar_theme_file = $theme;
        $this->calendar_lib_path = preg_replace('/\/+$/', '/', $calendar_lib_path);
        $this->calendar_options = array('ifFormat' => '%d/%m/%Y',
                                        'daFormat' => '%d/%m/%Y',
                                        'align'=>'',
                                        'width'=>'240px',
                                        );
    }

    /**
     * Calendar options setter.
     * @param string $name name of the option
     * @param string $value value of the option
     */
    function set_option($name, $value) {
        $this->calendar_options[$name] = $value;
    }

    /**
     * Loads the stylesheets and js code.
     * @param string $name name of the option
     * @param string $value value of the option
     */
    function load_files() {
        $this->Head->register_css($this->calendar_lib_path . $this->calendar_theme_file);
        $this->Head->register_js($this->calendar_lib_path . $this->calendar_file);
        $this->Head->register_js($this->calendar_lib_path . $this->calendar_lang_file);
        $this->Head->register_js($this->calendar_lib_path . $this->calendar_setup_file);
        $this->Head->register_cssblock(".calendar, .calendar table {  width:240px; }");
    }

    /**
     * Generates the javascript block to instantiate the calendar with
     * the options set in $this->calendar_options and ...
     * @param array $other_options extra options to set to our calendar
     */
    function _make_calendar($other_options = array()) {
        $js_options = $this->_make_js_hash(array_merge($this->calendar_options, $other_options));
        $code  = ( '<script type="text/javascript">Calendar.setup({' .
                   $js_options .
                   '});</script>' );
        return $code;
    }

    /**
     * Returns the code to display a calendar with an input field
     * @param string $tagName widget's tagname
     * @param array $cal_options calendar's extra options
     * @param array $field_attributes input field attributes
     */
    function input($tagName, $cal_options = array(), $field_attributes = array()) {
    	if (!$this->headers_included)
    	{
    		$this->load_files();
    		$this->headers_included = true;
    	}

    	$id = $this->_gen_id();
        $ret = '<div>'.$this->Frm->inputFecha(
        						$tagName , 
        						array_merge($field_attributes,
											array('id'   => $this->_field_id($id),
                                                  'type' => 'text',
                                                  'readonly'=>'1'
                                                 )));
//        debug($this->calendar_lib_path);
//        $img = $this->Html->image($this->calendar_lib_path . 'img.gif', array('border'=>"0"));
        $img = $this->Html->image('calendar/img.gif', array('border'=>"0"));
        $ret .= $this->Html->link($img, "#", array('id'=>$this->_trigger_id($id) ), false, false).'</div>';
        
        $options = array_merge($cal_options,
                               array('inputField' => $this->_field_id($id),
                                     'button'     => $this->_trigger_id($id)));
        $ret .= $this->_make_calendar($options);
        return $ret;
    }

    /// PRIVATE SECTION

    function _field_id($id) { return 'f-calendar-field-' . $id; }
    function _trigger_id($id) { return 'f-calendar-trigger-' . $id; }
    function _gen_id() { static $id = 0; return ++$id; }

    function _make_js_hash($array) {
        $jstr = '';
        reset($array);
        while (list($key, $val) = each($array)) {
            if (is_bool($val))
                $val = $val ? 'true' : 'false';
            else if (!is_numeric($val))
                $val = '"'.$val.'"';
            if ($jstr) $jstr .= ',';
            $jstr .= '"' . $key . '":' . $val;
        }
        return $jstr;
    }

    function _make_html_attr($array) {
        $attrstr = '';
        reset($array);
        while (list($key, $val) = each($array)) {
            $attrstr .= $key . '="' . $val . '" ';
        }
        return $attrstr;
    }
}

?>