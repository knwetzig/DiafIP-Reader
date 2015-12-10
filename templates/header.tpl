{config_load file="mc.conf" section="setup"}<!DOCTYPE HTML>
<head>
    <title>{#title#}</title>
    <meta charset='utf-8'/>
	<link rel='stylesheet' href="style.css">
    <link rel='shortcut icon' href='favicon.ico' />
	<script src="js/overlib/overlib.js" ></script>
</head>
<body>
    <div id='overDiv'></div>

    <script type="text/javascript">
	    var ol_width = '250px';
    </script>

    <div id='menue'>
	    <form action='index.php' method='get'>
		    <button class='noBG'><img src='images/diaf.png' alt='DIAF'/></button>
		    <p>
			    <button name='sektion' value='P'>{$dlg['P']}</button>
				<button name='sektion' value='F'>{$dlg['F']}</button>
		    </p>
		    <p>
			    <button class='flag' name='aktion' value='de'>
				    <img src='images/flag-german.png' alt='de'/>
			    </button>
			    <button class='flag' name='aktion' value='en'>
				    <img src='images/flag-english.png' alt='en'/>
			    </button>
			    <button class='flag' name='aktion' value='fr'>
				    <img src='images/flag-french.png' alt='fr'/>
			    </button>
		    </p>
			{if !empty($dlg['impr'])}
				<button  class='noBG' name="sektion" value="impr">{$dlg['impr']}</button>{/if}
	    </form>
    </div>