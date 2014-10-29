<?php

class Address {

	public $name;
	public $lastName;
	public $address1;
	public $address2;
	public $zipcode;
	public $city;
	public $state;
	public $country;
	public $countryName;
	public $companyName;
	public $phone;

	public function getFields() {
		return array(
			'company_name' => $this->companyName,
			'name' => $this->name,
			'address1' => $this->address1,
			'address2' => $this->address2,
			'city' => $this->city,
			'state' => $this->state,
			'zipcode' => $this->zipcode,
			'country_name' => $this->countryName
		);
	}

}

?>