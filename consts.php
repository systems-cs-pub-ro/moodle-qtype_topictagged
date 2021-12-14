<?php
        $sql_question_answer_lastused = '
                SELECT question.id "id", question.questiontext "question_text", GROUP_CONCAT(answers.answer) "answers", topictagged.lastused "last_used"
                FROM {question} question
                    JOIN {question_answers} answers
                        ON answers.question = question.id
                    JOIN {question_topictagged} topictagged
                        ON question.id = topictagged.questionid
                WHERE question.category = :categoryid
                GROUP BY question.id;
        ';

?>
