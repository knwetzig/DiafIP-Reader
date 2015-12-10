<?php namespace DiafIP {
    use MDB2, ErrorException, DateTime, DateInterval;
    /**
     * Klassenbibliotheken für Personen und Aliasnamen
     *
     * Dazu gehören eine Klasse für Namen und eine Klasse für Personen inclusive der Interfaces
     */

    /**
     * Class Person - Personen sind natürliche und juristische Personen.
     *
     * @author      Knut Wetzig <knwetzig@gmail.com>
     * @copyright   2014 Deutsches Institut für Animationsfilm e.V.
     * @license     http://opensource.org/licenses/BSD-3-Clause BSD-3 License
     * @package     DiafIP\Person
     * @version     $Id$
     * @since       r52
     * @requirement PHP Version >= 5.4
     */
    class Person extends PName implements iPerson {

        const
            TYPEPERSON = 'date,integer,date,integer,text',
            SQL_GET_DATA    = 'SELECT gtag, gort, ttag, tort, strasse, plz, wort, tel, mail, aliases
                          FROM p_person2 WHERE id = ?;';

        /**
         * Initialisiert das Personenobjekt
         *
         * @param int|null $nr
         */
        function __construct($nr = null) {
            parent::__construct($nr);
            if(!empty($nr) AND $this->content['bereich'] !== 'P') return 1;
            $this->content['gtag']    = '0001-01-01'; // Geburtstag
            $this->content['gort']    = null; // Geburtsort
            $this->content['ttag']    = null; // Todestag
            $this->content['tort']    = null; // Sterbeort
            $this->content['aliases'] = null;
            if (isset($nr) AND is_numeric($nr)) :
                $db = MDB2::singleton();
                $data = $db->extended->getRow(self::SQL_GET_DATA, list2array(self::TYPEPERSON), $nr, 'integer');
                self::WertZuwCont($data);
            endif;
            return null;
        }

        /**
         *  Ermitteln der/des Aliasnamen
         *
         * @return array|null Liste der Namen.
         */
        public function getAliases() {
            if ($this->content['aliases']) :
                $data = [];
                foreach (list2array($this->content['aliases']) as $val) :
                    $e      = new PName(intval($val));
                    $data[] = $e->fiVname() . $e->content['nname'];
                endforeach;
                return $data;
            endif;
            return null;
        }

        /**
         * Bereitstellung der Daten für Ausgabe durch display()
         *
         * Anzeige eines Datensatzes, Einstellen der Rechteparameter Auflösen von Listen und holen der Strings aus der Tabelle
         * Zuweisungen und ausgabe an pers_dat.tpl
         * Anm.:   Zentrales Objekt zur Handhabung der Ausgabe
         *
         * @return array
         **/
        public function view() {
            $data   = parent::view();
            $data[] = new d_feld('descr', changetext($this->content['descr']), null, 513); // Biografie
            $data[] = new d_feld('aliases', $this->getAliases(), null, 515);
            $data[] = new d_feld('gtag', $this->fiGtag(), null, 502);
            $data[] = new d_feld('gort', Ort::getOrt($this->content['gort']), null, 4014);
            $data[] = new d_feld('ttag', $this->content['ttag'], null, 509);
            $data[] = new d_feld('tort', Ort::getOrt($this->content['tort']), null, 4014);
            $data[] = new d_feld('castLi', $this->getCastList());
            return $data;
        }

        /**
         * Geburtstagsfilter
         *
         * @return int|null
         */
        private function fiGtag() {
            if (($this->content['gtag'] === '0001-01-01') OR ($this->content['gtag'] === '01.01.0001'))
                return null; else return $this->content['gtag'];
        }
    }
} // end Personen-Klasse