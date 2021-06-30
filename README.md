# Custom Quiz Moodle Plugin
__User stories__:
|Ca ...|Vreau să|Ca să|
|------|--------|-----|
|profesor|generez teste conform unor specificații clare|mă asigur că fiecare student va avea un test similar cu întrebări diferite|
|profesor|îmi aleg sursa întrebărilor: întrebări încărcate deja, să încarc un fișier cu întrebările sau mă leg la o baza de date externă|aleg la fiecare test ce întrebări vreau să folosesc|
|profesor|să existe valori implicite|pot genera rapid teste chiar dacă nu specific toate datele testului|
|profesor|selectez dacă vreau sau nu întrebări noi|evit intrebarile recent folosite|
|profesor|pastrez configurațiile Moodle-specific ale testului|setez timpul de deschidere, review etc.|
|profesor|adaug tag-uri întrebărilor|specific dificultatea, topicul, și alte date relevante|
|profesor|am propriul nivel de dificultate (easy - hard; 1 - 5; ușor - greu)|nu depind de un sistem de măsurare a dificultății fix|
|profesor|se actualizeze data utilizării întrebărilor în mod automat|evit refolosirea acestora|
|administrator Moodle|nu existe întrebări duplicat|pot face backup eficient
|administrator Moodle|nu blocheze plaforma|permită tuturor utilizatorilor să folosească Moodle|
|student|Am testul pregătit când dau click pe “Attempt quiz”|nu pierd din timpul alocat testului|





__File structure:__
```
  mod_form.php: a form to setup/update a module instance
  version.php: defines some meta-info and provides upgrading code
  pix/icon.gif: a 16x16 icon for the module
  db/install.xml: an SQL dump of all the required db tables and data
  index.php: a page to list all instances in a course
  view.php: a page to view a particular instance
  lib.php: any/all functions defined by the module should be in here.
         constants should be defined using MODULENAME_xxxxxx
         functions should be defined using modulename_xxxxxx

         There are a number of standard functions:

         modulename_add_instance()
         modulename_update_instance()
         modulename_delete_instance()

         modulename_user_complete()
         modulename_user_outline()

         modulename_cron()

         modulename_print_recent_activity()
```
Moodle Documentation:  http://moodle.org/doc
<br>Moodle Community:      http://moodle.org/community


