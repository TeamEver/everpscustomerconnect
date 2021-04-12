{*
* Project : everpscustomerconnect
* @author Team EVER
* @copyright Team EVER
* @license   Tous droits réservés / Le droit d'auteur s'applique (All rights reserved / French copyright law applies)
* @link https://www.team-ever.com
*}
<div class="card col-lg-6 p-3">
    <h3 class="bootstrap cardheader everpscustomerconnect">
        {l s='Connect as this customer' mod='everpscustomerconnect'}
    </h3>
    <div class="bootstrap cardbody everpscustomerconnect">
        <div class="panel-heading">
        {if isset($login_link) && $login_customer}
        <p><a href="{$login_link|escape:'htmlall':'UTF-8'}" target="_blank" class="btn btn-info btn-lg"><strong>{l s='Click here to log as' mod='everpscustomerconnect'} {$login_customer->firstname|escape:'htmlall':'UTF-8'} {$login_customer->lastname|escape:'htmlall':'UTF-8'}</strong></a></p>
        {/if}
        </div>
    </div>
</div>
