<?php namespace DiafIP {
    /**
     * Eventhandler für Aktionen der Personenverwaltung
     *
     * @author      Knut Wetzig <knwetzig@gmail.com>
     * @copyright   Deutsches Institut für Animationsfilm e.V.
     * @license     http://opensource.org/licenses/BSD-3-Clause BSD-3 License
     * @requirement PHP Version >= 5.4
     */

    global $marty, $str;
    // Überschrift
    $data = a_display([
                          // name,inhalt,rechte, optional-> $label,$tooltip,valString
                          new d_feld('bereich', $str->getStr(4012)),
                          new d_feld('sstring', $str->getStr(4011)),
                          new d_feld('sektion', 'P'),
                      ]);
    $marty->assign('dialog', $data);
    $marty->assign('darkBG', 0);
    $marty->display('main_bereich.tpl');

    if (isset($_REQUEST['aktion']) ? $_REQUEST['aktion'] : '') {
        $marty->assign('aktion', $_REQUEST['aktion']);
        // switch:aktion => add | edit | search | del | view
        switch ($_REQUEST['aktion']) :
            case "search" :
                if (isset($_POST['sstring'])) :
                    $PersonList = PName::search($_POST['sstring']);
                    if (is_array($PersonList)) :
                        // Ausgabe
                        $bg = 1;
                        foreach (($PersonList) as $key => $val) :
                            ++$bg;
                            $marty->assign('darkBG', $bg % 2);
                            switch ($val) :
                                case 'N' :
                                    $nam = new PName($key);
                                    $nam->display('pers_ldat.tpl');
                                    unset($nam);
                                    break;
                                case 'P' :
                                    $pers = new Person($key);
                                    $pers->display('pers_ldat.tpl');
                                    unset($pers);
                            endswitch;
                        endforeach;
                    else : feedback(102, 'hinw'); // kein Ergebnis
                    endif;
                endif;
                break; // Ende --search--

            case "view" :
                $pers = new Person($_REQUEST['id']);
                $pers->display('pers_dat.tpl');
                unset($pers);
        endswitch;
    }
// aus irgend welchen Gründen wurde keine 'aktion' ausgelöst?
}