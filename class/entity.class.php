<?php namespace DiafIP {
    use MDB2;
    /**
     * Diese Klasse stellt Grundlegende Eigenschaften und Methoden für ihre
     * Kindklassen bereit.
     */

    /**
     * Abstrakte Elternklasse aller Objekte
     *
     * @author      Knut Wetzig <knwetzig@gmail.com>
     * @copyright   2015 Deutsches Institut für Animationsfilm e.V.
     * @license     http://opensource.org/licenses/BSD-3-Clause BSD-3 License
     * @package     DiafIP-Reader\Entity
     * @requirement PHP Version >= 5.4
     */
    abstract class Entity implements iEntity {

        /**
         * Objektkonstante
         *
         * @var array TYPE_ENTITY <Typenliste für content>
         * @var string SQL_GET_DATA <SQL-Statement für Initialisierung>
         * @var string SQL_GET_MUELL <SQL-Statement für Indizierung Papierkorb>
         */
        const
            SQL_GET_DATA    = 'SELECT id,bereich,descr,isvalid,del
                               FROM entity  WHERE id = ?;',

            TYPE_ENTITY     = 'integer,text,text,boolean,boolean';

        /**
         * @var array Der Container für die Daten
         */
        protected
            $content = ['id'       => null,
                        'bereich'  => '',    // Enthält die Bereichskennung
                        'descr'    => '',    // Beschreibung bzw. Biografie bei Personen
                        'isvalid'  => false, // Flag zur Kennzeichnung, das dieser Datensatz
                                             // abschließend bearbeitet wurde
                        'del'      => false  // Löschflag
                        ];

        /**
         * Der Konstruktor initiiert ein leeres Objekt oder via get() ein definiertes
         *
         * @param int $nr
         */
        function __construct($nr = null) {
            if (self::existId($nr)) :
                $db   = MDB2::singleton();
                $result = $db->extended->getRow(self::SQL_GET_DATA, list2array(self::TYPE_ENTITY), $nr, 'integer');
                IsDbError($result);
                self::WertZuwCont($result);
            endif;
        }

        protected function WertZuwCont($data){
            // Ergebnis -> Objekt schreiben
            if (!empty($data)) :
                foreach ($data as $key => $val) $this->content[$key] = $val;
            else :
                // Siehe Eintrag "App" in struktur.html" zum Fehler #4
                feedback("Fehler bei der Initialisierung im Objekt \"Entity\"", 'error');
                exit(4);
            endif;
        }

        /**
         * @return integer
         */
        public function getId() {
            return $this->content['id'];
        }

        /**
         * Testet ob es einen Datensatz mit dieser Nummer gibt
         * @param $nr
         * @return bool
         */
        static function existId($nr) {
            $db = MDB2::singleton();
            $anzahl = false;
            if ($nr AND is_numeric($nr)) :
                $anzahl = $db->extended->getOne('SELECT COUNT(*) FROM entity WHERE id = ?;', 'integer', $nr, 'integer');
                IsDbError($anzahl);
            endif;
            return (bool)$anzahl;
        }

        /**
         * Test ob Nr. als id mit diesem Bereichsbuchstaben in der DB existiert
         *
         * @param int $nr
         * @param string $bereich Großbuchstabe
         * @return int
         */
        public static function IsInDB($nr, $bereich) {
            $db   = MDB2::singleton();
            $data = null;

            if (is_numeric($nr) AND is_string($bereich) AND (strlen($bereich) == 1)) :
                $data = $db->extended->getOne(
                    'SELECT COUNT(*) FROM entity WHERE id = ? AND bereich = ?;', 'integer', [$nr, $bereich]);
                IsDbError($data);
            endif;
            return $data;
        }

        /**
         * Holt die passende Bereichskennung zur angegebenen Id
         *
         * @param int $nr
         * @return string Bereichskennung
         */
        public static function getBereich($nr) {
            $db   = MDB2::singleton();
            $data = null;

            if ($nr AND is_numeric($nr)) :
                $data = $db->extended->getOne('SELECT bereich FROM entity WHERE id = ?;', 'text', $nr, 'integer');
                IsDbError($data);
            endif;
            return $data;
        }

        /**
         * Test Validierungsflag
         *
         * @return bool
         */
        public function isValid() {
            if ($this->content['isValid']) return true; else return false;
        }

        /**
         * Test Löschflag
         *
         * @return bool
         */
        public function isDel() {
            if ($this->content['del']) return true; else return false;
        }

        /**
         * Bereitstellung der Ausgabedaten für Filter
         *
         * @return array
         */
        protected function view() {
            $data = [
                // name,inhalt optional-> $rechte,$label,$tooltip,valString
                new d_feld('id', $this->content['id']),
                new d_feld('bereich', $this->content['bereich']),
                // d_feld('descr', $this->content['descr']),    // wird von instantiierter Klasse vorgenommen
                new d_feld('isVal', $this->content['isvalid'], null, 10009),
            ];
            return $data;
        }

        /**
         * Übergibt die Datenkollektion an Smarty und startet die Ausgabe
         *
         * @param string $vorlage Der Templatename (ohne Pfad)
         * @return void
         */
        public function display($vorlage) {
            global $marty;
            $marty->assign('dialog', a_display($this->view()));
            $marty->display($vorlage);
        }
    }
}

