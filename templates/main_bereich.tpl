<form action='{$dlg['phpself']}' method='post'>
    <div id='bereich'>
    {$dialog['bereich'][1]}

        <span id='bearbbtn'>
                <input title="{$dialog['sstring'][2]}"
                type='text'
                name='sstring'
                value="{$dialog['sstring'][1]}"
                onfocus="if(this.value=='{$dialog['sstring'][1]}'){literal}{this.value='';}{/literal}"
                />
                <input
                type='hidden'
                name='sektion'
                value='{$sektion}'
                />
                <input
                type='hidden'
                name='aktion'
                value='search'
                />
        </span>
    </div>
</form>
<div id="picklist">{if !empty($dialog['picklist'])}{$dialog['picklist'][1]}{/if}</div>