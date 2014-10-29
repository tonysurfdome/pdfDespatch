<?php

/**
 * 
 * @author Orest Hrycyna <orest.hrycyna@surfdome.com>
 */
class Customer {

	public $name;
	public $reference;
	public $firstName;
	public $lastName;
	public $mobile;
	public $phone;
	public $email;

	public function getFields() {
		return array(
			'name' => $this->name,
			'reference' => $this->reference,
			'firstname' => $this->firstName,
			'lastname' => $this->lastName,
			'phone' => $this->phone,
			'mobile' => $this->mobile,
			'email' => $this->email
		);
	}

}