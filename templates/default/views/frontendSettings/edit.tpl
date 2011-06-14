{include file="default/views/header.tpl"}

{if isset($error)}
    <div id="onapp_error">
            {$error|onapp_string}
    </div>
{/if}

{if isset($message)}
    <div id="onapp_msg">
            {$message|onapp_string}
    </div>
{/if}

<div style="clear:both;"></div>
<div class="info">

       <div class="info_title">
            {'EDIT_LOG_SETTINGS'|onapp_string}
        </div>

       <div class="info_body">
            {'EDIT_LOG_SETTINGS_INFO'|onapp_string}
        </div>
        <div class="info_bottom"></div>

</div>

<h1>{$title|onapp_string}</h1>
    
<form action='{$smarty.const.ONAPP_BASE_URL}/{$_ALIASES['frontend_settings']}' method="post">
    <div class="div_page">

        <dl>
            <dt class="label">{'BASE_URL'|onapp_string}</dt>
            <dd>
                 <input type="text" name="frontend_settings[base_url]" value="{$smarty.const.ONAPP_BASE_URL}" />      
            </dd>
        </dl>

          <dl>
            <dt class="label">{'HOST_NAME'|onapp_string}</dt>
            <dd>
                 <input type="text" name="frontend_settings[hostname]" value="{$smarty.const.ONAPP_HOSTNAME}"/>
            </dd>
        </dl>
        
        <dl style="clear:both;">
            <dt class="label">{'DEFAULT_ALIAS'|onapp_string}</dt>
            <dd>
                <input type="text" name="frontend_settings[default_alias]" value="{$smarty.const.ONAPP_DEFAULT_ALIAS}" />
            </dd>
        </dl>

        <dl style="clear:both;">
            <dt class="label">{'SECRET_KEY'|onapp_string}</dt>
            
            <dd>
                <input type="text" name="frontend_settings[secret_key]" value="{$smarty.const.ONAPP_SECRET_KEY}" />
            </dd>
        </dl>
        
        <dl style="clear:both;">
            <dt class="label">{'SESSION_LIFETIME'|onapp_string} (seconds)</dt>
            
            <dd>
                <input type="text" name="frontend_settings[session_lifetime]" value="{$smarty.const.ONAPP_SESSION_LIFETIME}" />
            </dd>
        </dl>
        
        <dl>
            <dt class="label">{'DEFAULT_LANGUAGE'|onapp_string}</dt>
            <dd>
                 <select name="frontend_settings[default_language]">
                    {html_options values=$language_list output=$language_list selected=$language_list[$smarty.const.ONAPP_DEFAULT_LANGUAGE]}
                </select>
            </dd>
        </dl>
        
        <dl>
            <dt class="label">{'TEMPLATE_'|onapp_string}</dt>
            <dd>
                 <select name="frontend_settings[template]">
                    {html_options values=$templates_list output=$templates_list selected=$templates_list[$smarty.const.ONAPP_TEMPLATE]}
                </select>
            </dd>
        </dl>
        
        <dl>
            <dt class="label">{'CONTROLLERS_'|onapp_string}</dt>
            <dd>
                 <select name="frontend_settings[controllers]">
                    {html_options values=$controllers_list output=$controllers_list selected=$controllers_list[$smarty.const.ONAPP_CONTROLLERS]}
                </select>
            </dd>
        </dl>
        
        <dl style="clear:both;">
            <dt class="label">{'LOG_DIRECTORY'|onapp_string}</dt>
            
            <dd>
                <input type="text" name="frontend_settings[log_directory]" value="{$smarty.const.ONAPP_LOG_DIRECTORY}" />
            </dd>
        </dl>
       
 </div>
 
    <input type="hidden" name="action" value="save" />
    <input type="submit" name="submit" value="{'SAVE_'|onapp_string}" />

</form>
{include file="default/views/navigation.tpl"}
{include file="default/views/footer.tpl"}