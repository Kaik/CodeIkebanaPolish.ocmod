<?php
class ControllerExtensionModuleCodeIkebanaPolish extends Controller
{
    /**
     * property named $error is defined to put errors
     * @var array
     */
    private $error = array();

    // Language
    private $language_pl = array(
        'name' => 'Polski',
        'code' => 'pl',
        'locale' => 'pl-PL,UTF-8',
        'sort_order' => '1',
        'status' => '1'
    );

    private $currency_pl = array(
        'title' => 'Złoty',
        'code' => 'PLN',
        'symbol_left' => '',
        'symbol_right' => 'zł',
        'decimal_place' => '2',
        'value' => '1',
        'status' => '1'
    );

    private $stock_statuses = array(
        '2-3 Days' => '2-3 dni',
        'In Stock' => 'W magazynie',
        'Out Of Stock' => 'Brak w magazynie',
        'Pre-Order' => 'Przedsprzedaż'
    );

    private $order_statuses = array(
        'Canceled' => 'Anulowano',
        'Canceled Reversal' => 'Unieważnienie zwrotu',
        'Chargeback' => 'Zwrot pieniędzy (błąd z przelewem)',
        'Complete' => 'Zrealizowano',
        'Denied' => 'Odrzucono',
        'Expired' => 'Przedawione',
        'Failed' => 'Nie powiodło się',
        'Pending' => 'Przyjęto',
        'Processed' => 'Skompletowane',
        'Processing' => 'W trakcie realizacji',
        'Refunded' => 'Zwrot pieniędzy',
        'Reversed' => 'Odwrócone',
        'Shipped' => 'Wysłano',
        'Voided' => 'Unieważnione'
    );

    private $return_statuses = array(
        'Awaiting Products' => 'Oczekiwanie na produkty',
        'Complete' => 'Zrealizowano',
        'Pending' => 'Przyjęto',
    );

    private $return_action_statuses = array(
        'Credit Issued' => 'Częściowy zwrot pieniędzy',
        'Refunded' => 'Zwrot pieniędzy',
        'Replacement Sent' => 'Wysłano zamiennik',
    );

    private $return_reason_statuses = array(
        'Dead On Arrival' => 'Uszkodzony produkt przy odbiorze',
        'Faulty, please supply details' => 'Błędne zamówienie, proszę opisać szczegóły błędu',
        'Order Error' => 'Błąd zamówienia',
        'Other, please supply details' => 'Inne, proszę opisać powód zwrotu',
        'Received Wrong Item' => 'Otrzymano produkt niezgodny z zamówieniem',
    );

    private $geoZone = array(
        'name' => 'PL VAT',
        'description' => 'PL - Podatek VAT',
        'zone_to_geo_zone' => array(
            array(
                'country_id' => '170',
                'zone_id' => '0'
            )
        )
    );

    private $length_class = array(
        'Centimeter' => 'Centymetr',
        'Inch' => 'Cal',
        'Millimeter' => 'Milimetr'
    );

    private $weight_class = array(
        'Gram' => 'Gram',
        'Kilogram' => 'Kilogram',
        'Ounce' => 'Uncja',
        'Pound' => 'Funt'
    );

    // VAT
    private $tax_23 = array(
        'name' => 'VAT 23%',
        'rate' => 23.0,
        'type' => 'P',
    );

    private $tax_8 = array(
        'name' => 'VAT 8%',
        'rate' => 8,
        'type' => 'P',
    );

    private $tax_5 = array(
        'name' => 'VAT 5%',
        'rate' => 5,
        'type' => 'P',
    );

    private $tax_class_23 = array(
        'title' => 'VAT 23%',
        'description' => 'Podatek VAT 23%',
    );

    private $tax_class_8 = array(
        'title' => 'VAT 8%',
        'description' => 'Podatek VAT 8%',
    );

    private $tax_class_5 = array(
        'title' => 'VAT 5%',
        'description' => 'Podatek VAT 5%',
    );

    public function install()
    {
        // we will save ids in settings for future removeall
        $this->load->model('setting/setting');
        $this->load->model('localisation/language');

        // Language
        $language_id = $this->model_localisation_language->addLanguage($this->language_pl);

        // Currency
        $this->load->model('localisation/currency');
        $currency_id = $this->model_localisation_currency->addCurrency($this->currency_pl);
        $this->model_localisation_currency->refresh(true);

        // Edit stock statuses
        $this->load->model('localisation/stock_status');
        $stock_statuses_query = $this->model_localisation_stock_status->getStockStatuses();
        if (count($stock_statuses_query) && $language_id) {
            foreach ($stock_statuses_query as $result) {
                if (isset($this->stock_statuses[$result['name']])) {
                    $this->db->query("UPDATE " . DB_PREFIX . "stock_status SET name = '" . $this->db->escape($this->stock_statuses[$result['name']]) . "' WHERE stock_status_id = '" . (int) $result['stock_status_id'] . "' AND language_id = '" . (int) $language_id . "'");
                }
            }
        }

        // Edit order statuses
        $this->load->model('localisation/order_status');
        $order_statuses_query = $this->model_localisation_order_status->getOrderStatuses();
        if (count($order_statuses_query) && $language_id) {
            foreach ($order_statuses_query as $result) {
                if (isset($this->order_statuses[$result['name']])) {
                    $this->db->query("UPDATE " . DB_PREFIX . "order_status SET name = '" . $this->db->escape($this->order_statuses[$result['name']]) . "' WHERE order_status_id = '" . (int) $result['order_status_id'] . "' AND language_id = '" . (int) $language_id . "'");
                }
            }
        }

        // Edit return statuses
        $this->load->model('localisation/return_status');
        $return_statuses_query = $this->model_localisation_return_status->getReturnStatuses();
        if (count($return_statuses_query) && $language_id) {
            foreach ($return_statuses_query as $result) {
                if (isset($this->return_statuses[$result['name']])) {
                    $this->db->query("UPDATE " . DB_PREFIX . "return_status SET name = '" . $this->db->escape($this->return_statuses[$result['name']]) . "' WHERE return_status_id = '" . (int) $result['return_status_id'] . "' AND language_id = '" . (int) $language_id . "'");
                }
            }
        }

        // Edit return actions statuses
        $this->load->model('localisation/return_action');
        $return_action_statuses_query = $this->model_localisation_return_action->getReturnActions();
        if (count($return_action_statuses_query) && $language_id) {
            foreach ($return_action_statuses_query as $result) {
                if (isset($this->return_action_statuses[$result['name']])) {
                    $this->db->query("UPDATE " . DB_PREFIX . "return_action SET name = '" . $this->db->escape($this->return_action_statuses[$result['name']]) . "' WHERE return_action_id = '" . (int) $result['return_action_id'] . "' AND language_id = '" . (int) $language_id . "'");
                }
            }
        }

        // Edit return reasons statuses
        $this->load->model('localisation/return_reason');
        $return_reason_statuses_query = $this->model_localisation_return_reason->getReturnReasons();
        if (count($return_reason_statuses_query) && $language_id) {
            foreach ($return_reason_statuses_query as $result) {
                if (isset($this->return_reason_statuses[$result['name']])) {
                    $this->db->query("UPDATE " . DB_PREFIX . "return_reason SET name = '" . $this->db->escape($this->return_reason_statuses[$result['name']]) . "' WHERE return_reason_id = '" . (int) $result['return_reason_id'] . "' AND language_id = '" . (int) $language_id . "'");
                }
            }
        }

        // Edit length class
        $this->load->model('localisation/length_class');
        $length_class_query = $this->model_localisation_length_class->getLengthClasses();
        if (count($length_class_query) && $language_id) {
            foreach ($length_class_query as $result) {
                if (isset($this->length_class[$result['title']])) {
                    $this->db->query("UPDATE " . DB_PREFIX . "length_class_description SET title = '" . $this->db->escape($this->length_class[$result['title']]) . "' WHERE length_class_id = '" . (int) $result['length_class_id'] . "' AND language_id = '" . (int) $language_id . "'");
                }
            }
        }

        // Edit weight class
        $this->load->model('localisation/weight_class');
        $weight_class_query = $this->model_localisation_weight_class->getWeightClasses();
        if (count($weight_class_query) && $language_id) {
            foreach ($weight_class_query as $result) {
                if (isset($this->weight_class[$result['title']])) {
                    $this->db->query("UPDATE " . DB_PREFIX . "weight_class_description SET title = '" . $this->db->escape($this->weight_class[$result['title']]) . "' WHERE weight_class_id = '" . (int) $result['weight_class_id'] . "' AND language_id = '" . (int) $language_id . "'");
                }
            }
        }

        // Add geo zone
        if ($language_id) {
            $this->load->model('localisation/geo_zone');
            $geo_zone_id = $this->model_localisation_geo_zone->addGeoZone($this->geoZone);
        }

        // Add tax classes and rates
        if ($geo_zone_id) {
            // Add tax rates
            $this->load->model('localisation/tax_rate');
            // Add 23
            $this->tax_23['geo_zone_id'] = $geo_zone_id;
            $this->tax_23['tax_rate_customer_group'] = array($this->config->get('config_customer_group_id'));
            $tax_rate_23_id = $this->model_localisation_tax_rate->addTaxRate($this->tax_23);
            // Add 8
            $this->tax_8['geo_zone_id'] = $geo_zone_id;
            $this->tax_8['tax_rate_customer_group'] = array($this->config->get('config_customer_group_id'));
            $tax_rate_8_id = $this->model_localisation_tax_rate->addTaxRate($this->tax_8);
            // Add 5
            $this->tax_5['geo_zone_id'] = $geo_zone_id;
            $this->tax_5['tax_rate_customer_group'] = array($this->config->get('config_customer_group_id'));
            $tax_rate_5_id = $this->model_localisation_tax_rate->addTaxRate($this->tax_5);

            // Add tax classes
            $this->load->model('localisation/tax_class');
            // 23
            $this->tax_class_23['tax_rule'] = array(
                array(
                    'tax_rate_id' => $tax_rate_23_id,
                    'based' => 'shipping',
                    'priority' => '0'
                )
            );
            $tax_class_23_id = $this->model_localisation_tax_class->addTaxClass($this->tax_class_23);
            // 8
            $this->tax_class_8['tax_rule'] = array(
                array(
                    'tax_rate_id' => $tax_rate_8_id,
                    'based' => 'shipping',
                    'priority' => '0'
                )
            );
            $tax_class_8_id = $this->model_localisation_tax_class->addTaxClass($this->tax_class_8);
            // 5
            $this->tax_class_5['tax_rule'] = array(
                array(
                    'tax_rate_id' => $tax_rate_5_id,
                    'based' => 'shipping',
                    'priority' => '0'
                )
            );
            $tax_class_5_id = $this->model_localisation_tax_class->addTaxClass($this->tax_class_5);
        }

        $toSaveData = ['codeikebana_polish_language_id'     => $language_id,
                       'codeikebana_polish_currency_id'     => $currency_id,
                       'codeikebana_polish_geo_zone_id'     => $geo_zone_id,
                       'codeikebana_polish_tax_rate_23_id'  => $tax_rate_23_id,
                       'codeikebana_polish_tax_rate_8_id'   => $tax_rate_8_id,
                       'codeikebana_polish_tax_rate_5_id'   => $tax_rate_5_id,
                       'codeikebana_polish_tax_class_23_id' => $tax_class_23_id,
                       'codeikebana_polish_tax_class_8_id'  => $tax_class_8_id,
                       'codeikebana_polish_tax_class_5_id'  => $tax_class_5_id
        ];

        $this->model_setting_setting->editSetting('codeikebana_polish', $toSaveData);
    }

    public function uninstall()
    {
        // retrive saved id's
        $this->load->model('setting/setting');
        $module_settings = $this->model_setting_setting->getSetting('codeikebana_polish');
        // Language
        $this->load->model('localisation/language');
        $this->model_localisation_language->deleteLanguage($module_settings['codeikebana_polish_language_id']);

        // Currency
        $this->load->model('localisation/currency');
        $this->model_localisation_currency->deleteCurrency($module_settings['codeikebana_polish_currency_id']);

        // reveresed order tax class - tax rate - geo zone

        // tax classes
        $this->load->model('localisation/tax_class');
        // 23
        $this->model_localisation_tax_class->deleteTaxClass($module_settings['codeikebana_polish_tax_class_23_id']);
        // 8
        $this->model_localisation_tax_class->deleteTaxClass($module_settings['codeikebana_polish_tax_class_8_id']);
        // 5
        $this->model_localisation_tax_class->deleteTaxClass($module_settings['codeikebana_polish_tax_class_5_id']);

        // tax rates
        $this->load->model('localisation/tax_rate');
        // 23
        $this->model_localisation_tax_rate->deleteTaxRate($module_settings['codeikebana_polish_tax_rate_23_id']);
        // 8
        $this->model_localisation_tax_rate->deleteTaxRate($module_settings['codeikebana_polish_tax_rate_8_id']);
        // 5
        $this->model_localisation_tax_rate->deleteTaxRate($module_settings['codeikebana_polish_tax_rate_5_id']);

        // Add geo zone
        $this->load->model('localisation/geo_zone');
        $this->model_localisation_geo_zone->deleteGeoZone($module_settings['codeikebana_polish_geo_zone_id']);

        // remove settings
        $this->model_setting_setting->deleteSetting('codeikebana_polish');

        //done! everything else will be deleted automatically during save
    }
}
