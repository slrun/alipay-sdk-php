<?php
/**
 * ALIPAY API: alipay.gotone.todo.search request
 * 
 * @author auto create
 * @since 1.0, 2013-07-08 18:28:24
 */
class AlipayGotoneTodoSearchRequest
{
	/** 
	 * 终端类型，如Android，iPhone，iPad.
	 **/
	private $terminal;
	
	/** 
	 * 要查询的支付宝账户id
	 **/
	private $userId;
	
	private $apiParas = array();
	
	public function setTerminal($terminal)
	{
		$this->terminal = $terminal;
		$this->apiParas["terminal"] = $terminal;
	}

	public function getTerminal()
	{
		return $this->terminal;
	}

	public function setUserId($userId)
	{
		$this->userId = $userId;
		$this->apiParas["user_id"] = $userId;
	}

	public function getUserId()
	{
		return $this->userId;
	}

	public function getApiMethodName()
	{
		return "alipay.gotone.todo.search";
	}
	
	public function getApiParas()
	{
		return $this->apiParas;
	}
}
