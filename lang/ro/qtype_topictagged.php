<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Strings for component 'qtype_topictagged', language 'ro', branch 'MOODLE_20_STABLE'
 *
 * @package    qtype
 * @subpackage topictagged
 * @copyright  2022 Andrei David; Ștefan Jumărea

 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
$string['pluginname'] = 'Topic Tagged Question';
$string['pluginname_help'] = 'Selectează o întrebare pe baza dificultății și a topicului';
$string['pluginname_link'] = 'question/type/topictagged';
$string['pluginnameadding'] = 'Adaugă o întrebare';
$string['pluginnameediting'] = 'Editează o întrebare';
$string['pluginnamesummary'] = 'Selectează o întrebare din banca de întrebări cu dificultatea și topicul ' .
    'dorite care nu a fost folosită recent.<br>' .
    '<p><font color=#F57542><b>Atenție:</b></font><br>' .
    '<font color=#A14A28>Nu adăugați acest tip de întrebare în banca de întrebări. Adăugați direct în quiz.</font></p><br>' .
    '<p><font color=#4287F5><b>Notă: </b></font>' .
    '<font color=#1C3C70>Previzualizarea întrebării arată o întrebare selectată aleator, cu dificultatea si topicul cerute, fără să afecteze procesul de selecție.</font></p>' .
    '<p><font color=#4287F5><b>Notă: </b></font>' .
    '<font color=#1C3C70>Dacă în timpul testului nu mai există întrebări valabile, se va selecta o întrebare aleatoare</font></p>' .
    '<p>Pentru mai multe informații consultați <a href="https://github.com/systems-cs-pub-ro/moodle-qtype_topictagged#readme">Documentația</a></p>';
$string['setdifficulty'] = 'Alegeți dificultatea';
$string['settags'] = 'Alegeți topicul';
$string['settagsempty'] = 'Nu s-a selectat niciun topic';
$string['noquestions'] = 'Nu au fost găsite întrebări cu dificultatea și topicul selectate. Nu au fost adăugate întrebări la quiz.';
$string['fileformat_warning'] = '<font color=#F57542><b>Atenție: </b></font>'.
    '<font color=#A14A28>Fișierul <b>MXML</b> poate fi reimportat; fișierul <b>CSV</b> <u>nu</u> poate fi reimportat.</font>';

$string['selectedby'] = '{$a->questionname} selected by {$a->randomname}';

$string['update_button'] = 'Actualizare';
$string['download_button'] = 'Descărcare';
$string['download_mode'] = 'Formatul fișierului';

$string['download_header'] = 'Exportați întrebările';
$string['update_header'] = 'Actualizați baza de date';

$string['question_cat'] = 'Categoria de întrebări';

$string['randomqname'] = 'Topic Tagged Question {$a}';

$string['editsettings'] = 'Administrarea Întrebărilor de tipul Topic Tagged';

$string['setdifficulty_help'] = 'Alegeți dificultatea întrebării din lista de mai jos.';
$string['settags_help'] = 'Topicurile sunt taguri definite de utilizatori care sunt adăugate întrebărilor în banca de întrebări. Ele trebuie să își păstreze forma în care au fost scrise când au fost adăugate întrebărilor, altfel nu vor fi găsite întrebări care să respecte condițiile. Topicurile sunt case insensitive și se poate alege un singur topic pentru o întrebare.';
$string['update_header_help'] = 'Actualizați baza de date locală pentru a include tagul `last_used` al întrebărilor din categoria selectată. Actualizarea TREBUIE făcută de fiecare dată după importarea unui nou set de întrebări.';
$string['download'] = 'Exportați întrebările';
$string['download_help'] = 'Pentru a exporta întrebările, selectați categoria din care vreți sa descărcați întrebările, apoi alegeți un format de fișier.<ol><li><b><i><a href=https://docs.moodle.org/310/en/Moodle_XML_format> MXML </a> (Recomandat)</i></b> poate fi ușor reimportat. </li><li><i>CSV(complex)</i> mai ușor de manipulat datele cât timp exsita o copie locală.<br>Fișierul CSV va conține doar întrebările folosite în cel puțin un quiz.<br>Consultați <a href=https://github.com/systems-cs-pub-ro/moodle-qtype_topictagged#readme> documentația </a> pentru mai multe informații.</li></ol>';
$string['available_questoins'] = 'Numărul de întrebări disponibile:';
