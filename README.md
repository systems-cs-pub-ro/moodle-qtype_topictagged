# Contents

0. [Introduction](/README.md#introduction)
1. [Installation](/README.md#installation)
2. [Adding Questions](/README.md#adding-questions)
3. [Managing Questions](/README.md#managing-questions)
4. [Updating local database](/README.md#updating-local-database)
5. [Downloading questions](/README.md#downloading-questions)
6. [References](/README.md#references)

# Introduction

Abbreviations: MXML (Moodle XML)

This plugin is used for creating quizzes that are unique for every student and contains questions with specific difficulties and topics chosen by evaluators.
The selection of the questions is automated based on the options that evaluators choose, so you can have a good distribution of difficulty levels and topics.

We want to choose the questions randomly based on the selected difficulty and topic, and to make sure that all the questions are used evenly.
In order to do this consistently when reusing the same questions in multiple Moodle instances, we need to keep timestamps of every question last use.

As defined in [Managing Questions](README.md#managing-questions), the `last_used` question tag keeps the time of the question was last use, measured in seconds since the Epoch (00:00:00 UTC, January 1, 1970).
The plugin automatically updates the `last_used` tag after every questions use, and can [export it in multiple ways](/README.md#downloading-questions), so it can be imported and reused in a different Moodle instance.

# Installation

### Installation From Downloaded zip File

You can download the zip from:
#### latest [(master branch)](https://github.com/systems-cs-pub-ro/quiz-manager-moodle/zipball/master)

Follow the steps below in your Moodle installation (administrative rights are required):

* Go to `Site administration > Plugins > Install plugins`.
* Upload the zip file. If you are prompted to add extra details, under `Plugin type` select `Question type (qtype)`.
* Go to `Show more > Rename the root directory` and type `quizmanager`.
* If your target directory is not writable, you will see a warning message. Proceed to [Manual installation](/README.md#manual-installation-from-zip-file).
* Press `Install plugin from the ZIP file` button.

### Manual Installation From zip file

If you are prompted with the message `Plugin type location /your/moodle/path/question/type is not writable`, you have to copy the files manually to the server file system, using a zip file or [Git](/README.md#manual-installation-using-git).

* Download the [zip file](https://github.com/systems-cs-pub-ro/quiz-manager-moodle/zipball/master).
* Unzip the file in the correct location: `/your/moodle/path/question/type`.
* Rename the directory to `quizmanager`.

### Manual Installation Using Git

To install using Git, use this command:

```
git clone https://github.com/systems-cs-pub-ro/quiz-manager-moodle.git /your/moodle/path/question/type/quizmanager
```

# Adding Questions

Questions are added using the Moodle interface, after the plugin was installed, from the quiz menu.

* After creating a quiz, access it and press the `Edit quiz` button.
* On the right side of the page, go to `Add > + a new question` and select the `quizmanager` question type.
* Select the category where you want your question to be from.
* Set the difficulty from the given list _(Easy - Hard)_.
* Set the topic that you want your question to have. Note: topics are case **insensitive**, and you can choose **only one** topic for one question.
* Press the `Save changes` button.
* Repeat the instructions for all the questions you want in your quiz.

# Managing Questions

The plugin can use any [question types](https://docs.moodle.org/311/en/Question_types) existent in the question bank, as long as the have the associated difficulty and topic tag.

If questions are imported from an external source, using any [format](https://docs.moodle.org/311/en/Import_questions#Question_import_formats) supported by Moodle, the following rules must be followed:
* The difficulty tag must be one from the following list: `Easy, Easy-Medium, Medium, Medium-Hard, Hard`.
* The topic tag can take any form, as long as it is consistent with the set topic in the question edit form (see [Adding Questions](/README.md/#adding-questions)).
* The `last_used` tag is optional (if not present will be considered 0), and the format is: `last_used:time`, where time is an integer representing time since the Epoch (00:00:00 UTC, January 1, 1970), measured in seconds.

Details for the importing procedure can be found in the [Moodle documentation](https://docs.moodle.org/311/en/Moodle_XML_format).

The following example can be used for importing questions with tags in MXML format (note that you can modify any detail about the questions, the example only shows the use of tags in MXML):

```xml
<?xml version="1.0" ?>

<quiz>
    <question type="multichoice">
        <name>
            <text>
                Question Name Goes Here
            </text>
        </name>
        <questiontext format="html">
            <text>
		    Question Text Goes Here
            </text>
        </questiontext>

        <tags>
            <tag>
                <text>
			First Tag Goes Here
                </text>
            </tag>
            <tag>
                <text>
			Second Tag Goes Here
                </text>
            </tag>
	    <tag>
		<text>
			Third Tag Goes Here
		</text>
	    </tag>
        </tags>

        <answer fraction="100">
            <text>
                Correct Answer Goes Here
            </text>
        </answer>

        <answer fraction="0">
            <text>
                Incorrect Answer Goes Here
            </text>
        </answer>
    </question>
</quiz>
```

# Updating local database

After every question import, the local database that stores the `last_used` tag must be updated, or the questions will be used with the default value of 0, making it useless.

To do that, in the main course page, navigate to the `Actions menu`(top right of the page, in the course header) and select `Quiz Manager Administration` option.
Under the `Update database` section, select the [category](https://docs.moodle.org/311/en/Question_categories) of the questions and press the `update` button.

# Downloading questions

You can download the questions in order to reuse them in a different Moodle instance.
You can choose the regular [MXML](https://docs.moodle.org/310/en/Moodle_XML_format) format, or a csv file format that contains the following columns:

`Question text,Question hash,last used tag`

The hash is used to identify the question, in case of having multiple questions with the same question text.
The hashed string has the following format: `sha256(Question_textQuestion_answer1,Question_answer2...)`.

To download the file, navigate to the `Actions menu`(top right of the page, in the course header) in the main course page and select the `Quiz Manager Administration` option.
Select the desired file format and the [category](https://docs.moodle.org/311/en/Question_categories), then press the `Download` button under the `Download questions` section.

# References

* [QType Template](https://github.com/marcusgreen/moodle-qtype_TEMPLATE/)
* [Moodle Docs](https://docs.moodle.org/dev/Question_types)
