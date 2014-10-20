<?php
class Forms extends CI_Controller {
	
	public function __construct()
	{
		parent::__construct();
		$this->load->helper('ajax');
		$this->load->library('session');
	}

	public function process($slug)
	{
		$form = $this->fuel->forms->get($slug);
		
		$return_url = ($this->input->get_post('return_url')) ? $this->input->get_post('return_url') : $form->get_return_url();
		$form_url = $this->input->get_post('form_url');

		if ($form AND $form->process())
		{
			if (is_ajax())
			{
				// Set a 200 (okay) response code.
				set_status_header('200');
				echo $form->after_submit_text;
				exit();
			}
			else
			{
				$this->session->set_flashdata('success', TRUE);
				redirect($return_url);
			}
		}
		else
		{
			$this->session->set_flashdata('posted', $this->input->post());

			if (is_ajax())
			{
				// Set a 500 (bad) response code.
				set_status_header('500');
				echo display_errors(NULL, '');
				exit();
			}
			else
			{
				if (!empty($form_url) && ($form_url != $return_url))
				{
					$return_url = $form_url; // update to post back to the correct page when there's an error
				}
				$this->session->set_flashdata('error', $form->errors());
				redirect($return_url);
			}
		}

	}
}