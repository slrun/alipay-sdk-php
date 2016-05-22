<?php
/**
 * ALIPAY API: alipay.system.oauth.token request
 * 
 * @author auto create
 * @since 1.0, 2013-07-08 10:03:41
 */
class AlipaySystemOauthTokenRequest
{
	/** 
	 * 授权码，用户对应用授权后得到。
	 **/
	private $code;
	
	/** 
	 * 获取访问令牌的类型，authorization_code表示用授权码换，refresh_token表示用刷新令牌来换。
	 **/
	private $grantType;
	
	/** 
	 * 刷新令牌，上次换取访问令牌时得到。
	 **/
	private $refreshToken;
	
	private $apiParas = array();
	
	public function setCode($code)
	{
		$this->code = $code;
		$this->apiParas["code"] = $code;
	}

	public function getCode()
	{
		return $this->code;
	}

	public function setGrantType($grantType)
	{
		$this->grantType = $grantType;
		$this->apiParas["grant_type"] = $grantType;
	}

	public function getGrantType()
	{
		return $this->grantType;
	}

	public function setRefreshToken($refreshToken)
	{
		$this->refreshToken = $refreshToken;
		$this->apiParas["refresh_token"] = $refreshToken;
	}

	public function getRefreshToken()
	{
		return $this->refreshToken;
	}

	public function getApiMethodName()
	{
		return "alipay.system.oauth.token";
	}
	
	public function getApiParas()
	{
		return $this->apiParas;
	}
}
