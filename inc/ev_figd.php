<?php namespace DiafIPReader {
    global $marty, $str;
    /**
     *
     * Eventhandler für Aktionen der Filmverwaltung
     *
     * @author      Knut Wetzig <knwetzig@gmail.com>
     * @copyright   Deutsches Institut für Animationsfilm e.V.
     * @license     http://opensource.org/licenses/BSD-3-Clause BSD-3 License
     * @requirement PHP Version >= 5.4
     *
     */

    // Anzeige eines zufälligen Films
    $list = Film::getAllFilms();
    $key = rand(1, count($list));

    // Picklist
    $picklist = '|&nbsp;';
    for($i=65; $i <= 90; $i++) :
        $picklist .= "<a href='index.php?sektion=F&aktion=Pick&C=".chr($i)."'>".chr($i)."</a>&nbsp;|&nbsp;";
    endfor;
    $picklist .= "<br>";

    // Kopfbereich
    $data = a_display([
              // name,inhalt,rechte, optional-> $label,$tooltip,valString
              new d_feld('bereich', $str->getStr(4008)),
              new d_feld('sstring', $str->getStr(4011)),
              new d_feld('picklist', $picklist),
              new d_feld('sektion', 'F'),
                      ]);
    $marty->assign('dialog', $data);
    $marty->assign('darkBG', 0);
    $marty->display('main_bereich.tpl');

    if (isset($_REQUEST['aktion']) ? $_REQUEST['aktion'] : '') :
        $marty->assign('aktion', $_REQUEST['aktion']);

        switch (isset($_REQUEST['aktion']) ? $_REQUEST['aktion'] : 'view') :

            case "search" :
                if (isset($_POST['sstring'])) :
                    $suche = $_POST['sstring'];
                    $_SESSION{'search'} = $suche;
                    $tlist = Film::search($suche);
                    if (!empty($tlist) AND is_array($tlist)) :
                        // Ausgabe
                        $bg = 1;
                        foreach (($tlist) as $nr) :
                            // Eingrenzung Filme
                            if(Entity::getBereich($nr) === 'F') :
                                ++$bg;
                                $motpic = new Film($nr);
                                $marty->assign('darkBG', $bg % 2);
                                $motpic->display('figd_ldat.tpl');
                                unset($motpic);
                            endif;
                        endforeach;
                    else :
                        feedback(102, 'hinw'); // kein Erg.
                    endif;
                endif;
                break;

            case "Pick" :
                $bg = 1;
                foreach (Film::listTitels($_GET['C']) as $wert) :
                    ++$bg;
                    $marty->assign('darkBG', $bg % 2);
                    $pers = new Film($wert);
                    $pers->display('figd_ldat.tpl');
                    unset($pers);
                endforeach;
                break;

            case 'view' :
                $motpic = new Film(intval($_REQUEST['id']));
                $motpic->display('figd_dat.tpl');
                break;

        endswitch;
    else:
        // aus irgend welchen Gründen wurde keine 'aktion' ausgelöst?
        $film = new Film($list[$key]);
        $film->display('figd_dat.tpl');
    endif;
}