<div class="list-item list-item-figd {if $darkBG}darkBG{/if}">

    {* --- Name/Status/Bearbeitungssymbole --- *}
    <div id='bearbzeile'>
        {* --Titel-- *}
        <div id='left' class="fett">
            {if !empty($dialog['titel'][1])}{$dialog['titel'][1]}{/if}
        </div>
        <span id="bearbbtn"  class="note">ID:&nbsp;{$dialog['id'][1]}&nbsp;</span>
    </div>

    {* --Regie-- *}
    {if !empty($dialog['regie'][1])}
    <div id="einzug">
        {$dialog['regie'][2]}:&nbsp;{foreach $dialog['regie'][1] as $wert}{$wert}<br />{/foreach}
    </div>
    {/if}

    {* --prod_jahr-- *}
    {if !empty($dialog['prod_jahr'][1])}
        <div id="einzug">
            {$dialog['prod_jahr'][2]}:&nbsp;{$dialog['prod_jahr'][1]}
        </div>
    {/if}

    {* --prodtech-- *}
    {if !empty($dialog['prodtech'][1])}
        <div id="einzug">
            {$dialog['prodtech'][2]}:&nbsp;
            {foreach from=$dialog['prodtech'][1] item=wert}
                {$wert}
                {if !$wert@last}
                    ,&nbsp;
                {/if}
            {/foreach}
        </div>
    {/if}
</div>