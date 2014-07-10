<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Robokassa
{
	private
		$merchant_login,
		$password_1,
		$password_2,
		$url,
		$CI,
		$user_params = array();

	protected 
		$invoice_id,
		$description,
		$currency,
		$encoding,
		$language,
		$md5_signature,
		$sum,
		$email;


	
	function __construct($invoice_id = NULL)
	{
		
		$this->set_invoice_id($invoice_id);

		$config = Kohana::$config->load('robokassa');

		$this->merchant_login	= $config['login'];
		$this->password_1		= $config['password_1'];
		$this->password_2		= $config['password_2'];
		$this->language			= $config['lang'];
		$this->encoding			= $config['encoding'];
		$this->currency			= $config['currency'];
		$this->url				= $config['url'];
	}

	public function set_invoice_id($invoice_id)
	{
		$this->invoice_id = intval($invoice_id);
	}

	public function set_description($description)
	{
		$this->description = trim($description);
	}

	public function set_sum($sum)
	{
		$this->sum = trim($sum);
	}

	public function set_email($email)
	{
		$this->email = trim($email);
	}

	public function set_user_var($var, $value)
	{
		$this->user_params['Shp_'.$var] = $value;
	}

	public function create_sign()
	{
		$sign = "$this->merchant_login:$this->sum:$this->invoice_id:$this->password_1";

		if ($this->user_params)
		{
			foreach ($this->user_params as $key => $value)
			{
				$sign .= ':'.$key.'='.$value;
			}
		}

		return md5(trim($sign, ':'));
	}

	public function create_result_sign()
	{
		return $this->create_p2_sign(array($this->sum, $this->invoice_id));
	}

	public function create_success_sign()
	{
		return $this->create_p1_sign(array($this->sum, $this->invoice_id));
	}

	public function create_p1_sign($params)
	{
		return md5(join(':', $params).':'.$this->password_1);
	}

	public function create_p2_sign($params)
	{
		return md5(join(':', $params).':'.$this->password_2);
	}

	public function get_params()
	{
		$params =  array(
				'MrchLogin'			=> $this->merchant_login,
				'OutSum'			=> $this->sum,
				'InvId'				=> $this->invoice_id,
				'Desc'				=> trim($this->description),
				'SignatureValue'	=> $this->create_sign(),
				'IncCurrLabel'		=> trim($this->currency),
				'Culture'			=> trim($this->language),
				'Encoding'			=> trim($this->encoding),
				'Email'				=> trim($this->email),
			);
		if ($this->user_params)
		{
			$params += $this->user_params;
		}

		return $params;
	}

	public function get_payment_url()
	{
		return $this->url.'?'.http_build_query($this->get_params());
	}

	public function get_invoice_state($invoice_id = NULL)
	{
		if (is_null($invoice_id))
		{
			$invoice_id = $this->invoice_id;
		}
		$invoice_id = intval($invoice_id);
		
		$url = 'https://merchant.roboxchange.com/WebService/Service.asmx/OpState?'.
			http_build_query(array(	'MerchantLogin' 	=> $this->merchant_login,
									'InvoiceID'			=> $invoice_id,
									'Signature'			=> $this->create_p2_sign(array($this->merchant_login, $invoice_id)),
						));

		$ch = curl_init();

		curl_setopt($ch, CURLOPT_URL,            $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);

		$xml = curl_exec($ch);
		curl_close($ch);

		$xml = simplexml_load_string($xml);

		if ( ! $xml->Result->Code OR intval($xml->Result->Code != 0))
		{
			return FALSE;
		}

		$result = array(
			'code_request'	=> intval($xml->State->Code),
			'request_date'	=> date('Y-m-d H:i:s', strtotime($xml->State->RequestDate)),
		);

		if ($xml->Info->PaymentMethod)
		{
			$result['payment_method_code'] 			= trim($xml->Info->PaymentMethod->Code);
			$result['payment_method_description'] 	= trim($xml->Info->PaymentMethod->Description);
		}

		return $result;
	}

	private 

	function get_url()
	{
		return $this->url;
	}
}
