<div class="list-item list-item-person {if $darkBG}darkBG{/if}">

    <div id='bearbzeile'>
        {* --Name-- *}
        <div id='left' class="fett">
        {if !empty($dialog['pname'][1])}{$dialog['pname'][1]}{/if}
        {if !empty($dialog['aliases'][1])}
            <span class="alias">
                ({foreach $dialog['aliases'][1] as $alias}{$alias}{if $alias@last}){else},&nbsp;{/if}
                {/foreach}
            </span>
        {/if}
        </div>

        <span id='bearbbtn' class="note">ID:&nbsp;{$dialog['id'][1]}&nbsp;</span>
    </div>
    <div id="einzug"></div>

    {* --Geburtstagszeile-- *}
    {if !empty($dialog['gtag'][1]) OR !empty($dialog['gort'][1])}
    <div id='einzug'>
        {if !empty($dialog['gtag'][2])}{$dialog['gtag'][2]}:{/if}
        {if !empty($dialog['gtag'][1])}{$dialog['gtag'][1]}{/if}
        {if !empty($dialog['gort'][1])}
            &nbsp;{$dialog['gort'][2]}&nbsp;{$dialog['gort'][1]['ort']}
            &nbsp;({$dialog['gort'][1]['land']},&nbsp;{$dialog['gort'][1]['bland']})
        {/if}
    </div>
    {/if}

    {* --Todeszeile-- *}
    {if !empty($dialog['ttag'][1]) OR !empty($dialog['tort'][1])}
    <div id='einzug'>
        {if !empty($dialog['ttag'][2])}{$dialog['ttag'][2]}{/if}
        {if !empty($dialog['ttag'][1])}{$dialog['ttag'][1]}{/if}
        {if !empty($dialog['tort'][1])}
            &nbsp;{$dialog['tort'][2]}&nbsp;{$dialog['tort'][1]['ort']}
            &nbsp;({$dialog['tort'][1]['land']},&nbsp;{$dialog['tort'][1]['bland']})
        {/if}
    </div>
    {/if}
</div>
