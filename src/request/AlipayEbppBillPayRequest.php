<?php
/**
 * ALIPAY API: alipay.ebpp.bill.pay request
 * 
 * @author auto create
 * @since 1.0, 2013-04-28 22:40:00
 */
class AlipayEbppBillPayRequest
{
	/** 
	 * 支付宝的业务订单号，具有唯一性。
	 **/
	private $alipayOrderNo;
	
	/** 
	 * 输出机构的业务流水号，需要保证唯一性。
	 **/
	private $merchantOrderNo;
	
	/** 
	 * 支付宝订单类型。公共事业缴纳JF,信用卡还款HK
	 **/
	private $orderType;
	
	private $apiParas = array();
	
	public function setAlipayOrderNo($alipayOrderNo)
	{
		$this->alipayOrderNo = $alipayOrderNo;
		$this->apiParas["alipay_order_no"] = $alipayOrderNo;
	}

	public function getAlipayOrderNo()
	{
		return $this->alipayOrderNo;
	}

	public function setMerchantOrderNo($merchantOrderNo)
	{
		$this->merchantOrderNo = $merchantOrderNo;
		$this->apiParas["merchant_order_no"] = $merchantOrderNo;
	}

	public function getMerchantOrderNo()
	{
		return $this->merchantOrderNo;
	}

	public function setOrderType($orderType)
	{
		$this->orderType = $orderType;
		$this->apiParas["order_type"] = $orderType;
	}

	public function getOrderType()
	{
		return $this->orderType;
	}

	public function getApiMethodName()
	{
		return "alipay.ebpp.bill.pay";
	}
	
	public function getApiParas()
	{
		return $this->apiParas;
	}
}
