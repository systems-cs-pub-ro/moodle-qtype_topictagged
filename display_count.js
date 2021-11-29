function eventHandler () {
	// extract selected topic, difficulty, category
	var cat_id = document.getElementById("id_category").selectedIndex;
	var dif_id = document.getElementById("id_setdifficulty").options[document.getElementById("id_setdifficulty").selectedIndex].text;
	var top_id = document.getElementById("id_settags").value;

	// extract exported JSON from PHP
	var json_string = document.getElementById("id_json").innerHTML;

	// parse JSON
	var json = JSON.parse(json_string);

	// display counter
	var availableQ = json[cat_id][dif_id][top_id];
	console.log(cat_id, dif_id, top_id);
	console.log(availableQ);
	document.getElementById("id_availablequestions_count").innerHTML = availableQ;
}

// initial update
eventHandler();

// on `select` change update
document.getElementById("id_category").addEventListener("change", event => {
	eventHandler();
});

document.getElementById("id_setdifficulty").addEventListener("change", event => {
	eventHandler();
});

document.getElementById("id_settags").addEventListener("change", event => {
	eventHandler();
});

