<?php
/**
 * 2019-2021 Team Ever
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License (AFL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/afl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 *  @author    Team Ever <https://www.team-ever.com/>
 *  @copyright 2019-2021 Team Ever
 *  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 */

if (!defined('_PS_VERSION_')) {
    exit;
}

class Everpscustomerconnect extends Module
{
    private $html;
    private $postErrors = array();
    private $postSuccess = array();

    public function __construct()
    {
        $this->name = 'everpscustomerconnect';
        $this->tab = 'administration';
        $this->version = '2.1.8';
        $this->author = 'Team Ever';
        $this->need_instance = 0;
        $this->bootstrap = true;
        parent::__construct();
        $this->displayName = $this->l('Ever PS Customer Connect');
        $this->description = $this->l('Allows you to connect on chosen customer account ');
        $this->confirmUninstall = $this->l('');
        $this->ps_versions_compliancy = array('min' => '1.6', 'max' => _PS_VERSION_);
        $this->everToken = Tools::encrypt('everpscustomerconnect/everlogin');
    }

    /**
     * Don't forget to create update methods if needed:
     * http://doc.prestashop.com/display/PS16/Enabling+the+Auto-Update
     */
    public function install()
    {
        return parent::install()
            && $this->registerHook('displayAdminOrderContentOrder')
            && $this->registerHook('displayAdminOrder')
            && $this->registerHook('displayAdminCustomers');
    }

    public function uninstall()
    {
        return parent::uninstall();
    }

    /**
     * Load the configuration page
     */
    public function getContent()
    {
        $this->html = '';
        $link = new Link();
        if (((bool)Tools::isSubmit('submitEverpscustomerconnectModule')) == true) {
            $this->postValidation();

            if (!count($this->postErrors)) {
                $this->postProcess();
            }
        }
        $customer = new Customer(
            (int)Configuration::get('EVERPSCUSTOMERCONNECT_CUST')
        );
        if (Validate::isLoadedObject($customer)) {
            $this->context->smarty->assign(array(
                'login_customer' => $customer,
                'lastname' => $customer->lastname,
                'firstname' => $customer->firstname,
                'base_uri' => __PS_BASE_URI__,
                'login_link' => $link->getModuleLink(
                    'everpscustomerconnect',
                    'everlogin',
                    array(
                        'id_ever_customer' => $customer->id,
                        'evertoken' => $this->everToken
                    )
                )
            ));
        }

        $this->context->smarty->assign(array(
            'evercustomerimage_dir' => $this->_path.'views/img/',
            'ever_token' => $this->everToken,
            'base_uri' => __PS_BASE_URI__,
        ));

        $this->html .= $this->context->smarty->fetch($this->local_path.'views/templates/admin/header.tpl');

        if ($this->checkLatestEverModuleVersion($this->name, $this->version)) {
            $this->html .= $this->context->smarty->fetch($this->local_path.'views/templates/admin/upgrade.tpl');
        }
        $this->html .= $this->context->smarty->fetch($this->local_path.'views/templates/admin/configure.tpl');
        
        $this->html .= $this->context->smarty->fetch($this->local_path.'views/templates/admin/footer.tpl');

        return $this->html;
    }

    public function hookDisplayAdminCustomers($params)
    {
        if (isset($params) && $params['id_customer']) {
            $id_customer = (int)$params['id_customer'];
        } else {
            $order = new Order((int)$params['id_order']);
            $id_customer = (int)$order->id_customer;
        }
        $customer = new Customer(
            $id_customer
        );
        $link = new Link();
        if (Validate::isLoadedObject($customer)) {
            $this->context->smarty->assign(array(
                'login_customer' => $customer,
                'lastname' => $customer->lastname,
                'firstname' => $customer->firstname,
                'base_uri' => __PS_BASE_URI__,
                'login_link' => $link->getModuleLink(
                    'everpscustomerconnect',
                    'everlogin',
                    array(
                        'id_ever_customer' => $customer->id,
                        'evertoken' => $this->everToken
                    )
                )
            ));
        }

        $this->context->smarty->assign(array(
            'evercustomerimage_dir' => $this->_path.'views/img/',
            'ever_token' => $this->everToken,
            'base_uri' => __PS_BASE_URI__,
        ));
        return $this->display(__FILE__, 'views/templates/hook/admin.tpl');
    }

    public function hookDisplayAdminOrder($params)
    {
        return $this->hookDisplayAdminCustomers($params);
    }

    public function checkLatestEverModuleVersion($module, $version)
    {
        $upgrade_link = 'https://upgrade.team-ever.com/upgrade.php?module='
        .$module
        .'&version='
        .$version;
        $handle = curl_init($upgrade_link);
        curl_setopt($handle, CURLOPT_RETURNTRANSFER, true);
        curl_exec($handle);
        $httpCode = curl_getinfo($handle, CURLINFO_HTTP_CODE);
        if ($httpCode != 200) {
            curl_close($handle);
            return false;
        }
        $response = curl_close($handle);
        $module_version = Tools::file_get_contents(
            $upgrade_link
        );
        if ($module_version && $module_version > $version) {
            return true;
        }
        return false;
    }
}
