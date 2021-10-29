# Contents

0. [Introduction](/README.md#introduction)
1. [Installation](/README.md#installation)
2. [Adding Questions](/README.md#adding-questions)
3. [Managing Questions](/README.md#managing-questions)
4. [User Stories](/README.md#user-stories)
5. [References](/README.md#references)

# Introduction

Abbreviations: MXML(Moodle XML)

This plugin is used for creating custom quizzes using questions from question bank and letting the Evaluator choose the
difficulty and topic for every question.
The plugin also updates the `last_used` tag with the time the specific question was used,
in order to avoid overusing some questions and ignoring others.

# Installation

### Installation From Downloaded zip file

You can download the zip from:
#### latest [(master branch)](https://github.com/systems-cs-pub-ro/quiz-manager-moodle/zipball/master)

* Login as an admin and go to `Site administration > Plugins > Install plugins`
* Upload the zip file. If you are prompted to add extra details, under `Plugin type` select `Question type (qtype)`
* Go to `Show more > Rename the root directory` and type `quizmanager`
* If your target directory is not writable, you will see a warning message. Proceed to
[Manual installation](/README.md#manual-installation-from-zip-file)
* Press `Install plugin from the ZIP file` button

### Manual Installation From zip file

If you are prompted with the message `Plugin type location /your/moodle/path/question/type is not writable`,
you have to copy the files manually to the server file system, using a zip file or [Git](/README.md#manual-installation-using-git)

* Download the [zip file](https://github.com/systems-cs-pub-ro/quiz-manager-moodle/zipball/master)
* Unzip the file in the correct location: `/your/moodle/path/question/type`
* Rename the directory to `quizmanager`

### Manual Installation Using Git

To install using git, type this command:
```
git clone https://github.com/systems-cs-pub-ro/quiz-manager-moodle.git /your/moodle/path/question/type/quizmanager
```
or directly in the root of your Moodle install:
```
git clone https://github.com/systems-cs-pub-ro/quiz-manager-moodle.git question/type/quizmanager
```

# Adding Questions

* After creating a quiz, access it and press the `Edit quiz` button
* On the right side of the page, go to `Add > + a new question` and select the `quizmanager` type
* Select the category where you want your question to be from
* Set the difficulty from the given list _(Easy - Hard)_
* Set the topic that you want your question to have
* Press the `Save changes` button
* Repeat the instructions for all the questions you want in your quiz

# Managing Questions

The plugin can use any question types existent in the question bank, as long as the have the associated difficulty and topic tag.

If imported from an external database, the following rules must be followed:
* The difficulty tag must be one from the following list: `Easy, Easy-Medium, Medium, Medium-Hard, Hard`
* The topic tag can take any form, as long as it is consistent with the set topic in the question edit form
(see [Adding Questions](/README.md/#adding-questions))
* The `last_used` tag is optional (if not present will be considered 0), and the format is: `last_used:time`, where
time is an integer representing time since the Epoch (00:00:00 UTC, January 1, 1970), measured in seconds

Details for the importing procedure can be found in the [Moodle documentation](https://docs.moodle.org/311/en/Moodle_XML_format)

The following example can be used for importing questions with tags in MXML format (note that you can modify
any detail about the questions, the example only shows the use of tags in MXML):
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

## __User stories__:
|As a...|I want to...|so that...|
|------|--------|-----|
|evaluator|generate quizzes according to clear specifications|I can make sure that every student will have a similar quiz with different questions|
|evaluator|choose a source for the questions: already loaded questions, new questions from a file or external database|I can choose what set of questions to use for every quiz|
|evaluator|to have the option to choose from default templates|I can generate the quiz fast, without specifying all the data|
|evaluator|choose if I want to use new questions or nor|avoid recently used questions|
|evaluator|keep the default quiz setting available|set the timer, review options, etc.|
|evaluator|add tags to my questions|specify the difficulty, topic and other relevant data|
|evaluator|update the last used date of the questions|avoid reusing them|
|Moodle administrator|avoid question duplicates|create a backup effectively|
|Moodle administrator|not to crash the server|all the users can use Moodle|
|student|have the quiz ready when I click "Attempt"|do not waste the allocated time|


## __References__
 - [QType Template](https://github.com/marcusgreen/moodle-qtype_TEMPLATE/)
 - [Moodle Docs](https://docs.moodle.org/dev/Question_types)
