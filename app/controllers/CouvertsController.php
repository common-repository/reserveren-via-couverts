<?php

namespace Tussendoor\Couverts;

class CouvertsController {

	protected $api;
	protected $plugin;

	public function __construct()
	{
		$this->plugin = CouvertsPlugin::getInstance();
		$this->api = new CouvertsApi('Dutch', $this->plugin->settings);

		add_action('wp_head', array($this, 'defineAjax'));
		add_action('wp_enqueue_scripts', array($this, 'loadScripts'));

		add_action('wp_ajax_nopriv_couvertsForm', array($this, 'processForm'));
		add_action('wp_ajax_couvertsForm', array($this, 'processForm'));
	}

	/**
	 * Register the shortcode used for the reservationform.
	 */
	public function registerShortcode()
	{	
		add_shortcode('couvertsForm', array($this, 'getForm'));
	}


	/**
	 * The URL to admin-ajax.php is not available on the front-end by default. This loads it in the <head>
	 * @return String
	 */
	public function defineAjax() {
	    echo '<script type="text/javascript">
	        var ajaxurl = "'.admin_url('admin-ajax.php').'"
	    </script>';
	}

	/**
	 * Register and load the required css and js for the front-end.
	 */
	public function loadScripts()
	{	
		wp_register_style('couvertsStyle', $this->plugin->url.'/assets/css/couverts.min.css', false, $this->plugin->version);
		wp_enqueue_style('couvertsStyle');

		wp_register_script('couvertsScript', $this->plugin->url.'/assets/js/couverts.min.js', array('jquery'), $this->plugin->version, true);
		wp_enqueue_script('couvertsScript');
		wp_enqueue_script('jquery-ui-datepicker');
	}

	/**
	 * Entry point for the shortcode
	 */
	public function getForm()
	{
		$html = '
			<section id="couvertsFormHolder">
				
			</section>
			<div class="spinner">
				<div class="bounce1"></div>
				<div class="bounce2"></div>
				<div class="bounce3"></div>
			</div>
		';
		return $html;
	}

	/**
	 * Method that processes the form steps and executes the right functions
	 * @return String 					Returns HTML on success.
	 */	
	public function processForm()
	{	
		$lang = $this->api->language;
		$info = $this->api->getBasicInformation();

		//Check if the API settings are set/correct
		if (isset($info->status) && $info->status === false) {
			$error = $info->info;
			include $this->plugin->partial_path.'/form.error.php';
			wp_die();
		}

		if ($_POST['command'] == 'getForm') {
			include $this->plugin->partial_path.'/form.date.php';
		} elseif ($_POST['command'] == 'getFormNext') {
			parse_str($_POST['info'], $formData);

			switch ($formData['currentStep']) {
				case '0':
					include $this->plugin->partial_path.'/form.date.php';
					break;

				case '1':
					$date = new \Datetime($formData['date']);
					$availableTimes = $this->api->getAvailableTimesForDate($date, intval($formData['numberPersons']));

					if (is_null($availableTimes->NoTimesAvailable)) {
					    include $this->plugin->partial_path.'/form.selecttime.php';
					} else {
					    include $this->plugin->partial_path.'/form.date.php';
					}
					break;

				case '2':
					$date = new \DateTime($formData['date'].' '.trim($formData['timeSelected']));
					$formSettings = $this->api->getForm($date);

					include $this->plugin->partial_path.'/form.information.php';
					break;

				case '3':
					$date = new \DateTime($formData['date']);
					$formSettings = $this->api->getForm($date);

					$errors = $this->validateForm($formSettings, $formData);

					if ($errors['error'] > 0) {
						$formData['currentStep']--; 
						include $this->plugin->partial_path.'/form.information.php';
					} else {
						$reservationDate = new \DateTime($formData['date']);
						$postData = $this->formatReservation($reservationDate, $formData, $formSettings);

						$response = $this->api->submitReservation($postData);
						
						include $this->plugin->partial_path.'/form.thankyou.php';
					}
					
					break;
				
				default:
					# code...
					break;
			}
		} else {
			echo json_encode(array('error' => 'error'));
		}

		wp_die();
	}

	/**
	 * Formats the form data in the right format used by Couverts.
	 * @param  \DateTime 	$date     		A DateTime object of the reservationdate.
	 * @param  Array    	$data     		All fields submitted to the server
	 * @param  Object    	$settings 		The form settings from Couverts
	 * @return Array              			A formatted array
	 */
	private function formatReservation(\DateTime $date, $data, $settings)
	{
		$reservationData = array();

		$reservationData['Date'] = array(
			'Year' 	=> $date->format('Y'),
			'Month' => $date->format('m'),
			'Day' 	=> $date->format('d'),
		);

		$reservationData['Time'] = array(
			'Hours' 	=> $date->format('H'),
			'Minutes' 	=> $date->format('i'),	
		);

		foreach ($settings as $name => $setting) {
			if ($name == 'BirthDate' && isset($data['BirthDate'])) {
				$reservationData[$name] = date('Y-m-d', strtotime($data[$name]));
			} elseif (isset($data[$name])) {
				$reservationData[$name] = $data[$name];
			}
		}
		$reservationData['NumPersons'] = $data['numberPersons'];

		return $reservationData;
	}

	/**
	 * Validates the submitted fields against the form settings. Only checks for Required
	 * @param  Object 		$settings 		The formsettings from Couverts
	 * @param  Array 		$data     		The submitted form data
	 * @return Array           				An array containing an error count and a list of fields that failed validation
	 */
	private function validateForm($settings, $data)
	{
		$validation = array('error' => 0, 'fields' => array());

		foreach ($settings as $name => $setting) {
			if ($setting->Required && empty($data[$name])) {
				$validation['error']++;
				$validation['fields'][$name] = false;
			}
		}

		return $validation;
	}

}
