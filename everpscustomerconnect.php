<?php
/**
 * 2019-2022 Team Ever
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
 *  @copyright 2019-2022 Team Ever
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
        $this->version = '2.3.3';
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
            && $this->registerHook('displayAdminOrder')
            && $this->registerHook('actionGetAdminOrderButtons')
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
                        'evertoken' => $this->everToken,
                        'ever_id_cart' => Cart::lastNoneOrderedCart(
                            (int) $customer->id
                        )
                    )
                )
            ));
        }

        $this->context->smarty->assign(array(
            'evercustomerimage_dir' => $this->_path.'views/img/',
            'evertoken' => $this->everToken,
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
        if (isset($params['id_customer']) && $params['id_customer']) {
            $id_customer = (int) $params['id_customer'];
        } else {
            $order = new Order((int) $params['id_order']);
            $id_customer = (int) $order->id_customer;
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
                        'evertoken' => $this->everToken,
                        'ever_id_cart' => Cart::lastNoneOrderedCart($id_customer)
                    )
                )
            ));
        }

        $this->context->smarty->assign(array(
            'evercustomerimage_dir' => $this->_path . 'views/img/',
            'evertoken' => $this->everToken,
            'base_uri' => __PS_BASE_URI__,
        ));
        return $this->display(__FILE__, 'views/templates/hook/admin.tpl');
    }

    public function hookDisplayAdminOrder($params)
    {
        return $this->hookDisplayAdminCustomers($params);
    }

    /**
     * Add buttons to main buttons bar
     */
    public function hookActionGetAdminOrderButtons(array $params)
    {
        $translator = $this->getTranslator();
        $order = new Order($params['id_order']);
        if (Validate::isLoadedObject($order)) {
            $link = new Link();
            $connect_link = $link->getModuleLink(
                'everpscustomerconnect',
                'everlogin',
                array(
                    'id_ever_customer' => $order->id_customer,
                    'evertoken' => $this->everToken,
                    'ever_id_cart' => Cart::lastNoneOrderedCart($order->id_customer)
                )
            );
            /** @var \PrestaShopBundle\Controller\Admin\Sell\Order\ActionsBarButtonsCollection $bar */
            $bar = $params['actions_bar_buttons_collection'];
            $bar->add(
                new \PrestaShopBundle\Controller\Admin\Sell\Order\ActionsBarButton(
                    'btn-info', ['href' => $connect_link, 'target' => '_blank'], $translator->trans('Connect to customer account', [], 'Modules.Cl_pscustomerconnect.Admin')
                )
            );
        }
    }

    public function checkLatestEverModuleVersion($module, $version)
    {
        $upgrade_link = 'https://upgrade.team-ever.com/upgrade.php?module='
        .$module
        .'&version='
        .$version;
        try {
            $handle = curl_init($upgrade_link);
            curl_setopt($handle, CURLOPT_RETURNTRANSFER, true);
            curl_exec($handle);
            $httpCode = curl_getinfo($handle, CURLINFO_HTTP_CODE);
            curl_close($handle);
            if ($httpCode != 200) {
                return false;
            }
            $module_version = Tools::file_get_contents(
                $upgrade_link
            );
            if ($module_version && $module_version > $version) {
                return true;
            }
            return false;
        } catch (Exception $e) {
            PrestaShopLogger::addLog(
                'unable to check Team Ever module upgrade'
            );
            return false;
        }
    }
}
