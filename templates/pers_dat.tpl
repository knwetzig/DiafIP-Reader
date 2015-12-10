

    <div id='bearbzeile'>
{* --Name-- *}
        <div id='left' class="fett">
        {if !empty($dialog['pname'][1])} {$dialog['pname'][1]}{/if}
        {if !empty($dialog['aliases'][1])}
            <span class="alias">
            {* Folgende Zeile darf nicht umgebrochen werden wegen Schreibweise-Leerzeichen *}
            ({foreach $dialog['aliases'][1] as $alias}{$alias}{if $alias@last}){else},&nbsp;{/if}{/foreach}
            </span>
        {/if}
        </div>

        <span id='bearbbtn' class="note">
            ID:&nbsp;{$dialog['id'][1]}&nbsp;
        </span>
    </div>

{* --Geburtstagszeile-- *}
    {if !empty($dialog['gtag'][1]) OR !empty($dialog['gort'][1])}<div id='einzug'>
        {if !empty($dialog['gtag'][2])}{$dialog['gtag'][2]}:&nbsp;{/if}
        {if !empty($dialog['gtag'][1])}{$dialog['gtag'][1]}{/if}
        {if !empty($dialog['gort'][1])}
            &nbsp;{$dialog['gort'][2]}&nbsp;{$dialog['gort'][1]['ort']}
            &nbsp;({$dialog['gort'][1]['land']},&nbsp;{$dialog['gort'][1]['bland']})
        {/if}
        </div>
    {/if}

{* --Todeszeile-- *}
    {if !empty($dialog['ttag'][1]) OR !empty($dialog['tort'][1])}<div id='einzug'>
        {if !empty($dialog['ttag'][2])}{$dialog['ttag'][2]}:&nbsp;{/if}
        {if !empty($dialog['ttag'][1])}{$dialog['ttag'][1]}{/if}
        {if !empty($dialog['tort'][1])}
            &nbsp;{$dialog['tort'][2]}&nbsp;{$dialog['tort'][1]['ort']}
            &nbsp;({$dialog['tort'][1]['land']},&nbsp;{$dialog['tort'][1]['bland']})
        {/if}
        </div>
    {/if}

{* --Biografiezeile-- *}
    {if !empty($dialog['descr'][1])}
        <div id='einzug'>{$dialog['descr'][2]}:&nbsp;{$dialog['descr'][1]|nl2br}</div>
    {/if}

{* --Verweis auf Filmografie-- *}
    {if !empty($dialog['castLi'][1])}
        <table id='einzug'>

        {foreach from=$dialog['castLi'][1] item=cast key=index name=count}
            {assign var="cnt" value="{$smarty.foreach.count.index}"}
            {if $cnt > 0 && $cast['ftitel'] == $dialog['castLi'][1][{$cnt-1}]['ftitel']}
                <tr>
                    <td class="re label"></td>
                    <td class="value">{$cast['job']}</td>
                </tr>
            {else}
                <tr>
                    <td class="re label">{$cast['ftitel']}:</td>
                    <td class="value">{$cast['job']}</td>
                </tr>
            {/if}
        {/foreach}
        </table>
    {/if}

{* --isvalid-- Eintrag *}
    {if !empty($dialog['isVal'][1])}
        <div id='bearbbtn'><img src="images/ok.png" />&nbsp;{$dialog['isVal'][2]}</div>{/if}

