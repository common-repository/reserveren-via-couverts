<?php 

namespace Tussendoor\Couverts;

class CouvertsApi {

    public $language;
    protected $baseUrl;
    protected $restaurantId;
    protected $apiKey;

    /**
     * Setup the API language and get the saved API settings
     * @param string        $language       The API language, default is Dutch
     * @param Settings      $settings       The Settings class used by this plugin
     */
    public function __construct($language = 'Dutch', Settings $settings)
    {
        $this->language = $language;
        $this->baseUrl = $settings->get('couvertsBaseUrl');
        $this->restaurantId = $settings->get('couvertsRestaurantId');
        $this->apiKey = $settings->get('couvertsApiKey');
    }

    /**
     * Private method for sending a GET request via cURL.
     * @param  String       $url        The URL relative to $this->baseUrl
     * @return Object
     */
    private function getRequest($url)
    {
        $ch = curl_init(); 
        curl_setopt($ch, CURLOPT_URL, $this->baseUrl.$url); 
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_AUTOREFERER, true);
        curl_setopt($ch, CURLOPT_MAXREDIRS, 10);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); 
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_ANY);
        curl_setopt($ch, CURLOPT_USERPWD, $this->restaurantId.':'.$this->apiKey);
        
        $data = curl_exec($ch); 
        $chStatus = curl_getinfo($ch);
        curl_close ($ch);

        if ($chStatus['http_code'] !== 200) {
            return $this->curlError($chStatus);
        }

        return json_decode($data);
    }

    /**
     * Private method for sending a POST request via cURL.
     * @param  String       $url        The URL relative to $this->baseUrl
     * @param  Array        $fields     All the fields that are posted to the server
     * @return Object         
     */
    private function postRequest($url, $fields)
    {   
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->baseUrl.$url); 
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_AUTOREFERER, true);
        curl_setopt($ch, CURLOPT_MAXREDIRS, 10);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); 
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_ANY);
        curl_setopt($ch, CURLOPT_USERPWD, $this->restaurantId.':'.$this->apiKey);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($fields));

        $data = curl_exec($ch); 
        $chStatus = curl_getinfo($ch);
        curl_close ($ch);

        if ($chStatus['http_code'] !== 200) {
            return $this->curlError($chStatus);
        }

        return json_decode($data);
    }

    /**
     * Standardized way of returning an error when the HTTP status != 200 (ok)
     * @param  Array        $data       Status information about the cURL request
     * @return Object       
     */
    private function curlError($data)
    {
        $error = new \stdClass();
        $error->status = false;
        $error->info = $data;

        return $error;
    }

    /**
     * Interface method for getting the basic information of the restaurant
     * @return Object 
     */
    public function getBasicInformation()
    {
        return $this->getRequest('basicinfo');
    }

    /**
     * Returns all available times for a given time
     * @param  \DateTime        $date           The selected reservationdate
     * @param  Integer          $numPersons     The number of persons
     * @return Object                
     */
    public function getAvailableTimesForDate(\DateTime $date, $numPersons)
    {
        $url = sprintf(
            'AvailableTimes?numPersons=%d&year=%d&month=%d&day=%d', 
            $numPersons,
            $date->format('Y'),
            $date->format('m'),
            $date->format('d')
        );

        return $this->getRequest($url);
    }

    /**
     * Returns the form settings 
     * @param  \DateTime        $timestamp      The selected reservationdate + time
     * @return Object               
     */
    public function getForm(\DateTime $timestamp) {
        $url = sprintf(
            'InputFields?year=%d&month=%d&day=%d&hours=%d&minutes=%d',
            $timestamp->format("Y"),
            $timestamp->format("m"),
            $timestamp->format("d"),
            $timestamp->format("H"),
            $timestamp->format("i")
        );

        return $this->getRequest($url);
    }

    /**
     * Submit the reservation to Couverts via Post
     * @param  Array        $fields            A formatted array of fields to send to Couverts
     * @return Object                   
     */
    public function submitReservation($fields)
    {
        return $this->postRequest('reservation', $fields);
    }

}
