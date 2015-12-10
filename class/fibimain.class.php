<?php namespace DiafIP;
        use MDB2;
/**
 * Klassenbibliotheken für Filmogr.-/Bibliografische Daten
 *
 * @author      Knut Wetzig <knwetzig@gmail.com>
 * @copyright   2015 Deutsches Institut für Animationsfilm e.V.
 * @license     http://opensource.org/licenses/BSD-3-Clause BSD-3 License
 * @package     DiafIP-Reader\Film
 * @requirement PHP Version >= 5.4
 */

/** FIBIMAIN CLASS */
abstract class FibiMain extends Entity implements iFibiMain {

    const
        SQL_GET_CAST_LI   = 'SELECT f_cast.tid, f_cast.pid
                             FROM f_cast
                             WHERE f_cast.fid = ?
                             ORDER BY tid;',

        SQL_GET_DATA      = 'SELECT titel, atitel, utitel, sid, sfolge, prod_jahr, anmerk, quellen, thema
                             FROM f_main2
                             WHERE id = ?;',

        SQL_GET_STITEL    = 'SELECT titel, descr
                             FROM f_stitel
                             WHERE sertitel_id = ?;',

        SQL_GET_STITEL_LI = 'SELECT sertitel_id, titel
                             FROM f_stitel
                             ORDER BY titel ASC;',

        SQL_GET_TAETIG    = 'SELECT * FROM f_taetig;',

        SQL_GET_TITEL_LI  = 'SELECT f_main2.id, f_main2.titel
                             FROM public.f_main2
                             WHERE f_main2.del != TRUE
                             ORDER BY f_main2.titel ASC;',

        SQL_IF_EXIST_CAST = 'SELECT COUNT(*)
                             FROM f_cast
                             WHERE fid = ? AND pid = ? AND tid = ?;',

        SQL_IF_LINKED     = 'SELECT COUNT(*)
                             FROM f_cast
                             WHERE fid = ?',

        SQL_SEARCH_TITEL  = 'SELECT DISTINCT id
                             FROM f_main2, f_stitel
                             WHERE (f_main2.del = FALSE) AND (f_main2.titel ILIKE ? OR
                                    f_main2.atitel ILIKE ? OR
                                    f_main2.utitel ILIKE ? OR
                                    (f_stitel.titel ILIKE ? AND f_stitel.sertitel_id = f_main2.sid));',

        TYPE_FIBI  = 'text,text,text,integer,integer,text,text,text,text,';

    protected
        $stitel = null, // Serientitel -> diafip.f_stitel.titel
        $sdescr = null; // Beschreibung Serie

    /**
     * Initialisiert das Objekt (auch gelöschte)
     * @param null $nr
     */
    public function __construct($nr = null) {
        parent::__construct($nr);
        $this->content['titel']     = null; // Originaltitel
        $this->content['atitel']    = null; // Arbeitstitel
        $this->content['utitel']    = null; // Untertitel
        $this->content['sid']       = null; // Serien - ID
        $this->content['sfolge']    = null; // Serienfolge
        $this->content['prod_jahr'] = null;
        $this->content['anmerk']    = null;
        $this->content['quellen']   = null;
        $this->content['thema']     = []; // Schlagwortverzeichnis
        if ((isset($nr)) AND is_numeric($nr)) :
            $db   = MDB2::singleton();
            $data = $db->extended->getRow(self::SQL_GET_DATA, list2array(self::TYPE_FIBI), $nr, 'integer');
            IsDbError($data);
            if(!empty($data['thema'])) $data['thema'] = list2array($data['thema']);
            self::WertZuwCont($data);

            // Serientitel holen, soweit vorhanden
            if ($this->content['sid']) :
                $data = $db->extended->getRow(self::SQL_GET_STITEL, null, $this->content['sid'], 'integer');
                IsDbError($data);
                $this->stitel = $data['titel'];
                $this->sdescr = $data['descr'];
            endif;
        endif;
    }

    /**
     * Ausgabe des Titels
     * @return string
     */
    public function getTitel() {
        return "<a href='index.php?{$this->content['bereich']}={$this->content['id']}'>{$this->content['titel']}</a>";
    }

    /**
     * gibt ein Array(num, text) der Tätigkeiten zurück
     * @return array
     */
    final static function getTaetigList() {
        $db = MDB2::singleton();
        global $str;
        $list = $db->extended->getCol(self::SQL_GET_TAETIG, 'integer');
        IsDbError($list);
        $data = [];
        foreach ($list as $wert) $data[$wert] = $str->getStr($wert);
        asort($data);
        return $data;
    }

    /**
     * Ausgabe der Serientitelliste
     * @return array|int
     */
    final public static function getSTitelList() {
        $db       = MDB2::singleton();
        $ergebnis = [];
        $erg      = $db->query(self::SQL_GET_STITEL_LI);
        IsDbError($erg);
        while ($row = $erg->fetchRow()) :
            $ergebnis[$row['sertitel_id']] = $row['titel'];
        endwhile;
        if ($ergebnis) return $ergebnis; else return 1;
    }

    /**
     * Gibt die Besetzungsliste für diesen Datensatz aus
     * Anm.: Die Sortierreihenfolge ist durch die ID in der Stringtabelle
     *  fest vorgegeben. Bei Änderung bitte den Eintrag in der Tabelle
     *  f_taetig korrigieren.
     * @return array (name, tid, pid, job)
     */
    final protected function getCastList() {
        $db = MDB2::singleton();
        global $str;
        if (empty($this->content['id'])) return null;
        $data = $db->extended->getALL(
            self::SQL_GET_CAST_LI, null, $this->content['id'], 'integer');
        IsDbError($data);

        // Übersetzung für die Tätigkeit und Namen holen
        foreach ($data as &$wert) :
            $wert['job']  = $str->getStr($wert['tid']);
            $p            = new PName($wert['pid']);
            $wert['name'] = $p->getName();
        endforeach;
        unset($wert);
        return $data;
    }

    /**
     * Suchfunktion in allen Titelspalten incl. Serientiteln
     * @param $s
     * @return array | int
     */
    static public function search($s) {
        $s  = "%" . $s . "%";
        $db = MDB2::singleton();

        // Suche in titel, atitel, utitel
        $data = $db->extended->getCol(self::SQL_SEARCH_TITEL, ['integer'], [$s, $s, $s, $s]);
        IsDbError($data);
        if ($data) :
            return $data;
        else :
            return 1;
        endif;
    }

    /**
     * Zusammenstellung der Daten eines Datensatzes, Einstellen der Rechteparameter
     * Auflösen von Listen und holen der Strings aus der Tabelle Zuweisungen und
     * ausgabe via display()
     * Anm.:   Zentrales Objekt zur Handhabung der Ausgabe
     * @return array|int
     */
    protected function view() {
        $data   = parent::view();

        $data[] = new d_feld('descr', changetext($this->content['descr']), null, 506); // Beschreibung
        $data[] = new d_feld('titel', self::getTitel(), null, 500); // Originaltitel
        $data[] = new d_feld('atitel', $this->content['atitel'], null, 503); // Arbeitstitel
        $data[] = new d_feld('utitel', $this->content['utitel'], null, 501); // Untertitel
        $data[] = new d_feld('stitel', $this->stitel, null, 504); // Serientitel
        $data[] = new d_feld('sfolge', $this->content['sfolge']); // Serienfolge
        $data[] = new d_feld('sdescr', $this->sdescr); // Beschreibung Serie
        $data[] = new d_feld('prod_jahr', $this->content['prod_jahr'], null, 576);
        $data[] = new d_feld('anmerk', changetext($this->content['anmerk']), null, 572);
        $data[] = new d_feld('quellen', $this->content['quellen'], null, 578);
        $data[] = new d_feld('thema', $this->content['thema'], null, 577); // Schlagwortliste
        $data[] = new d_feld('isVal', $this->content['isvalid'], null, 10009);
        $data[] = new d_feld('cast', $this->getCastList());
        return $data;
    }
}
