// Select All Checkbox in Attendance

let allCheckBoxes = document.querySelectorAll(".check");
let selectAllCheck = document.querySelector(".select-all");

allCheckBoxes.forEach((check) => {
	selectAllCheck.addEventListener("click", () => {
		check.checked = selectAllCheck.checked;
	});
	if (check.checked === true) {
		selectAllCheck.checked = true;
	} else {
		selectAllCheck.checked = false;
	}
});

function areAllCheckboxesChecked() {
	for (let i = 0; i < allCheckBoxes.length; i++) {
		if (!allCheckBoxes[i].checked) {
			return false;
		}
	}
	return true;
}

allCheckBoxes.forEach((checkbox) => {
	checkbox.addEventListener("change", () => {
		selectAllCheck.checked = areAllCheckboxesChecked();
	});
});
