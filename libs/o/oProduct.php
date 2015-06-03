<?php

namespace o;

class oProduct {

    /**
     * @var int
     */
    var $id = null;

    /**
     * @var iRestConnector
     */
    private static $connector;
    private $store_id;
    private $id_in_store;

    var $skynet_status;
    var $skynet_created;
    var $skynet_modified;


    var $properties = array(
        'id' => array(
            'name' => 'i Id',
            'field' => 'id',
            'type' => '',
            'group' => 'skynet',
            'writable' => 'n/a',
        ),
        'store_id' => array(
            'name' => 'Store Id',
            'field' => 'store_id',
            'type' => '',
            'group' => 'skynet',
            'writable' => 'n/a',
        ),
        'id_in_store' => array(
            'name' => 'Id in Store',
            'field' => 'id_in_store',
            'type' => '',
            'group' => 'skynet',
            'writable' => 'n/a',
        ),
        'skynet_created' => array(
            'name' => 'Created (i)',
            'field' => 'skynet_created',
            'type' => '',
            'group' => 'skynet',
            'writable' => 'n/a',
        ),
        'skynet_modified' => array(
            'name' => 'Modified (i)',
            'field' => 'skynet_modified',
            'type' => '',
            'group' => 'skynet',
            'writable' => 'n/a',
        ),
        'code' => array(
            'name' => 'Code',
            'field' => 'code',
            'type' => 'basic',
            'group' => '',
            'writable' => true,
        ),
        'description' => array(
            'name' => 'Description',
            'field' => 'description',
            'type' => 'basic',
            'group' => '',
            'writable' => true,
        ),
        'flags_current' => array(
            'name' => 'Current (flag)',
            'field' => 'flags_current',
            'type' => 'basic',
            'group' => 'flags',
            'writable' => true,
        ),
        'flags_inventoried' => array(
            'name' => 'Inventoried (flag)',
            'field' => 'flags_inventoried',
            'type' => 'basic',
            'group' => 'flags',
            'writable' => true,
        ),
        'flags_editable_sell' => array(
            'name' => 'Editable Sell (flag)',
            'field' => 'flags_editable_sell',
            'type' => 'basic',
            'group' => 'flags',
            'writable' => true,
        ),
        'flags_master_model' => array(
            'name' => 'Master Model (flag)',
            'field' => 'flags_master_model',
            'type' => 'basic',
            'group' => 'flags',
            'writable' => false,
        ),
        'sell_price' => array(
            'name' => 'Sell Price',
            'field' => 'sell_price',
            'type' => 'basic',
            'group' => '',
            'writable' => false,
        ),
        'inventory_available' => array(
            'name' => 'Inventory: available',
            'field' => 'inventory_available',
            'type' => 'basic',
            'group' => 'inventory'
        ),
        'inventory_reserved' => array(
            'name' => 'Inventory: reserved',
            'field' => 'inventory_reserved',
            'type' => 'basic',
            'group' => 'inventory',
            'writable' => false,
        ),
        'inventory_coming_for_stock' => array(
            'name' => 'Inventory: coming for stock',
            'field' => 'inventory_coming_for_stock',
            'type' => 'basic',
            'group' => 'inventory',
            'writable' => false,
        ),
        'inventory_coming_for_customer' => array(
            'name' => 'Inventory: coming for customer',
            'field' => 'inventory_coming_for_customer',
            'type' => 'basic',
            'group' => 'inventory',
            'writable' => false,
        ),
        'inventory_warehouses' => array(
            'name' => 'Inventory: warehouses',
            'field' => 'inventory_warehouses',
            'type' => 'basic',
            'group' => 'inventory',
            'writable' => false,
        ),
        'inventory_in_transit' => array(
            'name' => 'Inventory: in transit',
            'field' => 'inventory_in_transit',
            'type' => 'basic',
            'group' => 'inventory',
            'writable' => false,
        ),
        'inventory_total' => array(
            'name' => 'Inventory: total',
            'field' => 'inventory_total',
            'type' => 'basic',
            'group' => 'inventory',
            'writable' => false,
        ),
        'cost' => array(
            'name' => 'Cost',
            'field' => 'cost',
            'type' => 'advanced',
            'group' => '',
            'writable' => true,
        ),
       'flags_editable' => array(
            'name' => 'Editable (flag)',
            'field' => 'flags_editable',
            'type' => 'advanced',
            'group' => 'flags',
            'writable' => true,
        ),
       'flags_gift_card' => array(
            'name' => 'Gift card (flag)',
            'field' => 'flags_gift_card',
            'type' => 'advanced',
            'group' => 'flags',
            'writable' => true,
        ),
       'flags_new_cost' => array(
            'name' => 'New cost (flag)',
            'field' => 'flags_new_cost',
            'type' => 'advanced',
            'group' => 'flags',
            'writable' => false,
        ),
        'flags_new_import' => array(
            'name' => 'New import (flag)',
            'field' => 'flags_new_import',
            'type' => 'advanced',
            'group' => 'flags',
            'writable' => false,
        ),
        'flags_new_update' => array(
            'name' => 'New update (flag)',
            'field' => 'flags_new_update',
            'type' => 'advanced',
            'group' => 'flags',
            'writable' => false,
        ),
        'flags_no_live_rules' => array(
            'name' => 'No live rules (flag)',
            'field' => 'flags_no_live_rules',
            'type' => 'advanced',
            'group' => 'flags',
            'writable' => false,
        ),
        'flags_no_profit' => array(
            'name' => 'No profit (flag)',
            'field' => 'flags_no_profit',
            'type' => 'advanced',
            'group' => 'flags',
            'writable' => true,
        ),
        'flags_serialized' => array(
            'name' => 'Serialized (flag)',
            'field' => 'flags_serialized',
            'type' => 'advanced',
            'group' => 'flags',
            'writable' => true,
        ),
        'flags_web' => array(
            'name' => 'Web (flag)',
            'field' => 'flags_web',
            'type' => 'advanced',
            'group' => 'flags',
            'writable' => true,
        ),
        'long_web_description' => array(
            'name' => 'Long web description',
            'field' => 'long_web_description',
            'type' => 'advanced',
            'group' => '',
            'writable' => false,
        ),
        'family' => array(
            'name' => 'Family',
            'field' => 'family',
            'type' => 'advanced',
            'group' => '',
            'writable' => false,
        ),
        'margin' => array(
            'name' => 'Margin',
            'field' => 'margin',
            'type' => 'advanced',
            'group' => '',
            'writable' => false,
        ),
        'minimum_margin' => array(
            'name' => 'Minimum margin',
            'field' => 'minimum_margin',
            'type' => 'advanced',
            'group' => '',
            'writable' => false,
        ),
        'product_info_color' => array(
            'name' => 'Color (product info)',
            'field' => 'product_info_color',
            'type' => 'advanced',
            'group' => 'product info',
            'writable' => false,
        ),
        'product_info_height' => array(
            'name' => 'Height (product info)',
            'field' => 'product_info_height',
            'type' => 'advanced',
            'group' => 'product info',
            'writable' => false,
        ),
        'product_info_length' => array(
            'name' => 'Length (product info)',
            'field' => 'product_info_length',
            'type' => 'advanced',
            'group' => 'product info',
            'writable' => false,
        ),
        'product_info_size' => array(
            'name' => 'Size (product info)',
            'field' => 'product_info_size',
            'type' => 'advanced',
            'group' => 'product info',
            'writable' => false,
        ),
        'product_info_weight' => array(
            'name' => 'Weight (product info)',
            'field' => 'product_info_weight',
            'type' => 'advanced',
            'group' => 'product info',
            'writable' => false,
        ),
        'product_info_width' => array(
            'name' => 'Width (product info)',
            'field' => 'product_info_width',
            'type' => 'advanced',
            'group' => 'product info',
            'writable' => false,
        ),
        'product_id' => array(
            'name' => 'Product id',
            'field' => 'product_id',
            'type' => 'advanced',
            'group' => '',
            'writable' => false,
        ),
        'reorder_amount' => array(
            'name' => 'Reorder amount',
            'field' => 'reorder_amount',
            'type' => 'advanced',
            'group' => 'reorder',
            'writable' => false,
        ),
        'reorder_calc' => array(
            'name' => 'Reorder calc',
            'field' => 'reorder_calc',
            'type' => 'advanced',
            'group' => 'reorder',
            'writable' => false,
        ),
        'reorder_point' => array(
            'name' => 'Reorder point',
            'field' => 'reorder_point',
            'type' => 'advanced',
            'group' => 'reorder',
            'writable' => false,
        ),
        'reorder_type' => array(
            'name' => 'Reorder type',
            'field' => 'reorder_type',
            'type' => 'advanced',
            'group' => 'reorder',
            'writable' => false,
        ),
        'sells_sell' => array(
            'name' => 'Sell',
            'field' => 'sells_sell',
            'type' => 'advanced',
            'group' => 'sells',
            'writable' => true,
        ),
        'sells_sell_tax_inclusive' => array(
            'name' => 'Sell tax inclusive',
            'field' => 'sells_sell_tax_inclusive',
            'type' => 'advanced',
            'group' => 'sells',
            'writable' => false,
        ),
        'sells_sell_web' => array(
            'name' => 'Sell web',
            'field' => 'sells_sell_web',
            'type' => 'advanced',
            'group' => 'sells',
            'writable' => false,
        ),
        'supplier' => array(
            'name' => 'Supplier',
            'field' => 'supplier',
            'type' => 'advanced',
            'group' => 'web',
            'writable' => false,
        ),
        'supplier_code' => array(
            'name' => 'Supplier code',
            'field' => 'supplier_code',
            'type' => 'advanced',
            'group' => 'web',
            'writable' => false,
        ),
        'upc' => array(
            'name' => 'UPC',
            'field' => 'upc',
            'type' => 'advanced',
            'group' => 'web',
            'writable' => true,
        ),
        'web_colors' => array(
            'name' => 'UPC',
            'field' => 'Colors (web)',
            'type' => 'advanced',
            'group' => 'web',
            'writable' => false,
        ),
        'web_inventory' => array(
            'name' => 'Inventory (web)',
            'field' => 'web_inventory',
            'type' => 'advanced',
            'group' => 'web',
            'writable' => false,
        ),
        'web_sizes' => array(
            'name' => 'Sizes (web)',
            'field' => 'web_sizes',
            'type' => 'advanced',
            'group' => 'web',
            'writable' => false,
        ),
        'web_keyword1' => array(
            'name' => 'Keyword1 (web)',
            'field' => 'web_keyword1',
            'type' => 'advanced',
            'group' => 'web',
            'writable' => false,
        ),
        'web_keyword2' => array(
            'name' => 'Keyword2 (web)',
            'field' => 'web_keyword2',
            'type' => 'advanced',
            'group' => 'web',
            'writable' => false,
        ),
        'web_keyword3' => array(
            'name' => 'Keyword1 (web)',
            'field' => 'web_keyword3',
            'type' => 'advanced',
            'group' => 'web',
            'writable' => false,
        ),
        'multi_store_label' => array(
            'name' => 'Multi store label',
            'field' => 'multi_store_label',
            'type' => 'advanced',
            'group' => '',
            'writable' => false,
        ),
        'multi_store_master_label' => array(
            'name' => 'Multi store master label',
            'field' => 'multi_store_master_label',
            'type' => 'advanced',
            'group' => '',
            'writable' => false,
        ),
        'related_products' => array(
            'name' => 'Related products',
            'field' => 'related_products',
            'type' => 'advanced',
            'group' => '',
            'writable' => false,
        ),
        'serial_numbers' => array(
            'name' => 'Serial numbers',
            'field' => 'serial_numbers',
            'type' => 'advanced',
            'group' => '',
            'writable' => false,
        ),
        'photo_local_file' => array(
            'name' => 'Photo File',
            'field' => 'photo_local_file',
            'type' => 'advanced',
            'group' => 'skynet',
            'writable' => false,
        ),
        'server_photo_id' => array(
            'name' => 'Photo Id on Server',
            'field' => 'server_photo_id',
            'type' => 'photo',
            'group' => 'skynet',
            'writable' => false,
        ),
        'notes' => array(
            'name' => 'Notes',
            'field' => 'notes',
            'type' => 'advanced',
            'group' => '',
            'writable' => false,
        ),
        'created' => array(
            'name' => 'Created',
            'field' => 'created',
            'type' => 'advanced',
            'group' => '',
            'writable' => false,
        ),
        'modified' => array(
            'name' => 'Modified',
            'field' => 'modified',
            'type' => 'advanced',
            'group' => '',
            'writable' => false,
        ),
        'skynet_updated_quick' => array(
            'name' => 'i Updated Quick',
            'field' => 'skynet_updated_quick',
            'type' => 'basic',
            'group' => 'skynet',
            'writable' => false,
        ),
        'skynet_updated_detailed' => array(
            'name' => 'i Updated Detailed',
            'field' => 'skynet_updated_detailed',
            'type' => 'advanced',
            'group' => 'skynet',
            'writable' => false,
        ),
        'skynet_updated_photo' => array(
            'name' => 'i Updated Photo',
            'field' => 'skynet_updated_photo',
            'type' => 'advanced',
            'group' => 'skynet',
            'writable' => false,
        ),
    );

    var $code;
    var $description;
    var $flags_current;
    var $flags_inventoried;
    var $flags_editable_sell;
    var $flags_master_model;
    var $sell_price;
    var $inventory_available;
    var $inventory_reserved;
    var $inventory_coming_for_stock;
    var $inventory_coming_for_customer;
    var $inventory_warehouses;
    var $inventory_in_transit;
    var $inventory_total;

    // These are advanced properties that can be retrieved via /api/product/x
    var $cost = null;
    var $flags_editable = null;
    var $flags_gift_card = null;
    var $flags_new_cost = null;
    var $flags_new_import = null;
    var $flags_new_update = null;
    var $flags_no_live_rules = null;
    var $flags_no_profit = null;
    var $flags_serialized = null;
    var $flags_web = null;
    var $long_web_description = null;
    var $family = null;
    var $margin = null;
    var $minimum_margin = null;
    var $product_info_color = null;
    var $product_info_height = null;
    var $product_info_length = null;
    var $product_info_size = null;
    var $product_info_weight = null;
    var $product_info_width = null;
    var $product_id = null;
    var $reorder_amount = null;
    var $reorder_calc = null;
    var $reorder_point = null;
    var $reorder_type = null;
    var $sells_sell = null;
    var $sells_sell_tax_inclusive = null;
    var $sells_sell_web = null;
    var $supplier = null;
    var $supplier_code = null;
    var $upc = null;
    var $web_colors = null;
    var $web_inventory = null;
    var $web_sizes = null;
    var $web_keyword1 = null;
    var $web_keyword2 = null;
    var $web_keyword3 = null;
    var $multi_store_label = null;
    var $multi_store_master_label = null;
    var $related_products = null;
    var $serial_numbers = null;
    var $photo_local_file = null;
    var $server_photo_id = null;
    var $notes = null;
    var $created = null;
    var $modified = null;
    var $skynet_updated_quick = null;
    var $skynet_updated_detailed = null;
    var $skynet_updated_photo = null;

    /**
     * @param string|int $id_or_code
     * @param null $server
     */
    function __construct($id_or_code=null,$server=null) {

        if (!self::$connector || !self::$connector instanceof iRestConnector) {
            self::$connector = new iRestConnector();
        }

        if (is_numeric($id_or_code)) {
            $this->id = $id_or_code;
            return $this->load($this->id);
        } else if (is_string($id_or_code)) {
            $this->code = $id_or_code;
            if ($server) {
                $this->loadByCodeAndServer($this->code,$server);
            } else {
                error('iProduct::neither id nor code+server pair');
            }
        }

    }


    function delete() {

        if (!$this->id) {
            error('Can\'t delete product that doesn\'t have id');
            return false;
        }

        q('UPDATE products SET skynet_status = "deleted" WHERE id = '.$this->id);

    }




    function create($data=array()) {

        $code = $data['code'] ? $data['code'] : $this->code;
        $store_id = $data['store_id'] ? $data['store_id'] : $this->store_id;
        if (!$code) {
            error("Product::create - code is required");
            return false;
        }
        if (!$store_id) {
            error("Product::create - store_id is required");
            return false;
        }

        $query = <<<HEREDOC
INSERT INTO products
(id,code,store_id)
VALUES
(null,"{$code}","{$store_id}")
HEREDOC;
        $result = q($query);
        if ($result === false) {
            error('iProduct::create() failed');
            return false;
        }


        $this->id = mysql_insert_id();

        $this->skynet_created = getCurrentTime();
        $this->modify($data);

        $this->save();


    return true;
    }





    function createFromServer($id_in_store=null,$store_id=null) {

        if ($id_in_store) {
            $this->id_in_store = $id_in_store;
        }
        if ($store_id) {
            $this->store_id = $store_id;
        }

        if (!$this->id_in_store) {
            error('iProduct::createFromServer($id_in_store): no $id_in_store');
            return false;
        }
        if (!$this->store_id) {
            error('iProduct::createFromServer($store_id): no $store_id');
            return false;
        }

        $xml = self::$connector->runGet('product',$id_in_store);
        $data = xml2array($xml);
        $data = $this->cleanupProductData($data);
        $this->create($data);

    return true;
    }



    function loadFromServer($store_id=null,$id_in_store=null) {
    rec('iProduct::loadFromServer()');

        if ($store_id) {
            $this->store_id = $store_id;
        }

        if (!$this->store_id) {
            error('iProduct::loadFromServer() Set store_id first');
            return false;
        }

        if ($id_in_store) {
            $this->id_in_store = $id_in_store;
        }

        if (!$this->id_in_store) {
            $query = 'SELECT * FROM stores_products WHERE product_id = '.$this->id.' AND store_id = "'.$store_id.'"';
            $result = q($query);
            $product = $result[0];
            $this->id_in_store = $product['product_id_in_store'];
        }

        if (!$this->id_in_store) {
            rec('iProduct::loadFromServer() no id_in_store');
            error('iProduct::loadFromServer(): no id');
            return false;
        }
        rec('iProduct::loadFromServer('.$this->store_id.','.$this->id_in_store.')');

        self::$connector->setServer($this->store_id);
        $xml = self::$connector->runGet('product',$this->id_in_store);
        if ($xml === false) {
            error('Product with code '.$this->code.' was not found on server '.$this->store_id);
            return false;
        }
        $odata = new \SimpleXMLElement($xml);
        @$this->supplier = (string) $odata->supplier->attributes()->id;
        $data = xml2array($xml);
        $data['supplier'] = $this->supplier;



        $data = $this->cleanupProductData($data);

        $query = 'SELECT id from products WHERE code = "'.$data['code'].'" AND store_id = "'.$this->store_id.'"';
        $result = q($query);
        $product_id = @$result[0]['id'];
        if (!$this->id && $product_id) {
            $this->id = $product_id;
        }

        foreach ($data as $k=>$v) {
            $this->$k = $v;
        }

    return true;
    }



    function cleanupProductData($data) {


        unset($data['categories']);
        unset($data['gl_product']);
        unset($data['import_id']);
        unset($data['currency']);
        unset($data['class']);
        unset($data['pricing_levels']);

        $data['created'] = str_replace('T',' ',$data['created']);
        $data['modified'] = str_replace('T',' ',$data['modified']);

        $data['code'] = trim($data['code']);

        $data['cost'] = $data['costs']['cost'];
        unset($data['costs']);
        foreach ($data['flags'] as $k=>$v) {
            $data['flags_'.$k] = $v;
        }
        unset($data['flags']);
        foreach ($data['product_info'] as $k=>$v) {
            $data['product_info_'.$k] = $v;
        }
        unset($data['product_info']);
        foreach ($data['reorder'] as $k=>$v) {
            $data['reorder_'.$k] = $v;
        }
        unset($data['reorder']);
        foreach ($data['web'] as $k=>$v) {
            $data['web_'.$k] = $v;
        }
        unset($data['web']);
        foreach ($data['sells'] as $k=>$v) {
            $data['sells_'.$k] = $v;
        }
        unset($data['sells']);
        foreach ($data['inventory'] as $k=>$v) {
            $data['inventory_'.$k] = $v;
        }
        unset($data['inventory']);
        for ($i = 0; $i <= sizeof($data['keywords'])+1; $i++) {
            $data['web_keyword'.($i+1)] = $data['keywords']['keyword'][$i];
        }
        unset($data['keywords']);

        if ($data['product_photos'] && $data['product_photos']['product_photo']) {
            $data['photo'] = $data['product_photos']['product_photo'];
        }
        unset($data['product_photos']);

    return $data;
    }


    /**
     * @param string $store_id
     * @param int $id_in_store
     * @return null|iProduct
     * @throws oException
     */
    function loadPhotoFromServer($store_id=null,$id_in_store=null) {
    info('iProduct::loadPhotoFromServer()');

        if ($store_id) {
            $this->store_id = $store_id;
        }

        if (!$this->store_id) {
            error('iProduct::loadPhotoFromServer() Set store_id first');
            return false;
        }

        if ($id_in_store) {
            $this->id_in_store = $id_in_store;
        }

        if (!$this->id_in_store) {
            $query = 'SELECT * FROM stores_products WHERE product_id = '.$this->id.' AND store_id = "'.$store_id.'"';
            $result = q($query);
            $product = $result[0];
            $this->id_in_store = $product['product_id_in_store'];
        }

        if (!$this->id_in_store) {
            rec('iProduct::loadPhotoFromServer() no id_in_store');
            error('iProduct::loadPhotoFromServer(): no id');
            return false;
        }
        rec("iProduct::loadPhotoFromServer({$this->store_id},{$this->id_in_store})");

        self::$connector->setServer($this->store_id);
        $xml = self::$connector->runGet('photos',$this->id_in_store);

        if ($xml === false) {
            warning('Could not get photo list for '.$this->code.' on server '.$this->store_id);
            return false;
        }
        $odata = new \SimpleXMLElement($xml);
        $server_photo_id = (string) @$odata->product_photo->attributes()->id;
        rec($server_photo_id);
        $data = xml2array($xml);

        if ( !$data || !$odata || !$server_photo_id ) {
            message("Product %s doesn't have photos on %s",array($this->code,$this->store_id));
            $this->server_photo_id = $server_photo_id;
            $this->save();
            return null;
        } else {
            if ( $server_photo_id != $this->server_photo_id ) {
                message("New photo found for product ".$this->code." replacing");
                $this->server_photo_id = $server_photo_id;
                $this->save();
                $image_data = self::$connector->runGet('photo',$this->id_in_store, $server_photo_id );
                if (!$image_data) {
                    warning("No image data for photo $this->server_photo_id in $this->store_id");
                }
                $this->photo_local_file = $this->writeProductPhoto($image_data);
                $this->save();
                return $this;
            }
            else {
                if (file_exists($this->photo_local_file)) {
                    message("Photo for product ".$this->code." is old and is found in photos dir, skipping");
                    return $this;
                } else {
                    message("Found new photo for ".$this->code."!");
                    $this->server_photo_id = $server_photo_id;
                    rec($this->server_photo_id);
                    $this->save();
                    $image_data = self::$connector->runGet('photo',$this->id_in_store, $this->server_photo_id );
                    if (!$image_data) {
                        warning("No image data for photo $this->server_photo_id in $this->store_id");
                    }
                    $this->photo_local_file = $this->writeProductPhoto($image_data);
                    $this->save();
                    return $this;
                }
            }



        }


    return null;
    }


    function getImageType($image_data) {
        $types = array(
            'jpeg' => "\xFF\xD8\xFF",
            'gif' => 'GIF',
            'png' => "\x89\x50\x4e\x47\x0d\x0a",
            'bmp' => 'BM',
            'psd' => '8BPS',
            'swf' => 'FWS'
        );
        $first8Bytes = substr($image_data, 0, 8);
        $found = 'unknown_format';

        foreach ($types as $type => $header) {
            if (strpos($first8Bytes, $header) === 0) {
                $found = $type;
                break;
            }
        }

    return $found;
    }



    function writeProductPhoto($image_data) {

        $image_type = $this->getImageType($image_data);
        $product_code_sanitized = $this->code;

        // Remove anything which isn't a word, whitespace, number
        // or any of the following caracters -_~,;:[]().
        $product_code_sanitized = preg_replace("([^\w\s\d\-_~,;:\[\]\(\).])", '', $product_code_sanitized);
        // Remove any runs of periods (thanks falstro!)
        $product_code_sanitized = preg_replace("([\.]{2,})", '', $product_code_sanitized);

        $filename = ROOT."photos".DS."products".DS.$this->store_id.DS.$product_code_sanitized.'.'.$image_type;
        file_put_contents($filename, $image_data);
        message("Writing product image to $filename");

    return $filename;
    }



    function modify($data) {


        foreach ($data as $k=>$v) {
            if (property_exists($this,$k)) {
                $this->$k = $v;
            }
        }



        // @todo photo
        /*
        if ($this->photo) {
            $filename = dirname($_SERVER['SCRIPT_FILENAME']).'/photos/'.$this->id.'.png';
            rec('Writing photo data into file '.$filename);
            $binary_data = file_get_contents($this->photo);
            file_put_contents($filename,$binary_data);
        }
        */


    }



    function save() {
    info('iProduct::save()');

        $this->skynet_modified = getCurrentTime();

        if ($this->flags_current == 'false' && $this->inventory_total <= 0) {
            $this->skynet_status = 'hidden';
        } else {
            $this->skynet_status = 'normal';
        }

        $assignments = array();
        foreach ($this as $k=>$v) {
            if ($k == 'photo_id') continue;
            if ($k == 'tags') continue;
            // ffs care here
            if (!is_string($v) || !is_numeric($v)) continue;
            $assignments[] = $k.' = "'.mysql_real_escape_string($v).'"';
        }
        $assignments = implode(', ',$assignments);

        if (!$this->id) {
            $this->create();
        }
        else {
        $query = <<<HEREDOC
UPDATE products SET
$assignments
WHERE id = {$this->id}
HEREDOC;


        if (false === q($query)) {
            error($query);
            return false;
        }

        /*
        if ($this->id_in_store) {
            $query = "DELETE FROM stores_products WHERE product_id = '{$this->id}' AND store_id = '{$this->server}'";
            q($query);
            $query = <<<HEREDOC
INSERT INTO stores_products
(
product_id,product_id_in_store,store_id,photo_id,supplier,
flags_current,flags_inventoried,flags_editable_sell,flags_master_model,
inventory_available,inventory_reserved,inventory_coming_for_stock,inventory_coming_for_customer,inventory_warehouses,inventory_in_transit,inventory_total
)
VALUES
(
"{$this->id}","{$this->id_in_store}","{$this->server}","{$this->photo}","{$this->supplier}",
"{$this->flags_current}","{$this->flags_inventoried}","{$this->flags_editable_sell}","{$this->flags_master_model}",
"{$this->inventory_available}","{$this->inventory_reserved}","{$this->inventory_coming_for_stock}","{$this->inventory_coming_for_customer}","{$this->inventory_warehouses}","{$this->inventory_in_transit}","{$this->inventory_total}"
);
HEREDOC;
            $success = q($query);
            if ($success === false) {
                error($query);
                return false;
            }
        }
        */

        /*
        if (!empty($this->tags)) {
            q('DELETE FROM products_tags WHERE product_id = '.$this->id);
        }
        foreach ($this->tags as $tag) {
            $result = q("SELECT * FROM tags WHERE name = '$tag'");
            if (!empty($result)) {
                $tag_id = $result[0]['id'];
                q("INSERT INTO products_tags (tag_id,product_id) VALUES ($tag_id,{$this->id})");
            }
        }
        */


        }

    return true;
    }



    function load($id=null) {
    rec('iProduct::load()');

        if ($id) {
            $this->id = $id;
        }

        if (!$this->id) {
            error('iProduct::load() -- no id');
            return false;
        }

        $result = q("SELECT * FROM products WHERE id = ".$id);
        if (empty($result)) {
            error('Product with id '.$id.' not found in database');
            prd('Product with id '.$id.' not found in database');
        }
        $product = $result[0];

        foreach ($product as $k=>$v) {
            $this->$k = $v;
        }


        /*
        $result = q('SELECT * FROM products_tags LEFT JOIN tags ON (products_tags.tag_id = tags.id) WHERE product_id = '.$this->id);
        foreach ($result as $row) {
            $this->tags[] = $row['name'];
        }
        */

    }



    function loadByCodeAndServer($code,$server) {
    rec('iProduct::loadByCodeAndServer("%s","%s")', array($code,$server) );

        $q = 'SELECT * FROM products WHERE code = "'.$code.'" AND store_id = "'.$server.'"';
        $result = q($q);
        $product = $result[0];
        if ( !$product ) {
            $this->code = $code;
            $this->store_id = $server;
            warning('Product with code %s from server %s was not found in database', array($code,$server));
            // warning('Product with code %s was not found at server %s', array($code,$server));
        } else {

        }

        foreach ($product as $k=>$v) {
            @$this->$k = $v;
        }



    }


    function __set($property,$value) {

        if (property_exists($this,$property)) {
            $this->$property = $value;
        }
        if ($property == 'id') {
            rec('Working with product id '.$value);
        }
        if ($property == 'code') {
            if ($this->store_id) {
                $this->loadByCodeAndServer($this->code,$this->store_id);
            }
        }
        if ($property == 'store_id') {
            if ($this->code) {
                $this->loadByCodeAndServer($this->code,$this->store_id);
            }
        }

    }









}