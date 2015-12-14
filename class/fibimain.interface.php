<?php namespace DiafIPReader {
    /**
     * Klassenbibliotheken für Filmogr.-/Bibliografische Daten
     *
     * @author      Knut Wetzig <knwetzig@gmail.com>
     * @copyright   2015 Deutsches Institut für Animationsfilm e.V.
     * @license     http://opensource.org/licenses/BSD-3-Clause BSD-3 License
     * @package     DiafIPReader-Reader\Film
     */

    interface iFibiMain extends iEntity {
        static function getSTitelList();
        function getTitel();
        static function search($s);
    }
}