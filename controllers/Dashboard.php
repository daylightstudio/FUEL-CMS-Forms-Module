<?php
require_once(FUEL_PATH.'/libraries/Fuel_base_controller.php');

class Dashboard extends Fuel_base_controller {
	
	function __construct()
	{
		parent::__construct();

	}

	function index()
	{
		if ($this->fuel->auth->has_permission('forms'))
		{
			$num_days = 30;
			$end_date = date('Y-m-d 23:59:59');
			$start_date_ts = time() - (60 *60 *24 * $num_days); // Past month
			$start_date = date('Y-m-d', $start_date_ts);
			$entries = $this->fuel->forms->all_entries_by_date($start_date, $end_date);

			if (!empty($entries))
			{
				$data['entries'] = $entries;
				$this->load->module_view(FORMS_FOLDER, '_admin/dashboard', $data);
			}
		}
		
	}
}