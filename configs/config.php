<?php namespace DiafIP {

    /**
     * Diese Datei nicht bearbeiten - Do not edit this File
     * Bearbeiten sie für Ihre lokalen Einstellungen
     * die Datei config.local.php
     */

    /**
     * @author    Knut Wetzig <knwetzig@gmail.com>
     * @copyright 2012-2014 Deutsches Institut für Animationsfilm
     * @version   $Id$
     * @package   DiafIP\configs
     */

    require_once "service.php";
    require_once "MDB2.php";
    require_once "Smarty.class.php";
    require_once "entity.interface.php";
    require_once "pname.interface.php";
    require_once "person.interface.php";
    require_once "fibimain.interface.php";

    const
        /**
         * Regex für Namen - Keine Interpunktion, nur runde Klammern
         */
    REG_NAMEN  = '^[^`*+!-\':-@[-^{-~]+$',
    REG_ANZAHL = '([1-9]+[\d]*){1,1}',
        // Dezimalzahl -1,2e-3
    REG_DZAHL  = '^[-+]?[\d]*[.,]?[\d]+([eE][-+]?[\d]+)?',
    REG_PLZ    = '^[\d]{4,7}$',
    REG_DAUER  = '^(([\d]+[hH])?([\d]+[mM])?([\d]*(([.][\d]+)?|[sS]))?|([\d]+[:][\d]+[:][\d]+)+([.][\d]+)?)$',
    REG_BOOL   = '(^(true|[1]|false|[0])\b){1,1}',
    REG_TELNR = '^[+\d][\d\s]*$', // +49 351 123456
    REG_EMAIL = '[\w].*[@].*[.][\w]{2,3}', //xxx@yyy.zzz
    REG_DATUM = '[\d]{4,4}[\D\W][\d]{1,2}[\D\W][\d]{1,2}|[\d]{4,4}[0-1][\d][0-3][\d]|[\d]{1,2}[\D\W][\d]{1,2}[\D\W][\d]{2,4}',
        /* Vorsicht nicht Narrensicher! Kann nur der groben Prüfung dienen
            1999-2.31 ISO (Trenner kann alles ausser Buchtabe/Ziffer sein)
            19991231  ISO
            31.12/19 German/Euro/US (Sehr locker)
        */

        // Rechte-Zuweisung
    RE_VIEW   = 0, // sieht alle allgemein zugängl. Daten
    RE_IVIEW  = 1, // sieht auch interne Daten
    RE_EDIT   = 2, // kann allg. Daten editieren
    RE_IEDIT  = 3, // editieren interner Daten (Personaldaten etc)
    RE_SEDIT  = 4, // kann Presets bearbeiten
    RE_DELE   = 5, // Löschberechtigung
    RE_ARCHIV = 6, // Depotverwaltung
        /*
        .
        siehe auch configs/adm_user.php */
    RE_ADMIN  = 15,
    RE_SU     = 16,

    WERT_QUOT = 0.03; // Wertsteigerungsquotient zur Berechnung der Vers.Summe

    // Sektion für 'sektion'
    $datei = [
        'N'     => 'inc/ev_name.php',
        'P'     => 'inc/ev_person.php', // Personenverzeichnis
        'F'     => "inc/ev_figd.php", // filmografische Daten
        'impr'  => 'inc/impressum.php'];

}