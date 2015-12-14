<?php namespace DiafIPReader {
    /**
     * Stellt Klassen und Funktionen für die
     * Ein-/Ausgabefunktionalität bereit.
     *
     * @author      Knut Wetzig <knwetzig@gmail.com>
     * @copyright   Deutsches Institut für Animationsfilm e.V.
     * @license     http://opensource.org/licenses/BSD-3-Clause BSD-3 License
     * @requirement PHP Version >= 5.4
     */

    /**
     * STRINGS
     */
    interface iWort {
        public function getStr($nr);
        public function getStrList($sl);
    }

    class Wort implements iWort {
        protected $strtable = [];

        function __construct($lang) {
            global $db;

            $str = $db->extended->getAll('SELECT * FROM s_strings;');
            IsDbError($str);

            foreach ($str as $s) :
                $this->strtable[$s['id']] = ($s[$lang]) ? $s[$lang] : $s['de'];
            endforeach;
            unset($str);
        }

        public function getStr($nr) {
            if (empty($nr) OR !is_numeric($nr) OR empty($this->strtable[$nr])) return null;
            return $this->strtable[$nr];
        }

        /**
         * @param $sl
         * @return array|int
         */
        public function getStrList($sl) {
            if (!is_array($sl)) return 1;
            $nl = [];
            foreach ($sl as $value) $nl[] = $this->strtable[$value];
            return $nl;
        }
    }

    /**
     * VIEW
     */
    /*
    Repräsentiert ein Ein-/Ausgabeelement

      isValid()               Validierung + Variable setzen
      display()       DYNA    Gibt ein Array für Anzeige aus
    */

    class d_feld {
        protected
            $name = null, // Feldname aus Objekt
            $inhalt = null, // Wert des Objektes
            $valStr = null, // Regulärer Ausdruck zur Validierung des Inhalts
            $label = null, // Beschriftungstext
            $tooltip = null,
            $rights = null; // erforderliche Rechte (pos des Bits, 0 beginnend)

        function __construct($name, $wert, $rechte = null, $label = null, $tipp = null, $valStr = null) {
            global $str;

            $this->name   = $name;
            $this->inhalt = $wert;
            if (!empty($rechte) AND is_int($rechte)) $this->rights = $rechte;
            $this->valStr = $valStr;
            if (!empty($label) AND is_int($label)) $this->label = $str->getStr($label);
            if (!empty($tipp) AND is_int($tipp)) $this->tooltip = $str->getStr($tipp);
        }

        /**
         * @return bool|int
         */
        protected function isValid() {
            // Prüfung auf korrekte syntax - keine Semantikprüfung!
            if (isset($valStr)) {
                if (preg_match('/' . $this->valStr . '/', $this->inhalt))
                    return true; else return false;
            }
            return 4; // kein Validierungsstring vorhanden
        }

        function display() {
            // feldname, inhalt, label, tooltip
            $daten = [
                $this->name,
                $this->inhalt,
                $this->label,
                $this->tooltip];
            return $daten;
        }
    }


    /**
     * arrayverarbeitung
     *
     * @param $arr
     * @return array
     */
    function a_display($arr) {
        $data = [];
        foreach ($arr as $val) {
            if (is_array($val->display())) {
                $a = $val->display();
                if ($a) $data[$a[0]] = $a;
            }
        }
        return $data;
    }
}