<?php
/**
 * ALIPAY API: alipay.data.bill.downloadurl.get request
 * 
 * @author auto create
 * @since 1.0, 2013-04-27 20:19:20
 */
class AlipayDataBillDownloadurlGetRequest
{
	/** 
	 * 账单时间：日账单格式为yyyy-MM-dd,月账单格式为yyyy-MM
	 **/
	private $billDate;
	
	/** 
	 * 账单类型，目前支持的类型有：air
	 **/
	private $billType;
	
	private $apiParas = array();
	
	public function setBillDate($billDate)
	{
		$this->billDate = $billDate;
		$this->apiParas["bill_date"] = $billDate;
	}

	public function getBillDate()
	{
		return $this->billDate;
	}

	public function setBillType($billType)
	{
		$this->billType = $billType;
		$this->apiParas["bill_type"] = $billType;
	}

	public function getBillType()
	{
		return $this->billType;
	}

	public function getApiMethodName()
	{
		return "alipay.data.bill.downloadurl.get";
	}
	
	public function getApiParas()
	{
		return $this->apiParas;
	}
}
