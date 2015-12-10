    {* --- Name/Status/Bearbeitungssymbole --- *}
    <div id="bearbzeile">
        {if !empty($dialog['titel'][1])}
            <div id="left" class="fett">{$dialog['titel'][1]}</div>
        {/if}

            <span id="bearbbtn" class="note">
                ID:&nbsp;{$dialog['id'][1]}{if !empty($dialog['chdatum'][1])}&nbsp;|&nbsp;{$dialog['chdatum'][1]}&nbsp;
                |&nbsp;{$dialog['chname'][1]}{/if}&nbsp;
            </span>
    </div>

<table width="100%">
    <colgroup><col width="200px"><col><col width="200px"></colgroup>

{* --Untertitel-- *}
    {if !empty($dialog['utitel'][1])}<tr>
        <td class="re label">
            {if !empty($dialog['utitel'][2])}{$dialog['utitel'][2]}:{/if}
        </td>
        <td class="value">
            {if !empty($dialog['utitel'][1])}{$dialog['utitel'][1]}{/if}
        </td>
    </tr>{/if}

{* --Arbeitstitel-- *}
    {if !empty($dialog['atitel'][1])}<tr>
        <td class="re label">{$dialog['atitel'][2]}:</td>
        <td class="value">{$dialog['atitel'][1]}</td>
    </tr>{/if}

{* --Serientitel-- *}
    {if !empty($dialog['stitel'][1])}<tr>
        <td class="re label">{$dialog['stitel'][2]}:</td>
        <td class="value" {if !empty($dialog['sdescr'][1])}onmouseover="return overlib('{$dialog['sdescr'][1]}',DELAY,500);"
            onmouseout="return nd();"{/if}>{$dialog['stitel'][1]} {if !empty($dialog['sfolge'][1])}({$dialog['sfolge'][1]}){/if}</td>
    </tr>{/if}

{* --Auftraggeber-- *}
    {if !empty($dialog['auftraggeber'][1])}<tr>
        <td class="re label">{$dialog['auftraggeber'][2]}:</td>
        <td class="value">{$dialog['auftraggeber'][1]}</td>
    </tr>{/if}

{* --prod_jahr-- *}
    {if !empty($dialog['prod_jahr'][1])}<tr>
        <td class="re label">{$dialog['prod_jahr'][2]}:</td>
        <td class="value">{$dialog['prod_jahr'][1]}</td>
    </tr>{/if}

{* --prod_land-- *}
    {if !empty($dialog['prod_land'][1])}<tr>
        <td class="re label">{$dialog['prod_land'][2]}:</td>
        <td class="value">
          {foreach from=$dialog['prod_land'][1] item=wert}
            {$wert}&nbsp;
          {/foreach}
        </td>
    </tr>{/if}

{* --thema-- ist ein array*}
    {if !empty($dialog['thema'][1])}<tr>
        <td class="re label">{$dialog['thema'][2]}:</td>
        <td class="value">
            {foreach from=$dialog['thema'][1] item=wert}
                {$wert}{if !$wert@last},{/if}
            {/foreach}
        </td>
    </tr>{/if}

{* --gattung-- *}
    {if !empty($dialog['gattung'][1])}<tr>
        <td class="re label">{$dialog['gattung'][2]}:</td>
        <td class="value">{$dialog['gattung'][1]}</td>
    </tr>{/if}

{* --prodtech-- *}
    {if !empty($dialog['prodtech'][1])}<tr>
        <td class="re label" style="vertical-align:top">{$dialog['prodtech'][2]}:</td>
        <td class="value">
            {foreach from=$dialog['prodtech'][1] item=wert}
                {$wert}{if !$wert@last},{/if}
            {/foreach}
        </td>
    </tr>{/if}

{* --laenge-- *}
    {if !empty($dialog['laenge'][1])}<tr>
        <td class="re label">{$dialog['laenge'][2]}:</td>
        <td class="value">{$dialog['laenge'][1]}</td>
    </tr>{/if}

{* --fsk-- *}
    {if !empty($dialog['fsk'][1])}<tr>
        <td class="re label">{$dialog['fsk'][2]}:</td>
        <td class="value">{$dialog['fsk'][1]}</td>
    </tr>{/if}

{* --praedikat-- *}
    {if !empty($dialog['praedikat'][1])}<tr>
        <td class="re label">{$dialog['praedikat'][2]}:</td>
        <td class="value">{$dialog['praedikat'][1]}</td>
    </tr>{/if}

{* --urrauff-- *}
    {if !empty($dialog['urauff'][1])}<tr>
        <td class="re label">{$dialog['urauff'][2]}:</td>
        <td class="value">{$dialog['urauff'][1]}</td>
    </tr>{/if}

{* --bildformat-- *}
    {if !empty($dialog['bildformat'][1])}<tr>
        <td class="re label">{$dialog['bildformat'][2]}:</td>
        <td class="value">{$dialog['bildformat'][1]}</td>
    <tr>{/if}

{* --mediaspezi-- *}
    {if !empty($dialog['mediaspezi'][1])}<tr>
        <td class="re label" style="vertical-align:top">{$dialog['mediaspezi'][2]}:</td>
        <td class="value">
            {foreach from=$dialog['mediaspezi'][1] item=wert}
                {$wert}
                {if !$wert@last}
                    ,&nbsp;
                {/if}
            {/foreach}</td>
    </tr>{/if}

{* --Besetzung-- *}
	{if !empty($dialog['cast'][1])}
		{foreach from=$dialog['cast'][1] item=cast key=index name=count}
			{assign var="cnt" value="{$smarty.foreach.count.index}"}
			{if $cnt > 0 && $cast['job'] == $dialog['cast'][1][{$cnt-1}]['job']}
				<tr>
					<td class="re label"></td>
					<td class="value">{$cast['name']}</td>
				</tr>
			{else}
				<tr>
					<td class="re label">{$cast['job']}:</td>
					<td class="value">{$cast['name']}</td>
				</tr>
			{/if}
		{/foreach}
	{/if}

{* --descr-- *}
    {if !empty($dialog['descr'][1])}<tr>
        <td class="re label" style="vertical-align:top">{$dialog['descr'][2]}:</td>
        <td class="value">{$dialog['descr'][1]|nl2br}</td>
    </tr>{/if}

{* --quellen-- *}
    {if !empty($dialog['quellen'][1])}<tr>
        <td class="re label">{$dialog['quellen'][2]}:</td>
        <td class="value">{$dialog['quellen'][1]}</td>
    </tr>{/if}

{* --anmerk-- *}
    {if !empty($dialog['anmerk'][1])}<tr>
        <td class="re label" style="vertical-align:top">{$dialog['anmerk'][2]}:</td>
        <td class="value">{$dialog['anmerk'][1]|nl2br}</td>
    </tr>{/if}

{* --isvalid-- Eintrag *}
    {if !empty($dialog['isVal'][1])}<tr>
        <td colspan="3" class="re"><img src="images/ok.png" />&nbsp;{$dialog['isVal'][2]}</td>
    </tr>{/if}

</table>