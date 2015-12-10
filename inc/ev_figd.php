<?php namespace DiafIP {
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

    // Kopfbereich
    $data = a_display([
              // name,inhalt,rechte, optional-> $label,$tooltip,valString
              new d_feld('bereich', $str->getStr(4008)),
              new d_feld('sstring', $str->getStr(4011)),
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

            case 'view' :
                $motpic = new Film(intval($_REQUEST['id']));
                $motpic->display('figd_dat.tpl');
                break;

        endswitch;
    endif;
}