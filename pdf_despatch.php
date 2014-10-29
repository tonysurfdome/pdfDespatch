<?php

include('../ms-dbfunc.php');
require_once 'json/despatch.php';

class PdfDespatch{
	private $despatch = null;
	private $despatchNumber = null;
	public $despatch_count = 0;

	function __construct() {
		if (null == $this->despatch) {
			$this->despatch = new Despatch();
		}
	}

	public function despatchNumber($despatchNumber)
	{
		$this->despatchNumber = $despatchNumber;
	}

	public function run() {
		return $this->getdata();
	}

	public function getData() {
		$ms_connect = ms_connect();

		$sql = "select 
					s.SalesOrderNumber AS carrierConsignmentNumber,
					s.CustomerPurchaseOrderReferenceNumber AS newMediaOrderNumber,
					s.TotalSale AS OrderGrossValue,
					s.Discount AS DiscountValue,
					s.Email AS PersonalEmail,
					s.ShippingCost as DeliveryGrossValue,
					c.Reference as customerUrn,
					c.Name as shipment_Contact,
					ship.Line1 as shipment_Address1,
					ship.Line2 as shipment_Address2,
					ship.TownCity as shipment_Town,
					sc.Code as shipment_CountryCode,
					ship.PostCode as shipment_postCode,
					sc.Name as shipment_CountryName,
					s.ContactName as billing_ContactName,
					bill.Line1 as billing_Add1,
					bill.Line2 as billing_Add2,
					bill.TownCity as billing_AddTown,
					bill.PostCode as billing_AddPostCode,
					sb.Code as billing_AddCountryCode,
					sb.Name as billing_CountryName,
					si.Line as itemNumber,
					si.SalePrice as ItemUnitPrice,
					it.ItemCode as Sku,
					it.Attribute1 as size,
					si.QuantityOrdered as Quantity, 
					it.Name as itemName,
					s.ShippingCost,
					ship.Region as collectplus,
					s.[ChannelName] as channelname,
					bill.CountryName as b_CountryName,
					ship.CountryName as s_CountryName
				from
					salesorder s
					join
					SalesOrderItem si on (s.SalesOrderId = si.SalesOrderId)
					join
					ItemType it on (si.ItemTypeId = it.ItemTypeId)
					join
					[Address] as ship on (s.ShippingAddressId = ship.AddressId)
					left join 
					Country as sc on (ship.CountryId = sc.CountryId)
					join
					[Address] as bill on (s.invoiceAddressId = bill.AddressId)
					left join
					Country as sb on (bill.CountryId = sb.CountryId)
					left join
					Account as c on (s.CustomerId = c.AccountId)
					where
					s.SalesOrderNumber ='".$this->despatchNumber."'";

		$arr_data = ms_query_all_assoc($sql);
		return $this->process($arr_data);
	}

	public function process($arr_data) {

		foreach ($arr_data as $k => $v) {
			$despatch = null;
			$linecount = count($arr_data[$k]);
			$arr_line_data = $v[0];


			$this->despatch->id = $arr_line_data['carrierConsignmentNumber'];
			$this->despatch->orderId = $arr_line_data['newMediaOrderNumber'];
			//$this->despatch->OAOrderNumber = $arr_line_data['orderNumber'];
			$this->despatch->customerUrn = $arr_line_data['customerUrn'];
			$this->despatch->updatedAt = date('Y-m-d H:i:s');
			$this->despatch->shippedAt = date('Y-m-d H:i:s');
			$this->despatch->orderDate = date('Y-m-d H:i:s');
			$this->despatch->collectPlus = $arr_line_data['collectplus'];
			$this->despatch->salesChannel = $arr_line_data['channelname'];

			$this->despatch->email = $arr_line_data['PersonalEmail'];
			$this->despatch->cost =  (float)$arr_line_data['DeliveryGrossValue'];
			$this->despatch->totalSale = $arr_line_data['OrderGrossValue'];
			$this->despatch->discount = (!empty($arr_line_data['DiscountValue'])) ? $arr_line_data['DiscountValue'] : 0.00;
			//$this->despatch->orderDate =  $arr_line_data['OrderDate'];

			$shippingAddress = new Address();			
			$shippingAddress->companyName = null;
			$shippingAddress->name = $arr_line_data['shipment_Contact'];
			$shippingAddress->address1 =(!empty($arr_line_data['shipment_Address1'])) ? $arr_line_data['shipment_Address1'] : null;
			$shippingAddress->address2 = (!empty($arr_line_data['shipment_Address2'])) ? $arr_line_data['shipment_Address2'] : null;
			$shippingAddress->zipcode = (!empty($arr_line_data['shipment_postCode']))  ? $arr_line_data['shipment_postCode'] : null;
			$shippingAddress->city = (!empty($arr_line_data['shipment_Town']))  ? $arr_line_data['shipment_Town'] : null;
			$shippingAddress->countryName = (isset($arr_line_data['shipment_CountryName']) &&  ! empty($arr_line_data['shipment_CountryName'])) ? $arr_line_data['shipment_CountryName'] : $arr_line_data['s_CountryName'];


			$this->despatch->shippingAddress = $shippingAddress->getFields();

			$invoiceAddress = new Address();
			$invoiceAddress->name = $arr_line_data['billing_ContactName'];
			$invoiceAddress->address1 = (!empty($arr_line_data['billing_Add1']))  ? $arr_line_data['billing_Add1'] : null;
			$invoiceAddress->address2 = (!empty($arr_line_data['billing_Add2']))  ? $arr_line_data['billing_Add2'] : null;
			$invoiceAddress->zipcode = (!empty($arr_line_data['billing_AddPostCode']))  ? $arr_line_data['billing_AddPostCode'] : null;
			$invoiceAddress->city = (!empty($arr_line_data['billing_AddTown']))  ? $arr_line_data['billing_AddTown'] : null;
			$invoiceAddress->countryName = (isset($arr_line_data['billing_CountryName']) && !empty($arr_line_data['billing_CountryName'])) ? $arr_line_data['billing_CountryName'] : $arr_line_data['b_CountryName'];
				
	


			$this->despatch->invoiceAddress = $invoiceAddress->getFields();

			$items = array();
			$i = 0;
			while ($i < $linecount) {

				$arr_line_data = $v[$i];

				$item = new Item();
				$item->sku = $arr_line_data['Sku'];
				$item->name = $arr_line_data['itemName'];
				$item->size = $arr_line_data['size'];
				$item->price = (float)$arr_line_data['ItemUnitPrice'];
				$item->quantity = $arr_line_data['Quantity'];
				$items[] = $item->getFields();
				$i++;
			}

			$this->despatch->items = $items;
			$despatch = $this->despatch->getFields();
			$this->despatch_count= count($despatch);

			return json_encode($despatch);
		}
	}
}