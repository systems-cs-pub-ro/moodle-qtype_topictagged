# Content

0. [Introduction](/README.md#introduction)
1. [Installation](/README.md#installation)
2. [Adding Questions](/README.md#adding-questions)
3. [Managing Questions](/README.md#managing-questions)
4. [Updating the Local Database](/README.md#updating-the-local-database)
5. [Exporting Questions](/README.md#exporting-questions)
6. [References](/README.md#references)

# Introduction

Abbreviations: MXML (Moodle XML)

This plugin is used for creating quizzes that are unique for every student and that contain questions with specific difficulties and topics chosen by evaluators.
The selection of the questions is automated based on options from evaluators.
The aim is to have a fair distribution of difficulty levels and topics among the unique quizzes created for each student.

In order to use all questions evenly, we keep a timestamp marking when each question was last used.
There will be a preference to use questions that haven't been recently used.
As defined in [Managing Questions](README.md#managing-questions), the `last_used` question tag keeps the time when the question was last used, measured in seconds since the Epoch (00:00:00 UTC, January 1, 1970).
The plugin automatically updates the `last_used` tag after every questions use, and can [export it in multiple ways](/README.md#exporting-questions), so it can be imported and reused in a different course or in another Moodle instance.

# Installation

### Installation From Downloaded zip File

You can download the zip from:
#### latest [(master branch)](https://github.com/systems-cs-pub-ro/quiz-manager-moodle/zipball/master)

Follow the steps below in your Moodle installation (administrative rights are required):

* Go to `Site administration > Plugins > Install plugins`.
* Upload the zip file.
  If you are prompted to add extra details, under `Plugin type` select `Question type (qtype)`.
* Go to `Show more > Rename the root directory` and type `quizmanager`.
* If your target directory is not writable, you will see a warning message.
  Proceed to [Manual installation](/README.md#manual-installation-from-zip-file).
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

Questions are added to a quiz using the Moodle interface, after the plugin was installed, from the quiz menu.
**Note:** You have to import questions to the question bank beforehand.

* After creating a quiz, access it and press the `Edit quiz` button.
* On the right side of the page, go to `Add > + a new question` and select the `Topic Tagged` question type.
* Select the category where you want your question to be from.
* Set the difficulty from the given list _(Easy - Hard)_.
* Set the topic that you want your question to have.
  Note: You can choose **only one** topic for one question.
* Press the `Save changes` button.
* Repeat the instructions for all the questions you want in your quiz.

**Note:** If you preview the added question, you will only see the first selected question from the question bank, and the `last_used` tag will not be updated.
The students will each receive different questions having the selected difficulty and topic.

# Managing Questions

The plugin can use any [question type](https://docs.moodle.org/311/en/Question_types) in the question bank, as long as the question has the corresponding difficulty and topic tag.

If questions are imported from an external source, using any [format](https://docs.moodle.org/311/en/Import_questions#Question_import_formats) supported by Moodle, the following rules must be followed:
* The difficulty tag must be one from the following list: `Easy`, `Easy-Medium`, `Medium`, `Medium-Hard`, `Hard`.
* The `last_used` tag is optional (if not present, it will be considered `0`).
  The format is: `last_used:time`, where time is an integer representing time since the Epoch (00:00:00 UTC, January 1, 1970), measured in seconds.
* The topic tags **MUST NOT** include the character `-`, as it is internally used by the plugin as a delimiter between tags.

Details on the import procedure can be found in the [Moodle documentation](https://docs.moodle.org/311/en/Moodle_XML_format).

The listing below is a template for creating tagged questions in MXML format.
Simply update it with the contents of the proposed question: statement, answers, tags.

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

# Updating the Local Database

After every question import, the local database that stores the `last_used` tag must be updated.
Otherwise the `last_used` tag will be set to `0`, losing its meaning.

To do that, in the main course page, navigate to the `Actions menu`(top right of the page, in the course header) and select `Quiz Manager Administration` option.
Under the `Update database` section, select the [category](https://docs.moodle.org/311/en/Question_categories) of the questions and press the `update` button.

# Exporting Questions

You can export the questions in order to reuse them in a different Moodle instance.
You can choose the regular [MXML](https://docs.moodle.org/310/en/Moodle_XML_format) format, or a CSV file format that contains three columns: question text, question hash, the `last_used` tag.

The question hash is used to uniquely identify the question such that the metadata (i.e. `last_used` field) can be updated in an external database.
This requires further processing according to the chosen storage solution.
The hashed string has the following format: `sha256(Question_textQuestion_answer1,Question_answer2...)`.

To download the file, navigate to the `Actions menu`(top right of the page, in the course header) in the main course page and select the `Quiz Manager Administration` option.
Select the desired file format and the [category](https://docs.moodle.org/311/en/Question_categories), then press the `Download` button under the `Download questions` section.

# References

* [QType Template](https://github.com/marcusgreen/moodle-qtype_TEMPLATE/)
* [Moodle Docs](https://docs.moodle.org/dev/Question_types)
