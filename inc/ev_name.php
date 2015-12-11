<?php namespace DiafIP {
    /**
     * Eventhandler für Aktionen der Namensverwaltung
     *
     * @author      Knut Wetzig <knwetzig@gmail.com>
     * @copyright   Deutsches Institut für Animationsfilm e.V.
     * @license     http://opensource.org/licenses/BSD-3-Clause BSD-3 License
     * @requirement PHP Version >= 5.4
     **************************************************************/

    global $marty, $str;
    // Kopf
    $data = [new d_feld('bereich', $str->getStr(4012)),
             new d_feld('sektion', 'N'),
             new d_feld('sstring', $str->getStr(4011))
            ];
    $marty->assign('dialog', a_display($data));
    $marty->assign('darkBG', 0);
    $marty->display('main_bereich.tpl');

    if (isset($_REQUEST['aktion']) ? $_REQUEST['aktion'] : '') :
        // switch:aktion => add | extra | edit | search | del | view
        switch ($_REQUEST['aktion']) :
            case "search" :
                if (isset($_POST['sstring'])) :
                    $NameList = PName::search($_POST['sstring']);
                    if (is_array($NameList)) :
                        // Ausgabe
                        $bg = 1;
                        foreach (($NameList) as $key => $val) :
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
                $n = new PName($_REQUEST['id']);
                $n->display('pers_dat.tpl');
                break; // Endview
        endswitch;
    endif;
}
// aus iwelchen Gründen wurde keine 'aktion' ausgelöst?