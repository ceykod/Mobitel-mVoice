<?php

/*********************************************************************

 * playFile($fileName)
 * playFileAsync($fileName, $callbackURL)
 * sayNumber($number, $language)
 * sayDigits($number, $language)
 * hangup()

 * int playFileWaitInput($fileName, $timeout, $numDigits)
 
 * getCallerId()
 * getIvrNo()
 * getTxId()

 *********************************************************************/
class mVoice
{
    private $clientId;
    private $callerId;
    private $txId;
    private $ivrNo;
    private $txdata;

    public function __construct($clientId)
    {
        // Obtained data form the post request
        $array = json_decode(file_get_contents('php://input'), true);

        $this->callerId = $array['callerid'];
        $this->txId = $array['txid'];
        $this->ivrNo = $array['ivrno'];

        // Obtained data from construction parameter
        $this->clientId = $clientId;

        $this->txdata = array(
            "txid" => $this->txId,
            "ivrno" => $this->ivrNo,
            "callerid" => $this->callerId
        );
    }

    public function playFile($fileName)
    {
        $data = array(
            "txdata" => $this->txdata,
            "filename" => $fileName     // The name of the file that you uploaded to the portal
        );

        $endpoint = "https://apphub.mobitel.lk/mobext/mapi/mvoice/playfile";
        $jsonObjectFields = json_encode($data);

        return $this->sendRequest($endpoint, $jsonObjectFields);
    }

    public function playFileAsync($fileName, $callbackURL)
    {
        $data = array(
            "txdata" => $this->txdata,
            "filename" => $fileName,        // The name of the file that you uploaded to the portal
            "callbackurl" => $callbackURL   // This is the custom URL to which you will receive a callback when the file playback is complete
        );

        $endpoint = "https://apphub.mobitel.lk/mobext/mapi/mvoice/playfileasync";
        $jsonObjectFields = json_encode($data);

        return $this->sendRequest($endpoint, $jsonObjectFields);
    }

    public function playFileWaitInput($fileName, $timeout, $numDigits)
    {
        $data = array(
            "txdata" => $this->txdata,
            "filename" => $fileName,    // The name of the file that you uploaded to the portal
            "timeout" => $timeout,      // The maximum time this method will wait for the customer to enter a digit
            "numdigits" => $numDigits   // The number of digits the customer is expected to enter
        );

        $endpoint = "https://apphub.mobitel.lk/mobext/mapi/mvoice/playandwaitforinput";
        $jsonObjectFields = json_encode($data);

        // response: {"status":1,"code":1000,"msg":"Success","data":{"input":"1"}}
        $response = json_decode($this->sendRequest($endpoint, $jsonObjectFields),1);

        return $response['data']['input'];
    }

    public function sayNumber($number, $language)
    {
        $data = array(
            "txdata" => $this->txdata,
            "number" => $number,        // The number that you want to readout
            "language" => $language     // Language: en, sin, tam
        );

        $endpoint = "https://apphub.mobitel.lk/mobext/mapi/mvoice/saynumber";
        $jsonObjectFields = json_encode($data);

        return $this->sendRequest($endpoint, $jsonObjectFields);
    }

    public function sayDigits($number, $language)
    {
        $data = array(
            "txdata" => $this->txdata,
            "number" => $number,        // The digits that you want to readout
            "language" => $language     // Language: en, sin, tam
        );

        $endpoint = "https://apphub.mobitel.lk/mobext/mapi/mvoice/saydigits";
        $jsonObjectFields = json_encode($data);

        return $this->sendRequest($endpoint, $jsonObjectFields);
    }

    public function hangup()
    {
        $data = $this->txdata;
        $endpoint = "https://apphub.mobitel.lk/mobext/mapi/mvoice/hangupcall";
        $jsonObjectFields = json_encode($data);

        return $this->sendRequest($endpoint, $jsonObjectFields);
    }

    public function helloWorld()
    {
        $data = $this->txdata;
        $endpoint = "https://apphub.mobitel.lk/mobext/mapi/mvoice/helloworld";
        $jsonObjectFields = json_encode($data);

        return $this->sendRequest($endpoint, $jsonObjectFields);
    }


    public function getCallerId()
    {
        return $this->callerId;
    }

    public function getIvrNo()
    {
        return $this->ivrNo;
    }

    public function getTxId()
    {
        return $this->txId;
    }

    private function sendRequest($endpoint, $jsonObjectFields)
    {
        $curl = curl_init();
        curl_setopt_array($curl, array(

            CURLOPT_URL => $endpoint,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => $jsonObjectFields,
            CURLOPT_HTTPHEADER => array(
                "accept: application/json",
                "content-type: application/json",
                "x-ibm-client-id: " . $this->clientId
            ),
        ));

        $response = curl_exec($curl);
        curl_close($curl);
        return $this->handleResponse($response);
    }

    private function handleResponse($resp)
    {
        if ($resp == "") {
            return json_encode(array("status" => 0, "code" => "UnknownError", "msg" => "Unsuccessful"));
        } else {
            return $resp;
        }
    }
} 