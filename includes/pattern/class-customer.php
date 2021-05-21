<?php 

Class Customer
{

  function __construct(Customer_Builder $b) {
    $this->first_name = $b->first_name();
    $this->email = $b->email();
    $this->phone = $b->phone();
  }
}
?>
