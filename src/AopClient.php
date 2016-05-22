<?php

require_once(__DIR__.'/request/AlipayPointBalanceGetRequest.php');
require_once(__DIR__.'/request/AlipayPointBudgetGetRequest.php');
require_once(__DIR__.'/request/AlipayPointOrderAddRequest.php');
require_once(__DIR__.'/request/AlipayPointOrderGetRequest.php');
require_once(__DIR__.'/request/AlipaySystemOauthTokenRequest.php');

class AopClient {
	//应用ID
	public $appId;
    //私钥文件路径
	public $rsaPrivateKeyFilePath;
    //网关
	public $gatewayUrl = "https://openapi.alipay.com/gateway.do";
	//public $gatewayUrl = "http://openapi.alipaydev.com/gateway.do"; //沙箱
    //返回数据格式
	public $format = "json";
    //api版本
	public $apiVersion = "1.0";
    //签名类型
	protected $signType = "RSA";
	protected $alipaySdkVersion = "alipay-sdk-php-20130320";
	  //终端类型
	protected $terminalType;
	  //终端信息	
	protected $terminalInfo;

	protected function generateSign($params) {
		ksort($params);
		$stringToBeSigned = "";
		$i = 0;
		foreach ($params as $k => $v) {
			if (false === $this->checkEmpty($v) && "@" != substr($v, 0, 1)) {
				if ($i == 0) {
					$stringToBeSigned .= "$k" . "=" . "$v";
				} else {
					$stringToBeSigned .= "&" . "$k" . "=" . "$v";
				}
				$i++;
			}
		}
		unset ($k, $v);
		return $this->sign($stringToBeSigned);
	}

	protected function sign($data) {
		$priKey = file_get_contents($this->rsaPrivateKeyFilePath);
		$res = openssl_pkey_get_private($priKey);
		openssl_sign($data, $sign, $res);
		openssl_free_key($res);
		$sign = base64_encode($sign);
		return $sign;
	}

	protected function curl($url, $postFields = null) {
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_FAILONERROR, false);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

		/* 取消验证证书  begin */
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
		/* 取消验证证书  end */ 
		
		$postBodyString = "";
		if (is_array($postFields) && 0 < count($postFields)) {
			
			$postMultipart = false;
			foreach ($postFields as $k => $v) {
				if ("@" != substr($v, 0, 1)) //判断是不是文件上传
					{
					$postBodyString .= "$k=" . urlencode($v) . "&";
				} else //文件上传用multipart/form-data，否则用www-form-urlencoded
					{
					$postMultipart = true;
				}
			}
			unset ($k, $v);
			curl_setopt($ch, CURLOPT_POST, true);
			if ($postMultipart) {
				curl_setopt($ch, CURLOPT_POSTFIELDS, $postFields);
			} else {
				curl_setopt($ch, CURLOPT_POSTFIELDS, substr($postBodyString, 0, -1));
			}
		}
		$headers = array('content-type: application/x-www-form-urlencoded;charset=UTF-8');	
		curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

		$reponse = curl_exec($ch);
		if (curl_errno($ch)) {
			throw new Exception(curl_error($ch), 0);
		} else {
			$httpStatusCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
			if (200 !== $httpStatusCode) {
				throw new Exception($reponse, $httpStatusCode);
			}
		}
		curl_close($ch);
		return $reponse;
	}

	/*
	protected function logCommunicationError($apiName, $requestUrl, $errorCode, $responseTxt) {
		$localIp = isset ($_SERVER["SERVER_ADDR"]) ? $_SERVER["SERVER_ADDR"] : "CLI";
		$logger = new LtLogger;
		$logger->conf["log_file"] = rtrim(AOP_SDK_WORK_DIR, '\\/') . '/' . "logs/aop_comm_err_" . $this->appId . "_" . date("Y-m-d") . ".log";
		$logger->conf["separator"] = "^_^";
		$logData = array (
			date("Y-m-d H:i:s"),
			$apiName,
			$this->appId,
			$localIp,
			PHP_OS,
			$this->alipaySdkVersion,
			$requestUrl,
			$errorCode,
			str_replace("\n", "", $responseTxt)
		);
		$logger->log($logData);
	}
	*/

	public function execute($request, $authToken = null) {
		//组装系统参数
		$sysParams["app_id"] = $this->appId;
		$sysParams["version"] = $this->apiVersion;
		$sysParams["format"] = $this->format;
		$sysParams["sign_type"] = $this->signType;
		$sysParams["method"] = $request->getApiMethodName();
		$sysParams["timestamp"] = date("Y-m-d H:i:s");
		$sysParams["auth_token"] = $authToken;
		$sysParams["alipay_sdk"] = $this->alipaySdkVersion;
		$sysParams["terminal_type"] = $this->terminalType;
		$sysParams["terminal_info"] = $this->terminalType;		
		//获取业务参数
		$apiParams = $request->getApiParas();

		//签名
		$sysParams["sign"] = $this->generateSign(array_merge($apiParams, $sysParams));
		
		//系统参数放入GET请求串
		$requestUrl = $this->gatewayUrl . "?";
		foreach ($sysParams as $sysParamKey => $sysParamValue) {
			$requestUrl .= "$sysParamKey=" . urlencode($sysParamValue) . "&";
		}
		$requestUrl = substr($requestUrl, 0, -1);
		//发起HTTP请求
		try {
			$resp = $this->curl($requestUrl, $apiParams);
		} catch (Exception $e) {
			//$this->logCommunicationError($sysParams["method"], $requestUrl, "HTTP_ERROR_" . $e->getCode(), $e->getMessage());
			return false;
		}
		//解析AOP返回结果
		$respWellFormed = false;
		if ("json" == $this->format) {
			$respObject = json_decode($resp);

			if (null !== $respObject) {
				$respWellFormed = true;								
			}
		} else
			if ("xml" == $this->format) {
				$respObject = @ simplexml_load_string($resp);
				if (false !== $respObject) {
					$respWellFormed = true;
				}
			}

		//返回的HTTP文本不是标准JSON或者XML，记下错误日志
		if (false === $respWellFormed) {
			//$this->logCommunicationError($sysParams["method"], $requestUrl, "HTTP_RESPONSE_NOT_WELL_FORMED", $resp);
			return false;
		}

		//如果AOP返回了错误码，记录到业务错误日志中
		/*
		if (isset ($respObject->code)) {
			$logger = new LtLogger;
			$logger->conf["log_file"] = rtrim(AOP_SDK_WORK_DIR, '\\/') . '/' . "logs/aop_biz_err_" . $this->appId . "_" . date("Y-m-d") . ".log";
			$logger->log(array (
				date("Y-m-d H:i:s"),
				$resp
			));
		}
		*/

		return $respObject;
	}

	public function exec($paramsArray) {
		if (!isset ($paramsArray["method"])) {
			trigger_error("No api name passed");
		}
		$inflector = new LtInflector;
		$inflector->conf["separator"] = ".";
		$requestClassName = ucfirst($inflector->camelize(substr($paramsArray["method"], 7))) . "Request";
		if (!class_exists($requestClassName)) {
			trigger_error("No such api: " . $paramsArray["method"]);
		}

		$session = isset ($paramsArray["session"]) ? $paramsArray["session"] : null;

		$req = new $requestClassName;
		foreach ($paramsArray as $paraKey => $paraValue) {
			$inflector->conf["separator"] = "_";
			$setterMethodName = $inflector->camelize($paraKey);
			$inflector->conf["separator"] = ".";
			$setterMethodName = "set" . $inflector->camelize($setterMethodName);
			if (method_exists($req, $setterMethodName)) {
				$req-> $setterMethodName ($paraValue);
			}
		}
		return $this->execute($req, $session);
	}
	
	/**
	 * 校验$value是否非空
	 *  if not set ,return true;
	 *	if is null , return true;
	 **/
	protected function checkEmpty($value) {
		if(!isset($value))
			return true ;
		if($value === null )
			return true;
		if(trim($value) === "")
			return true;
		
		return false;
	}
}