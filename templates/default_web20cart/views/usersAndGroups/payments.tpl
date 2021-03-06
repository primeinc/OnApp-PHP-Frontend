{include file="default_web20cart/views/header.tpl"}

     {if $payment_obj == null}
        <p class="not_found">No payments found</p>
     {else}
    <table class="table_my" cellpadding="0" cellspacing="0" border="0">
        <tr>
            <th>{'PAYMENT_DATE'|onapp_string}</th>
            <th>{'INVOICE_NUMBER'|onapp_string}</th>
            <th>{'AMOUNT_'|onapp_string}</th>
            <th></th>

        </tr>
     
     {foreach from=$payment_obj item=payment}
        <tr>
            <td>
               {str_replace(array('T', 'Z'), ' ', $payment->_created_at)}
            </td>
            <td>
                {$payment->_invoice_number}
            </td>
            <td>
                {$payment->_amount|onapp_format_money} {$billing_plan_obj->_currency_code}
            </td>
            <td class="dark_td">
                <a href="{$_ALIASES["users_and_groups"]}?action=payment_edit&amp;id={$payment->_id}&amp;user_id={$user_id}">
                    <img title="{'EDIT_PAYMENT'|onapp_string}" src="templates/{$smarty.const.ONAPP_TEMPLATE}/images/edit.png" />
                </a>
                <a href="{$_ALIASES["users_and_groups"]}?action=payment_delete&amp;id={$payment->_id}&amp;user_id={$user_id}">
                    <img title="{'DESTROY_PAYMENT'|onapp_string}" src="templates/{$smarty.const.ONAPP_TEMPLATE}/images/delete_icon.png" />
                </a>
            </td>
        </tr>
     {/foreach}
    </table>
     {/if}
        
        <form style="float:right" action='{$_ALIASES["users_and_groups"]}' method="post">
            <input type="hidden" name = "id" value="{$user_id}" />
            <input type="hidden" name = "action" value="payment_create" />
            <input type="submit" value="{'ADD_NEW_PAYMENT'|onapp_string}" />
        </form>
    
        
    
{include file="default_web20cart/views/usersAndGroups/navigation.tpl"}
{include file="default_web20cart/views/navigation.tpl"}
{include file="default_web20cart/views/footer.tpl"}