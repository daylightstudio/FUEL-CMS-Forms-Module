<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');

require_once(FUEL_PATH.'models/base_module_model.php');

class Form_entries_model extends Base_module_model {

	// read more about models in the user guide to get a list of all properties. Below is a subset of the most common:

	public $record_class = 'Form_entry'; // the name of the record class (if it can't be determined)
	public $filters = array(); // filters to apply to when searching for items
	public $required = array(); // an array of required fields. If a key => val is provided, the key is name of the field and the value is the error message to display
	public $foreign_keys = array('form_name' => array(FORMS_FOLDER => 'forms_model')); // map foreign keys to table models
	public $linked_fields = array(); // fields that are linked meaning one value helps to determine another. Key is the field, value is a function name to transform it. (e.g. array('slug' => 'title'), or array('slug' => arry('name' => 'strtolower')));
	public $boolean_fields = array(); // fields that are tinyint and should be treated as boolean
	public $unique_fields = array(); // fields that are not IDs but are unique. Can also be an array of arrays for compound keys
	public $parsed_fields = array(); // fields to automatically parse
	public $serialized_fields = array(); // fields that contain serialized data. This will automatically serialize before saving and unserialize data upon retrieving
	public $has_many = array(); // keys are model, which can be a key value pair with the key being the module and the value being the model, module (if not specified in model parameter), relationships_model, foreign_key, candidate_key
	public $belongs_to = array(); // keys are model, which can be a key value pair with the key being the module and the value being the model, module (if not specified in model parameter), relationships_model, foreign_key, candidate_key
	public $formatters = array(); // an array of helper formatter functions related to a specific field type (e.g. string, datetime, number), or name (e.g. title, content) that can augment field results
	public $display_unpublished_if_logged_in = FALSE;
	public $filter_join = 'and'; // how to combine the filters in the query (and or or)

	protected $friendly_name = ''; // a friendlier name of the group of objects
	protected $singular_name = ''; // a friendly singular name of the object
	protected $is_export = FALSE;

	public function __construct()
	{
		parent::__construct('form_entries', FORMS_FOLDER); // table name
		$this->filters =  array($this->_tables['forms'].'.name', 'post', 'date_added');
	}

	public function list_items($limit = NULL, $offset = NULL, $col = 'date_added', $order = 'desc', $just_count = FALSE)
	{
		$this->db->select($this->_tables['form_entries'].'.id, '.$this->_tables['form_entries'].'.form_name, '.$this->_tables['form_entries'].'.post, '.$this->_tables['form_entries'].'.is_spam, '.$this->_tables['form_entries'].'.remote_ip, '.$this->_tables['form_entries'].'.date_added');
		$data = parent::list_items($limit, $offset, $col, $order, $just_count);

		if (!$just_count && !$this->is_export)
		{
			foreach($data as $key => $val)
			{
				$data[$key] = str_replace(array('[', ']', '"', '{', '}'), '', $data[$key]);
				$data[$key] = str_replace(array(','), ', ', $data[$key]);
				// put deserializing json_decode code here if needed
			}
		}
		return $data;
	}

	public function form_fields($values = array(), $related = array())
	{	
		$CI =& get_instance();
		$fields = parent::form_fields($values, $related);
		$fields['form_name']['options'] = $CI->fuel->forms->options_list();
		$fields['post']['type'] = 'keyval';
		return $fields;
	}
	
	public function on_before_save($values)
	{
		parent::on_before_save($values);
		return $values;
	}

	public function on_after_save($values)
	{
		parent::on_after_save($values);
		return $values;
	}

	// --------------------------------------------------------------------
	
	/**
	 * Returns CSV data that will be downloaded automatically. Overwrite this method for more specific output
	 *
	 * @access	public
	 * @param	array An array that contains "col", "order", "offset", "limit", "search_term" to help with the formatting of the output. By default only the "col" and "order" parameters are used (optional)
	 * @return	string
	 */	
	public function export_data($params = array())
	{
		$CI =& get_instance();
		$form_name = (int) $this->input->post('form_name');
		$form = $CI->fuel->forms->get($form_name);

		// normalize parameters
		$valid_params = array('col', 'order');
		foreach($valid_params as $p)
		{
			if (!isset($params[$p]))
			{
				$params[$p] = NULL;
			}
		}

		$this->is_export = TRUE;
		$items = $this->list_items(NULL, NULL, $params['col'], $params['order']);

    	$field_terminator=',';
    	$line_terminator="\n";
        $data = '';
        $data = array();
        $header = '';


        // get a list of all the fields we'll need
        $headings = array();
        foreach($items as $item)
		{
			$post = json_decode($item['post'], TRUE);
			unset($item['post']);
			$item = array_merge($item, $post);
			$headings = array_unique(array_merge($headings, array_keys($item)));
		}

		// create headings
		foreach($headings as $heading)
		{
			$data[0][$heading] = ucwords(str_replace(array('_', '-'), ' ', $heading));
		}

		// now loop through the data and place the data based on all the ehadings
		$i = count($data);
    	foreach($items as $item)
		{
			$post = json_decode($item['post'], TRUE);

			// we don't know what was thrown into the post so we'll remove any values from post that may conflict
			unset($post['id'], $post['remote_ip'], $post['date_added'], $item['post']);

			// merge data from post
			$item = array_merge($item, $post);

			foreach($headings as $heading)
			{
				$data[$i][$heading] = (isset($item[$heading])) ? $item[$heading] : '';
			}
			$i++;
		}

		$filename = (isset($form->id)) ? $form->name : 'forms';
		$filename .= '_' . date("Ymd") . '.csv';	
				
		header('Content-type: application/download');
		header('Content-Type: text/csv' );
		header('Content-Disposition: attachment;filename='.$filename);
		$fp = fopen('php://output', 'w');
		foreach($data as $d)
		{
			fputcsv($fp, $d);
		}
		fclose($fp);
		// exit to prevent running the rest of the script
		exit();
	}

	public function _common_query($display_unpublished_if_logged_in = NULL)
	{
		parent::_common_query($display_unpublished_if_logged_in );

		$this->db->select($this->_tables['form_entries'].'.*, '.$this->_tables['forms'].'.name as form');
		$this->db->join($this->_tables['forms'], $this->_tables['forms'].'.name = '.$this->_tables['form_entries'].'.form_name', 'LEFT');

		// remove if no precedence column is provided
		$this->db->order_by('date_added desc');
	}

}

class Form_entry_model extends Base_module_record {
	
	// put your record model code here
	public function is_spam()
	{
		return is_true_val($this->is_spam);
	}

	public function is_savable()
	{
		return !$this->is_spam() OR ($this->is_spam() AND $this->_CI->fuel->forms->config('save_spam'));
	}
}