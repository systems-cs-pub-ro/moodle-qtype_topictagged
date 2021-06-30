# Custom Quiz Moodle Plugin


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


