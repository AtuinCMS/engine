<?php


namespace atuin\engine\helpers;


use yii\base\Component;

class AjaxResponse extends Component
{
    /**
     * Defines if the Ajax Response it's a normal one or has had an error.
     * @var int
     */
    protected $error;

    /**
     * Error message in case there has been one. Null otherwise
     * @var String
     */
    protected $errorMessage;

    /**
     * Response data
     * @var Mixed
     */
    protected $data;

    /**
     * @param String $errorMessage
     */
    public function setErrorMessage($errorMessage)
    {
        $this->error = 1;
        $this->errorMessage = $errorMessage;
    }

    public function toJSON()
    {
        $response = $this->toArray();

        return json_encode($response);
    }

    public function toArray()
    {
        $response = [];
        $response['error'] = $this->error;
        $response['error_message'] = $this->errorMessage;
        $response['data'] = $this->data;

        return $response;
    }

    /**
     * Sets the response data
     * @param $data
     */
    public function setData($data)
    {
        $this->data = $data;
    }

    public function getData()
    {
        return $this->data;
    }

    public function getError()
    {
        return $this->error;
    }

    public function getErrorMessage()
    {
        return $this->errorMessage;
    }

}