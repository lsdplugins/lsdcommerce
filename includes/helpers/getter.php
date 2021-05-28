<?php
use LSDCommerce\Common\i18n;

function lsdc_get_country( string $data = 'iso2' )
{
  $country = i18n::get_countries();
  return 'id';
}