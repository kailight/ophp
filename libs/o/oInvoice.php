<?php

class iInvoice {

    var $store_id;
    var $id = null;
    var $id_in_store;
    var $user_id;
    var $total;
    var $flags_drop_shipment;
    var $flags_exported;
    var $flags_pay_backorders;
    var $flags_posted;
    var $flags_voided;
    var $printed_notes;
    var $internal_notes;
    var $shipping_method;
    var $invoice_status;
    var $source;
    var $margin;
    var $created;
    var $customer_id;
    var $billing_address_line;
    var $billing_address_city;
    var $billing_address_state;
    var $billing_address_country;
    var $billing_address_zip;
    var $shipping_address_line;
    var $shipping_address_city;
    var $shipping_address_state;
    var $shipping_address_country;
    var $shipping_address_zip;
    var $print_options_languages;
    var $print_options_images;
    var $print_options_discounts;

    var $properties = array(
        'id',
        'store_id',
        'id_in_store',
        'user_id',
        'total',
        'flags_drop_shipment',
        'flags_exported',
        'flags_pay_backorders',
        'flags_posted',
        'flags_voided',
        'printed_notes',
        'internal_notes',
        'shipping_method',
        'invoice_status',
        'source',
        'margin',
        'created',
        'customer_id',
        'billing_address_line',
        'billing_address_city',
        'billing_address_state',
        'billing_address_country',
        'billing_address_zip',
        'shipping_address_line',
        'shipping_address_city',
        'shipping_address_state',
        'shipping_address_country',
        'shipping_address_zip',
        'print_options_languages',
        'print_options_images',
        'print_options_discounts',
    );
    
    var $origin;
    var $customer;
    var $user;
    var $payments;
    var $items;

    function iInvoice($whatever=null) {
        $this->connector = new iRestConnector();

        if (is_numeric($whatever)) {
            $this->id = $whatever;
            $this->load($whatever);
        } else if (is_array($whatever)) {
            if ($this->search($whatever)) {
                $this->load();
            } else {
                // $this->cleanup($whatever);
                // $this->create($whatever);
            }
        }


    return $this;
    }


    function delete() {

        if ($this->id) {
            skynet\q("DELETE FROM invoices WHERE id = ".$this->id);
        }

    }



    function cleanup($data) {

        // skynet\q("DELETE FROM invoices WHERE store_id = '{$data['store_id']}' AND id_in_store = '{$data['id_in_store']}'");

    }



    function search($data) {

        $conditions = array();
        foreach ( $data as $k=>$item ) {
            if (is_numeric ($item) ) {
                $conditions[] = $k.' = '.$item.'';
            }
            else {
                $conditions[] = $k.' = "'.$item.'"';
            }
        }
        $conditions = implode (' AND ',$conditions);
        $result = skynet\q("SELECT * FROM invoices WHERE $conditions");
        if ($result) {
            $this->id = $result[0]['id'];
            skynet\rec('The invoice was found in the database as #'.$this->id);
            return true;
        }

    return false;
    }


    /**
     * loads payments for the current invoice into $this->payments
     */
    function loadPayments() {

        $this->payments = array();
        $result = skynet\q("SELECT * FROM payments WHERE invoice_id = {$this->id}");
        if ($result) {
            foreach ($result as $row) {
                $this->payments[$row['id']] = $row;
            }
        }

    }


    /**
     * loads items for the current invoice into $this->items
     */
    function loadItems() {

        $this->items = array();
        $result = skynet\q("SELECT * FROM items WHERE invoice_id = {$this->id}");
        if ($result) {
            foreach ($result as $row) {
                $this->items[$row['id']] = $row;
            }
        }

    }


    /**
     * loads customer for the current invoice into $this->customer
     */
    function loadCustomer() {

        $this->customer = array();
        $result = skynet\q("SELECT * FROM customers WHERE invoice_id = {$this->id}");
        if ($result) {
            foreach ($result as $row) {
                $this->customer = $row;
            }
        }

    }


    function loadUser() {

        $this->user = array();
        $result = skynet\q("SELECT * FROM users WHERE users.id_in_store = ".$this->user_id." AND users.store_id = '".$this->store_id."'");
        if ($result) {
            foreach ($result as $row) {
                $this->user = $row;
            }
        }
    }



    function create($data=array()) {
    skynet\rec('iInvoice::create()');

        foreach ($this->properties as $p) {
            $data[$p] = $data[$p] ? $data[$p] : $this->$p;
        }

        $fields = array_keys($data);
        $fields = implode(',',$fields);
        $values = '"'.implode('","',$data).'"';


        $query = <<<HEREDOC
INSERT INTO invoices
($fields)
VALUES
($values)
HEREDOC;

        $result = skynet\q($query);
        if ($result === false) {
            error('iInvoice::create() failed');
            return false;
        }

        $this->id = mysql_insert_id();

        $this->modify($data);
        $this->addUser();

        skynet\rec('iInvoice::create() - created a new invoice with id '.$this->id);

    return true;
    }



    function addPayment($payment = array()) {

        if (!$payment) {
            error('CosnsolightInvoice::addPayment() - no payment data');
            return false;
        }
        if (!$this->id) {
            error('CosnsolightInvoice::addPayment() - this invoice has no id yet');
            return false;
        }

        $result = skynet\q('SELECT * FROM payments WHERE invoice_id = '.$this->id.' AND amount = '.$payment['amount'].' AND created = "'.$payment['created'].'"');
        if ($result) {
            $payment = $result[0];
            $payment_id = $result[0]['id'];
        } else {
            skynet\q("INSERT INTO payments (id,invoice_id,amount,type,created) VALUES (null,{$this->id},'{$payment['amount']}','{$payment['type']}','{$payment['created']}')");
            $payment_id = mysql_insert_id();
        }
        $payment['id'] = $payment_id;

        $this->payments[$payment_id] = $payment;

    }



    function addUser() {
    skynet\error('CosnsolightInvoice::addUser()');

        if ($this->user_id) {
            $result = skynet\q('SELECT * FROM users WHERE store_id = "'.$this->store_id.'" AND id_in_store = '.$this->user_id);
            $user = $result[0];
        } else {
            $user = null;
        }

        $this->user = $user;
    }


    function addCustomer($customer = array()) {
    skynet\rec('CosnsolightInvoice::addCustomer()');

        if (!$customer) {
            skynet\error('CosnsolightInvoice::addCustomer() - no customer data');
            return false;
        }
        if (!$this->id) {
            skynet\error('CosnsolightInvoice::addCustomer() - this invoice has no id yet');
            return false;
        }

        $result = skynet\q('SELECT * FROM customers WHERE invoice_id = '.$this->id.' ');
        if ($result) {
            $customer = $result[0];
            $customer_id = $result[0]['id'];
        } else {
            foreach ($customer as $field=>$value) {
                $fields[] = $field;
                if (!$value) {
                    $values[] = "NULL";
                }
                else {
                    $values[] = "\"$value\"";
                }
            }
            $fields[] = 'invoice_id';
            $values[] = $this->id;
            @array_walk($values,'mysql_real_escape_string');
            $fields = '('.implode(',',$fields).')';
            $values = '('.implode(',',$values).')';
            $query = <<<HEREDOC
INSERT INTO customers $fields VALUES $values
HEREDOC;
            skynet\q($query);
            $customer_id = mysql_insert_id();
        }
        $customer['id'] = $customer_id;


        $this->customer[$customer_id] = $customer;



    }


    function addItem($item = array()) {

        if (!$item) {
            error('CosnsolightInvoice::addItem() - no item data');
            return false;
        }
        if (!$this->id) {
            error('CosnsolightInvoice::addItem() - this invoice has no id yet');
            return false;
        }

        $result = skynet\q('SELECT * FROM items WHERE invoice_id = '.$this->id.' AND code = "'.$item['code'].'" ');
        if ($result) {
            $item = $result[0];
            $item_id = $result[0]['id'];
        } else {
            $query = <<<HEREDOC
INSERT INTO items (id_in_store,invoice_id,code,cost,quantity,discount,quantity_discount,
sells_sell,sells_base,sells_total,editable,taxes,serial_numbers)
 VALUES
 ({$item['id_in_store']},{$this->id},'{$item['code']}','{$item['cost']}','{$item['quantity']}','{$item['discount']}','{$item['quantity_discount']}',
 '{$item['sells_sell']}','{$item['sells_base']}','{$item['sells_total']}','{$item['editable']}','{$item['taxes']}','{$item['serial_numbers']}')
HEREDOC;

            skynet\q($query);
            $item_id = mysql_insert_id();
        }
        $item['id'] = $item_id;


        $this->items[$item_id] = $item;



    }



    function toXML($type='basic') {

        $all_possible_xml = <<<HEREDOC
    <costs>
        <cost>{$this->cost}</cost>
        <average>{$this->cost}</average>
        <raw>{$this->cost}</raw>
    </costs>
    <flags>
        <current>{$this->flags_current}</current>
        <editable>{$this->flags_editable}</editable>
        <gift_card>{$this->flags_gift_card}</gift_card>
        <inventoried>{$this->flags_inventoried}</inventoried>
        <licensed>{$this->flags_licensed}</licensed>
        <new_cost>{$this->flags_new_cost}</new_cost>
        <new_import>{$this->flags_new_import}</new_import>
        <new_update>{$this->flags_new_update}</new_update>
        <no_live_rules>{$this->flags_no_live_rules}</no_live_rules>
        <no_profit>{$this->flags_no_profit}</no_profit>
        <recurring>{$this->flags_recurring}</recurring>
        <serialized>{$this->flags_serialized}</serialized>
        <web>{$this->flags_web}</web>
        <editable_sell>{$this->flags_editable_sell}</editable_sell>
        <master_model>{$this->flags_master_model}</master_model>
    </flags>
    <sell_price>{$this->sell_price}</sell_price>
    <long_web_description>{$this->long_web_description}</long_web_description>
    <margin>{$this->margin}</margin>
    <minimum_margin>{$this->minimum_margin}</minimum_margin>
    <product_info>
        <color>{$this->product_info_color}</color>
        <height>{$this->product_info_height}</height>
        <length>{$this->product_info_length}</length>
        <size>{$this->product_info_size}</size>
        <weight>{$this->product_info_weight}</weight>
        <width>{$this->product_info_width}</width>
    </product_info>
    <reorder>
        <amount>{$this->reorder_amount}</amount>
        <calc>{$this->reorder_calc}</calc>
        <point>{$this->reorder_point}</point>
        <type>{$this->reorder_type}</type>
    </reorder>
    <supplier>{$this->supplier}</supplier>
    <sells>
        <sell>{$this->sells_sell}</sell>
        <sell_tax_inclusive>{$this->sells_sell_tax_inclusive}</sell_tax_inclusive>
        <sell_web>{$this->sells_sell_web}</sell_web>
    </sells>
    <upc>{$this->upc}</upc>
    <web>
        <colors>{$this->web_colors}</colors>
        <inventory>{$this->web_inventory}</inventory>
        <sizes>{$this->web_sizes}</sizes>
    </web>
    <keywords>
        <keyword>{$this->web_keyword1}</keyword>
        <keyword>{$this->web_keyword2}</keyword>
        <keyword>{$this->web_keyword3}</keyword>
    </keywords>
    <multi_store_label>{$this->multi_store_label}</multi_store_label>
    <multi_store_master_label>{$this->multi_store_master_label}</multi_store_master_label>
    <related_products>{$this->related_products}</related_products>
    <serial_numbers>{$this->serial_numbers}</serial_numbers>
HEREDOC;



        $basic_xml = <<<HEREDOC
<product>
    <code>{$this->code}</code>
    <description>{$this->description}</description>
    <upc>{$this->upc}</upc>
    <family>{$this->family}</family>
    <supplier_code>{$this->supplier_code}</supplier_code>
</product>
HEREDOC;



        $advanced_xml = <<<HEREDOC
<product>
    <code>{$this->code}</code>
    <description>{$this->description}</description>
    <costs>
        <cost>{$this->cost}</cost>
        <raw>{$this->cost}</raw>
    </costs>
    <flags>
        <current>{$this->flags_current}</current>
        <editable>{$this->flags_editable}</editable>
        <gift_card>{$this->flags_gift_card}</gift_card>
        <inventoried>{$this->flags_inventoried}</inventoried>
        <no_profit>{$this->flags_no_profit}</no_profit>
        <serialized>{$this->flags_serialized}</serialized>
        <web>{$this->flags_web}</web>
        <editable_sell>{$this->flags_editable_sell}</editable_sell>
    </flags>
    <upc>{$this->upc}</upc>
    <family>{$this->family}</family>
    <supplier_code>{$this->supplier_code}</supplier_code>
    <sells>
        <sell>{$this->sells_sell}</sell>
    </sells>
</product>
HEREDOC;

        if ($type == 'basic') {
            return $basic_xml;
        } else {
            return $advanced_xml;
        }

        return $basic_xml;
    }







    function modify($data) {

        foreach ($data as $k=>$v) {
            if (property_exists($this,$k)) {
                $this->$k = $v;
            }
        }

    }



    function save() {
        skynet\rec('iInvoice::save()');

        if (!$this->id) {
            skynet\rec("Couldn't create invoice: no id");
        }

        $assignments = array();
        foreach ($this->properties as $p) {
            $assignments[] = $p.' = "'.mysql_real_escape_string($this->$v).'"';
        }
        $assignments = implode(', ',$assignments);


        $query = <<<HEREDOC
UPDATE invoices SET
$assignments
WHERE id = {$this->id}
HEREDOC;
        skynet\q($query);

        $this->addUser();
        $this->savePayments();

    }



    function savePayments() {

        skynet\q("DELETE FROM payments WHERE invoice_id = {$this->id}");
        if (!empty($this->payments)) {
            foreach ( $this->payments as $payment ) {
                skynet\q("INSERT INTO payments (id,invoice_id,amount) VALUES (null,$this->id,{$payment['amount']})");
            }
        }

    }



    function load($id=null) {
    skynet\rec('iInvoice::load()');

        if (!$this->id && is_numeric($id)) {
            $this->id = $id;
        }

        $result = skynet\q("SELECT * FROM invoices WHERE id = ".$this->id);
        if (empty($result)) {
            skynet\error('Invoice with id '.$id.' not found in database');
            skynet\prd('Invoice with id '.$id.' not found in database');
        }
        $invoice = $result[0];

        foreach ($invoice as $k=>$v) {
            $this->$k = $v;
        }

        $this->loadCustomer();
        $this->loadPayments();
        $this->loadItems();
        $this->loadUser();
        $this->origin = 'database';

    }




    function __set($property,$value) {
        if (property_exists($this,$property)) {
            $this->$property = $value;
        }
        if ($property == 'id') {
            rec('Working with product id '.$value);
        }
    }









}