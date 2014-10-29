<?php

require_once 'item.php';
require_once 'address.php';
require_once 'customer.php';

class Despatch {
	
	public $id;
	public $orderId;
	public $email;
	public $cost;
	public $discount;
	public $tracking;
	public $updatedAt;
	public $shippedAt;
	public $collectPlus;
	public $salesChannel;
	public $shippingAddress = null;
	public $invoiceAddress = null;
	public $items = array();
	public $customer = array();
	/**
	 * Custom fields
	 */
	public $customerUrn;
	public $orderDate;
	public $totalSale;
	public $OAOrderNumber;
	
	/**
	 * 
	 * @return string
	 */
	public function getFields() {
		return array(
			'id' => $this->id,
			'order_id' => $this->orderId,
			'oa_order_number' => $this->OAOrderNumber,
			'customer_urn' => $this->customerUrn,
			'updated_at' => $this->updatedAt,
			'shipped_at' => $this->shippedAt,
			'order_date' => $this->orderDate,
			'email' => $this->email,
			'collect_plus' => $this->collectPlus,
			'cost' => $this->cost,
			'total_sale' => $this->totalSale,
			'discount' => $this->discount,
			'order_date' => $this->orderDate,
			'shipping_address' => $this->shippingAddress,
			'invoice_address' => $this->invoiceAddress,
			'sales_channel' => $this->salesChannel,
			'items' => $this->items
		);
	}
}
?>