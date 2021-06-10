User
- Meta User Address
- Meta User Payment

Shoppping Session : Transient

Cart
- Session 
- Product
- QTY

Order Item
-- Order Detail ( User, Payment )
-- 

'order_key'            => '_order_key',
			'customer_id'          => '_customer_user',
			'payment_method'       => '_payment_method',
			'payment_method_title' => '_payment_method_title',
			'transaction_id'       => '_transaction_id',
			'customer_ip_address'  => '_customer_ip_address',
			'customer_user_agent'  => '_customer_user_agent',
			'created_via'          => '_created_via',
			'date_completed'       => '_date_completed',
			'date_paid'            => '_date_paid',
			'cart_hash'            => '_cart_hash',

			'billing_index'        => '_billing_address_index',
			'billing_first_name'   => '_billing_first_name',
			'billing_last_name'    => '_billing_last_name',
			'billing_company'      => '_billing_company',
			'billing_address_1'    => '_billing_address_1',
			'billing_address_2'    => '_billing_address_2',
			'billing_city'         => '_billing_city',
			'billing_state'        => '_billing_state',
			'billing_postcode'     => '_billing_postcode',
			'billing_country'      => '_billing_country',
			'billing_email'        => '_billing_email',
			'billing_phone'        => '_billing_phone',

			'shipping_index'       => '_shipping_address_index',
			'shipping_first_name'  => '_shipping_first_name',
			'shipping_last_name'   => '_shipping_last_name',
			'shipping_company'     => '_shipping_company',
			'shipping_address_1'   => '_shipping_address_1',
			'shipping_address_2'   => '_shipping_address_2',
			'shipping_city'        => '_shipping_city',
			'shipping_state'       => '_shipping_state',
			'shipping_postcode'    => '_shipping_postcode',
			'shipping_country'     => '_shipping_country',

			'discount_total'       => '_cart_discount',
			'discount_tax'         => '_cart_discount_tax',
			'shipping_total'       => '_order_shipping',
			'shipping_tax'         => '_order_shipping_tax',
			'cart_tax'             => '_order_tax',
			'total'                => '_order_total',

			'version'              => '_order_version',
			'currency'             => '_order_currency',
			'prices_include_tax'   => '_prices_include_tax',

			'amount'               => '_refund_amount',
			'reason'               => '_refund_reason',
			'refunded_by'          => '_refunded_by',

      CREATE TABLE {$table} (
				order_id BIGINT UNSIGNED NOT NULL COMMENT 'Order post ID',
				order_key varchar(100) DEFAULT NULL COMMENT 'Unique order key',
				customer_id BIGINT UNSIGNED NOT NULL COMMENT 'Customer ID. Will be 0 for guests.',
				billing_index varchar(255) DEFAULT NULL COMMENT 'Billing fields, concatenated for search',
				billing_first_name varchar(100) DEFAULT NULL COMMENT 'Billing first name',
				billing_last_name varchar(100) DEFAULT NULL COMMENT 'Billing last name',
				billing_company varchar(100) DEFAULT NULL COMMENT 'Billing company',
				billing_address_1 varchar(255) DEFAULT NULL COMMENT 'Billing street address',
				billing_address_2 varchar(200) DEFAULT NULL COMMENT 'Billing extended address',
				billing_city varchar(100) DEFAULT NULL COMMENT 'Billing city/locality',
				billing_state varchar(100) DEFAULT NULL COMMENT 'Billing state/province/locale',
				billing_postcode varchar(20) DEFAULT NULL COMMENT 'Billing postal code',
				billing_country char(2) DEFAULT NULL COMMENT 'Billing country (ISO 3166-1 Alpha-2)',
				billing_email varchar(200) NOT NULL COMMENT 'Billing email address',
				billing_phone varchar(200) DEFAULT NULL COMMENT 'Billing phone number',
				shipping_index varchar(255) DEFAULT NULL COMMENT 'Shipping fields, concatenated for search',
				shipping_first_name varchar(100) DEFAULT NULL COMMENT 'Shipping first name',
				shipping_last_name varchar(100) DEFAULT NULL COMMENT 'Shipping last name',
				shipping_company varchar(100) DEFAULT NULL COMMENT 'Shipping company',
				shipping_address_1 varchar(255) DEFAULT NULL COMMENT 'Shipping street address',
				shipping_address_2 varchar(200) DEFAULT NULL COMMENT 'Shipping extended address',
				shipping_city varchar(100) DEFAULT NULL COMMENT 'Shipping city/locality',
				shipping_state varchar(100) DEFAULT NULL COMMENT 'Shipping state/province/locale',
				shipping_postcode varchar(20) DEFAULT NULL COMMENT 'Shipping postal code',
				shipping_country char(2) DEFAULT NULL COMMENT 'Shipping country (ISO 3166-1 Alpha-2)',
				payment_method varchar(100) DEFAULT NULL COMMENT 'Payment method ID',
				payment_method_title varchar(100) DEFAULT NULL COMMENT 'Payment method title',
				discount_total varchar(100) NOT NULL DEFAULT 0 COMMENT 'Discount total',
				discount_tax varchar(100) NOT NULL DEFAULT 0 COMMENT 'Discount tax',
				shipping_total varchar(100) NOT NULL DEFAULT 0 COMMENT 'Shipping total',
				shipping_tax varchar(100) NOT NULL DEFAULT 0 COMMENT 'Shipping tax',
				cart_tax varchar(100) NOT NULL DEFAULT 0 COMMENT 'Cart tax',
				total varchar(100) NOT NULL DEFAULT 0 COMMENT 'Order total',
				version varchar(16) NOT NULL COMMENT 'Version of WooCommerce when the order was made',
				currency char(3) NOT NULL COMMENT 'Currency the order was created with',
				prices_include_tax varchar(3) NOT NULL COMMENT 'Did the prices include tax during checkout?',
				transaction_id varchar(200) NOT NULL COMMENT 'Unique transaction ID',
				customer_ip_address varchar(40) DEFAULT NULL COMMENT 'The customer\'s IP address',
				customer_user_agent text DEFAULT NULL COMMENT 'The customer\'s User-Agent string',
				created_via varchar(200) NOT NULL COMMENT 'Order creation method',
				date_completed varchar(20) DEFAULT NULL COMMENT 'Date the order was completed',
				date_paid varchar(20) DEFAULT NULL COMMENT 'Date the order was paid',
				cart_hash varchar(32) DEFAULT NULL COMMENT 'Hash of cart items to ensure orders are not modified',
				amount varchar(100) DEFAULT NULL COMMENT 'The refund amount',
				refunded_by BIGINT UNSIGNED DEFAULT NULL COMMENT 'The ID of the user who issued the refund',
				reason text DEFAULT NULL COMMENT 'The reason for the refund being issued',
			PRIMARY KEY  (order_id),
			UNIQUE KEY `order_key` (`order_key`),
			KEY `customer_id` (`customer_id`),
			KEY `order_total` (`total`)
			) $collate;