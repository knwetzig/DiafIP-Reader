<?php namespace DiafIP {
    use Exception, MDB2;
    /**
     * Klassenbibliotheken für Filmografische Daten
     *
     * @author      Knut Wetzig <knwetzig@gmail.com>
     * @copyright   2014 Deutsches Institut für Animationsfilm e.V.
     * @license     http://opensource.org/licenses/BSD-3-Clause BSD-3 License
     * @package     DiafIP
     * @version     $Id$
     * @since       r52
     * @requirement PHP Version >= 5.4
     */
    final class Film extends FibiMain {
        const
            SQL_GET_FILM        = 'SELECT gattung,prodtechnik,fsk,praedikat,mediaspezi,urauffuehr,laenge,bildformat
                                   FROM f_film2
                                   WHERE id = ?;',

            GET_BILDFORMAT      = 'SELECT format
                                   FROM f_bformat
                                   WHERE id = ?;',

            SQL_GET_BILDFORMAT_LI = 'SELECT *
                                     FROM f_bformat
                                     ORDER BY id ASC;',

            SQL_GET_GENRE       = 'SELECT * FROM f_genre;',

            SQL_GET_MEDIASPEZ   = 'SELECT * FROM f_mediaspezi;',

            SQL_GET_PRAED       = 'SELECT * FROM f_praed ORDER BY praed ASC;',

            SQL_GET_PRODTECHNIK = 'SELECT * FROM f_prodtechnik;',

            TYPE_FILM           = 'integer,integer,integer,integer,integer,date,text,integer';

        private $fehler = [];

        public function __construct($nr = null) {
            parent::__construct($nr);
            $this->content['gattung']  = null;
            $this->content['prodtechnik']   = null;
            $this->content['fsk'] = null;
            $this->content['praedikat'] = 0;
            $this->content['mediaspezi'] = 0;
            $this->content['urauffuehr'] = null;
            $this->content['laenge'] = null;
            $this->content['bildformat'] = 0;
            if ((isset($nr)) AND is_numeric($nr)) :
                $db   = MDB2::singleton();
                $data = $db->extended->getRow(self::SQL_GET_FILM, list2array(self::TYPE_FILM), $nr, 'integer');
                self::WertZuwCont($data);
            endif;
        }

        /**
         * @return array
         */
        public function getContent() {
            return $this->content;
        }

        /**
         * @param array $content
         */
        public function setContent($content) {
            $this->content = $content;
        }

        /**
         * Ausgabe des Filmdatensatzes (an smarty)
         * @return array|int
         */
        protected function view() {
            global $str;

            $data = parent::view();
            // name, inhalt, opt -> rechte, label,tooltip
            $data[] = new d_feld('prod_land', self::getProdLand(), null, 698);
            $data[] = new d_feld('gattung', $str->getStr($this->content['gattung']), null, 579);
            $data[] = new d_feld('prodtech', self::getThisProdTech(), null, 571);
            $data[] = new d_feld('laenge', $this->content['laenge'], null, 580);
            $data[] = new d_feld('fsk', $this->content['fsk'], null, 581);
            $data[] = new d_feld('praedikat', $str->getStr($this->content['praedikat']), null, 582);
            $data[] = new d_feld('bildformat', self::getBildformat(), null, 608);
            $data[] = new d_feld('mediaspezi', self::getThisMediaSpez(), null, 583);
            $data[] = new d_feld('urauff', $this->content['urauffuehr'], null, 584);
            $data[] = new d_feld('regie', self::getRegie(), null, 1000);

            return $data;
        }

        /**
         * Ermitttelt die Namen der Regisseure für diesen Film
         * @return array
         */
        protected function getRegie() {
            $db = MDB2::singleton();
            $Regie = $db->extended->getCol(
                'SELECT f_cast.pid FROM public.f_cast WHERE fid = ? AND tid = 1000', 'integer', $this->content['id'],
                'integer');
            IsDbError($Regie);
            $namen = [];
            foreach($Regie as $wert) :
                $pers = new PName($wert);
                $namen[] = $pers->getName();
            endforeach;
            return $namen;
        }

        /**
         * Aufgabe: Prüft, ob für diesen filmogr. Datensatz ein Hersteller
         *          angelegt ist und gibt im Erfolgsfall, das aus den Personen-
         *          daten ermittelte Land zurück.
         * @return array | NULL
         */
        protected function getProdLand() {
            $db       = MDB2::singleton();
            $ProdLand = $db->extended->getCol(
                'SELECT s_land.land FROM public.f_cast, public.p_person2, public.s_land, public.s_orte
                 WHERE
                   f_cast.pid = p_person2.id AND p_person2.wort = s_orte.id AND
                   s_orte.land = s_land.id AND f_cast.fid = ? AND f_cast.tid = ?;',
                null, [$this->content['id'], 1480], ['integer', 'integer']
            );
            IsDbError($ProdLand);
            return $ProdLand;
        }

        /**
         * Gibt eine Liste der Produktionstechniken zurück
         * @return array (string)
         */
        static function getListProdTech() {
            global $str;
            $db   = MDB2::singleton();
            $data = $db->extended->getCol(self::SQL_GET_PRODTECHNIK, 'integer');
            IsDbError($data);
            return $str->getStrList($data);
        }


        /**
         * Gibt eine Liste der verwendeten Produktionstechniken zurück
         * @return array (string)
         */
        protected function getThisProdTech() {
            $list = self::getListProdTech();
            $data = [];
            foreach ($list as $key => $wert) :
                if (isbit($this->content['prodtechnik'], $key)) $data[] = $wert;
            endforeach;
            return $data;
        }

        /**
         * Gibt eine Liste der Mediaspezifikationen zurück
         * @return array(int)
         */
        protected static function getListMediaSpez() {
            global $str;
            $db   = MDB2::singleton();
            $data = $db->extended->getCol(self::SQL_GET_MEDIASPEZ, 'integer');
            IsDbError($data);
            return $str->getStrList($data);
        }

        protected function getBildformat() {
            // gibt den string mit dem Bildformat zurück
            $db = MDB2::singleton();
            if (empty($this->content['bildformat'])) return null;
            $data = $db->extended->getOne(
                self::GET_BILDFORMAT, null, $this->content['bildformat']);
            IsDbError($data);
            return $data;
        }

        /**
         * Gibt die Liste der verwendeten Mediadaten zurück
         * @return array (string)
         */
        protected function getThisMediaSpez() {
            $list = self::getListMediaSpez();
            $data = [];
            foreach ($list as $key => $wert) :
                if (isbit($this->content['mediaspezi'], $key)) $data[] = $wert;
            endforeach;
            return $data;
        }
    }
}