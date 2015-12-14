<?php namespace DiafIPReader {
    /**
     * Klassenbibliotheken für Personen und Aliasnamen
     *
     * Dazu gehören eine Klasse für Namen und eine Klasse für Personen inclusive der Interfaces
     */


    /**
     * Interface iPName
     * @author      Knut Wetzig <knwetzig@gmail.com>
     * @copyright   2014 Deutsches Institut für Animationsfilm e.V.
     * @license     http://opensource.org/licenses/BSD-3-Clause BSD-3 License
     * @package     DiafIPReader\Person
     * @since       r99
     * @requirement PHP Version >= 5.4
     */
    interface iPName extends iEntity {

        /**
         * Ermittelt die Person zum Aliasnamen
         *
         * @return mixed
         */
        function getPerson();

        /**
         * Listet alle unbenutzten Aliasnamen (nicht Personen)
         *
         * @return mixed
         */
        function getName();

        /**
         * liefert die ID's+Bereich des Suchmusters
         *
         * @param string $s
         */
        static function search($s);
    }
}