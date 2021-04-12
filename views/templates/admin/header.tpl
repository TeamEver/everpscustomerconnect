{*
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
*}

<div class="panel row">
    <div class="col-md-6">
    	<h3><i class="icon icon-smile"></i> {l s='Ever Customer Connect' mod='everpscustomerconnect'}</h3>
    	<img id="everlogo" src="{$evercustomerimage_dir|escape:'htmlall':'UTF-8'}ever.png" style="max-width: 120px;">
    	<p>
    		<strong>{l s='Welcome to Ever Customer Connect module !' mod='everpscustomerconnect'}</strong><br />
    		{l s='Thanks for using Team Ever\'s module' mod='everpscustomerconnect'}.<br />
    		<a href="https://www.team-ever.com/produit/prestashop-ever-ultimate-seo/" target="_blank">{l s='Have you seen this best SEO module for your Prestashop ?' mod='everpscustomerconnect'}</a>
    	</p>
        <p>
            <strong>{l s='How to use this module ?' mod='everpscustomerconnect'}</strong><br />
            {l s='First go to customer page, a link will be shown to connect as customer' mod='everpscustomerconnect'}
        </p>
    </div>
    <div class="col-md-6">
        <p class="alert alert-warning">
            {l s='This module is free and will always be ! You can support our free modules by making a donation by clicking the button below' mod='everpscustomerconnect'}
        </p>
        <form action="https://www.paypal.com/cgi-bin/webscr" method="post" target="_top">
        <input type="hidden" name="cmd" value="_s-xclick" />
        <input type="hidden" name="hosted_button_id" value="3LE8ABFYJKP98" />
        <input type="image" src="https://www.team-ever.com/wp-content/uploads/2019/06/appel_a_dons-1.jpg" border="0" name="submit" title="PayPal - The safer, easier way to pay online!" alt="Bouton Faites un don avec PayPal" />
        <img alt="" border="0" src="https://www.paypal.com/fr_FR/i/scr/pixel.gif" width="1" height="1" />
        </form>
    </div>
</div>
