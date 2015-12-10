<?php namespace DiafIP;
use MDB2;
/**
 *  Klassenbibliotheken für die Verwaltung von Orten (Personen/ Lagermöglichkeiten)
 * @todo Revision dringend erforderlich
 */

/** Orte */
/*
 * func: __construct($)
 * ::getOrt($!)    // holt die Ortsdaten aus der Ortstabelle -> array
 * get()         // --dito-- schreibt dies aber ins Objekt
 * neu()
 * edit()
 * set()         // schreibt objekt -> db
 * del()         // löscht einen Ort
 * ::getOrtList()  // listet alle Orte in einem Array
 *
 * @todo Die Liste mit den Staaten und Ländern wird händisch geflegt Überarbeitung dieser Klasse zwingend erforderlich!
 */
class Ort {
    protected
        $oid = null,
        $lid = 1, // Landeskennung
        $ort = null,
        $land = null,
        $bland = null;

    function __construct($nr = null) {
        if (isset($nr) AND ($nr > 0)) {
            $this->oid = $nr;
            $this->get();
        } else $this->neu(false);
    }

    protected function get() { // die dynamische Version
        $db   = MDB2::singleton();
        $sql  = 'SELECT * FROM orte WHERE oid = ?;';
        $data = $db->extended->getRow($sql, null, [$this->oid]);
        IsDbError($data);
        /* ACHTUNG: Die Kombination mit einem statischen Aufruf führt zum
        Überschreiben von Speicherinhalten!!! deswegen gibt es 2 Versionen */
        foreach ($this as $key => &$wert) $wert = $data[$key];
        unset($wert);
    }

    public static function getOrt($nr) { // die statische Version
        $db   = MDB2::singleton();
        $sql  = 'SELECT * FROM orte WHERE oid = ?;';
        $data = $db->extended->getRow($sql, null, [$nr]);
        IsDbError($data);
        return $data;
    }

    public static function getOrtList() {
        // listet alle Orte in einem Array
        $db   = MDB2::singleton();
        $sql  = 'SELECT * FROM orte;';
        $data = $db->extended->getAll($sql);
        IsDbError($data);
        $orte = ['-- unbekannt --'];
        foreach ($data as $val) {
            $st                = $val['ort'] . '&nbsp;-&nbsp;' . $val['land'];
            $orte[$val['oid']] = $st;
        }
        return $orte;
    }

    function getLandList() {
        $db   = MDB2::singleton();
        $sql  = 'SELECT * FROM s_land ORDER BY s_land.land ASC, s_land.bland ASC;';
        $data = $db->extended->getAll($sql);
        IsDbError($data);
        $laend = [];
        foreach ($data as $val) {
            $laend[$val['id']] = (empty($val['bland']) ? $val['land'] : $val['bland'] . "&nbsp;-&nbsp;" . $val['land']);
        }
        return $laend;
    }
} //endclass;