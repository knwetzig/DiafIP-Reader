<?php namespace DiafIPReader {
    use MDB2;
    /**
     * Klassenbibliotheken für Personen und Aliasnamen
     *
     * Dazu gehören eine Klasse für Namen und eine Klasse für Personen inclusive der Interfaces
     */


    /**
     * Class PName
     *
     * @author      Knut Wetzig <knwetzig@gmail.com>
     * @copyright   2015 Deutsches Institut für Animationsfilm e.V.
     * @license     http://opensource.org/licenses/BSD-3-Clause BSD-3 License
     * @since       r99 Klassentrennung
     * @requirement PHP Version >= 5.4
     */
    class PName extends Entity implements iPName {
        const
            SQL_GET_DATA =     'SELECT vname, nname
                                FROM p_namen
                                WHERE id = ?;',

            SQL_GET_PERSON =   'SELECT id
                                FROM p_person2
                                WHERE ? = ANY(aliases);',

            SQL_GET_ALIAS  =   'SELECT DISTINCT p_namen.id, p_namen.vname, p_namen.nname
                                FROM ONLY p_namen,p_person2
                                WHERE (p_namen.del = FALSE) AND (p_namen.id = ANY(p_person2.aliases))
                                ORDER BY p_namen.nname, p_namen.vname;',

            SQL_GET_CAST_LI    = 'SELECT fid, tid FROM f_cast WHERE pid= ? ORDER BY fid;',      // Casting-Liste

            SQL_GET_NAMES =    'SELECT id,vname,nname
                                FROM ONLY p_namen
                                WHERE del = FALSE
                                ORDER BY nname,vname;',

            SQL_GET_ID_FROM_NAME = 'SELECT id
                                FROM p_namen
                                WHERE (vname = ?) AND (nname = ?)',

            SQL_SEARCH_NAME  = 'SELECT id,bereich
                                FROM p_namen
                                WHERE (del = FALSE) AND ((nname ILIKE ?) OR (vname ILIKE ?))
                                ORDER BY nname,vname;',

            SQL_SEARCH_FIRSTCHAR  = 'SELECT id FROM p_namen WHERE (del = FALSE) AND ((nname ILIKE ?)) ORDER BY nname;',

            TYPENAME         = 'text,text,';

        /**
         * Verweis auf die Person die den Alias verwendet
         *
         * @var null $alias
         */
        protected
            $alias = null;

        /**
         * Initialisiert das Objekt
         *
         * @param int|null $nr
         */
        function __construct($nr = null) {
            parent::__construct($nr);
            $this->content['vname']   = '-';
            $this->content['nname']   = '';
            if (isset($nr) AND is_numeric($nr)) :
                $db = MDB2::singleton();
                $data = $db->extended->getRow(self::SQL_GET_DATA, list2array(self::TYPENAME), $nr, 'integer');
                IsDbError($data);
                if ($data) :
                    $this->content['vname'] = $data['vname'];
                    $this->content['nname'] = $data['nname'];
                    $this->alias            = self::getPerson();
                else :
                    feedback(4, 'error');
                    exit(4);
                endif;
            endif;
        }

        /**
         * Ermittelt die Person zum Aliasnamen
         *
         * @return  int|null null : Es existiert keine Person, Datensatz frei zum löschen
         *          int :  Id zum Benutzer des Alias
         */
        function getPerson() {
            $db = MDB2::singleton();
            $p  = null;
            if ($this->content['bereich'] === 'N') :
                $p = $db->extended->getOne(self::SQL_GET_PERSON, 'integer', $this->content['id'], 'integer');
                IsDbError($p);
            endif;
            return $p;
        }

        /**
         * Prüft, ob sich ein Namenseintrag in der DB finden lässt und liefert die Id's
         *
         * @param $vname
         * @param $nname
         * @return array | null
         */
        final static function getIdFromName($nname, $vname = null) {
            if(empty($vname)) $vname = '-';
            $db = MDB2::singleton();
            $data = $db->extended->getCol(self::SQL_GET_ID_FROM_NAME, 'integer', [$vname, $nname]);
            IsDbError($data);
            return $data;
        }

        /**
         * Stellt die Liste mit den Id's und Namen für das Formular zusammen
         *
         * @param $arr
         * @return array
         */
        protected function arrpack($arr) {
            $erg = [];
            foreach ($arr as $val) :
                if ($val['vname'] === '-') :
                    $erg[$val['id']] = $val['nname'];
                else :
                    $erg[$val['id']] = $val['vname'] . '&nbsp;' . $val['nname'];
                endif;
            endforeach;
            return $erg;
        }

        /**
         * Liefert die Namensliste für Drop-Down-Menü "Aliasnamen" im Personendialog
         * Listet nur die unbenutzten Aliasnamen
         *
         * @return  array   [id, vname+name]
         *
         *                  Anm.:   Vielleicht findet sich ja mal ein Held der die Datenbankabfrage optimiert
         *                          und dieses recht komplizierte Konstrukt auflöst ;-)
         */
        static function getUnusedAliasNameList() {
            $db   = MDB2::singleton();
            $erg  = [];
            $data = $db->extended->getAll(self::SQL_GET_ALIAS, ['integer', 'text', 'text']);
            IsDbError($data);
            $data = self::arrpack($data);
            $all  = $db->extended->getAll(self::SQL_GET_NAMES, ['integer', 'text', 'text']);
            IsDbError($all);
            $all    = self::arrpack($all);
            $erg += array_diff($all, $data);
            return $erg;
        }

        /**
         * Sucht alle Namen mit dem Anfangsbuchstaben (nicht Literal)
         *
         * @param string $s Suchmuster
         * @return array|null  Id's oder null (Namen und Personen)
         */
        public static function listNames($s) {
            $db  = MDB2::singleton();
            $data = $db->extended->getCol(self::SQL_SEARCH_FIRSTCHAR, 'integer', "$s%");
            IsDbError($data);
            return $data;
        }

        /**
         * Sucht in Vor- und Nachnamen (nicht Literal)
         *
         * @param string $s Suchmuster
         * @return array|null  Id's oder null (Namen und Personen)
         */
        static public function search($s) {
            $db  = MDB2::singleton();
            /* Ermittelt die Anzahl der gültigen Aliase und Personen
            $max = $db->extended->getOne('SELECT COUNT(*) FROM p_namen WHERE del = FALSE;', 'integer');
            IsDbError($max); */

            $s = "%" . $s . "%";                // Suche nach Teilstring
            $data = $db->extended->getAll(self::SQL_SEARCH_NAME, ['integer', 'text'], [$s, $s]);
            IsDbError($data);
            // [id] wird schlüssel
            $list = [];
            foreach($data as $val) : $list[intval($val['id'])] = $val['bereich']; endforeach;
            $data = array_diff_key($list, self::getUnusedAliasNameList());
            if ($data) return $data; else return 102;
        }

        /**
         * Liefert den zusammen gesetzten und verlinkten Namen zurück
         *
         * @return string
         */
        public function getName() {
            if (empty($this->content['id'])) return null;

            $a = null;
            $data = self::fiVname() . $this->content['nname'];
            $i    = $this->content['id'];
            if (!empty($this->alias)) :
                $i = $this->alias;
                $a = '*';
            endif;
            return '<a href="index.php?P='.$i.'">'.$data."</a>$a";
        }

        /**
         * Aufgabe: Ausfiltern des default-Wertes von Vorname
         *
         * @return string|null
         */
        protected function fiVname() {
            if ($this->content['vname'] === '-') return null;
            else return $this->content['vname'] . '&nbsp;';
        }

        /**
         *  Aufgabe: gibt die Besetzungsliste für diese Person aus
         *
         * @return array [ftitel, job]
         */
        final protected function getCastList() {
            if (empty($this->content['id'])) return null;

            global $str;
            $db = MDB2::singleton();
            $castLi = [];

            // Zusammenstellen der Castingliste für diese Person
            $data = $db->extended->getALL(
                self::SQL_GET_CAST_LI,
                ['integer', 'integer', 'integer'],
                $this->content['id'],
                'integer'
            );
            IsDbError($data);

            // Übersetzung für die Tätigkeit und Namen holen
            foreach ($data as $wert) :
                $film = new Film($wert['fid']);
                if (!$film->isDel()) :
                    $g           = [];
                    $g['ftitel'] = $film->getTitel();
                    $g['job']    = $str->getStr($wert['tid']);
                    $castLi[]         = $g;
                endif;
                unset($film);
            endforeach;
            return $castLi;
        }

        /**
         * Bereitstellung des Namens
         *
         * @return array
         */
        protected function view() {
            $data   = parent::view();
            $data[] = new d_feld('pname', self::getName(), RE_VIEW);
            return $data;
        }
    }
}