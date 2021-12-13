<?php
// SQL Queries
$sql_questionids_anytopic_anydifficulty = '
        SELECT id from {question}
        WHERE category = :categoryid AND hidden = 0 AND qtype != "topictagged" AND qtype != "random"
    ';

$sql_questionids_anydifficulty = '
        SELECT tag_instance.itemid
        FROM {tag} tag
            JOIN {tag_instance} tag_instance ON tag.id = tag_instance.tagid
        WHERE strcmp(upper(tag_instance.itemtype), \'QUESTION\') = 0 AND strcmp(upper(tag.name), upper(:topic)) = 0
        INTERSECT
        SELECT question.id
        FROM {tag_instance} tag_instance
            JOIN {question} question ON question.id = tag_instance.itemid
        WHERE question.category = :categoryid AND question.hidden = 0
     ';

$sql_questionids_anytopic = '
        SELECT tag_instance.itemid
        FROM {tag} tag
            JOIN {tag_instance} tag_instance ON tag.id = tag_instance.tagid
        WHERE strcmp(upper(tag_instance.itemtype), \'QUESTION\') = 0 AND strcmp(upper(tag.name), upper(:difficulty)) = 0
        INTERSECT
        SELECT question.id
        FROM {tag_instance} tag_instance
            JOIN {question} question ON question.id = tag_instance.itemid
        WHERE question.category = :categoryid AND question.hidden = 0
     ';

$sql_questionids = '
        SELECT tag_instance.itemid
        FROM {tag} tag
            JOIN {tag_instance} tag_instance ON tag.id = tag_instance.tagid
        WHERE strcmp(upper(tag_instance.itemtype), \'QUESTION\') = 0 AND strcmp(upper(tag.name), upper(:difficulty)) = 0
        INTERSECT
        SELECT tag_instance.itemid
        FROM {tag} tag
            JOIN {tag_instance} tag_instance ON tag.id = tag_instance.tagid
        WHERE strcmp(upper(tag_instance.itemtype), \'QUESTION\') = 0 AND strcmp(upper(tag.name), upper(:topic)) = 0
        INTERSECT
        SELECT question.id
        FROM {tag_instance} tag_instance
            JOIN {question} question ON question.id = tag_instance.itemid
        WHERE question.category = :categoryid AND question.hidden = 0
     ';
?>
