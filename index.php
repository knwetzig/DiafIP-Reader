<?php namespace DiafIP {
    use MDB2, Smarty;
    global $dsn, $smartyConf;

    /**
     * DiafIP-Reader HAUPTPROGRAMM
     *
     * @author      Knut Wetzig <knwetzig@gmail.com>
     * @copyright   2015 Deutsches Institut für Animationsfilm e.V.
     * @license     http://opensource.org/licenses/BSD-3-Clause BSD-3 License
     * @package     DiafIP-Reader
     * @requirement PHP Version >= 5.4
     */

    require_once 'configs/config.local.php';
    require_once 'configs/config.php';
    $_POST = normtext($_POST);              // Filter für htmlentities
    $_GET = normtext($_GET);
    session_start();
    if(!isset($_SESSION['lang'])) $_SESSION['lang'] = 'de';

    // DB-Initialisierung
    $db = MDB2::singleton($dsn, ['use_transactions' => true, 'persistent' => true]);
    IsDbError($db);
    $db->setFetchMode(MDB2_FETCHMODE_ASSOC);
    $db->loadModule('Extended');

    // Abfangen von Aktionen die nicht durch nachfolgende Eventhandler bedient werden
    if (isset($_GET['aktion'])) switch ($_GET['aktion']) :
        case 'de' :
        case 'en' :
        case 'fr' :
            $_SESSION['lang'] = $_GET['aktion'];
    endswitch;

    // locale der DB einstellen
    switch ($_SESSION['lang']) :
        case 'de' :
            $db->query("SET datestyle TO German");
            break;
        case 'en' :
        case 'fr' :
            $db->query("SET datestyle TO European");
            break;
        default :
            $db->query("SET datestyle TO ISO");
    endswitch;

    // Laden der Klassen
    require_once 'entity.class.php';        // Basisklasse
    require_once 'pname.class.php';         // Aliasnamen
    require_once 'person.class.php';        // Personenklasse
    require_once 'fibimain.class.php';      // Basisklasse für Biblio-/Filmogr. Daten
    require_once 'figd2.class.php';         // Filmografische Daten
    require_once 'view.class.php';
    require_once 's_location.class.php';

    // Initialisierung String-Objekt
    $str = new Wort($_SESSION['lang']);

    // Laden Menübereich
    $menue = ['F'        => $str->getStr(4008),
              'Y'        => $str->getStr(4028),
              'P'        => $str->getStr(4003),
              'Z'        => $str->getStr(4032),
              'K'        => $str->getStr(4038),
              'impr'     => $str->getStr(4040),
              'lang'     => $_SESSION['lang'],
              'phpself'  => $_SERVER['PHP_SELF']];

    // Smarty initialisieren
    $marty = new Smarty;
    $marty->setTemplateDir($smartyConf['template_dir']);
    $marty->setCompileDir($smartyConf['compile_dir']);
    $marty->setConfigDir($smartyConf['config_dir']);
    $marty->setCacheDir($smartyConf['cache_dir']);
    $marty->force_compile = $smartyConf['force_compile'];
    $marty->debugging = $smartyConf['debugging'];

    $marty->assign('dlg', $menue);
    $marty->display('header.tpl');

    require_once 'inc/main.php';
    echo '</body></html>';
}