// Select All Checkbox in Attendance

// let allCheckBoxes = document.querySelectorAll(".check");
// let selectAllCheck = document.querySelector(".select-all");

// allCheckBoxes.forEach((check) => {
// 	selectAllCheck.addEventListener("click", () => {
// 		check.checked = selectAllCheck.checked;
// 	});
// 	if (check.checked === true) {
// 		selectAllCheck.checked = true;
// 	}
// });

let allCheckBoxes = document.querySelectorAll(".check");
let selectAllCheck = document.querySelector(".select-all");

// Function to check if all checkboxes are checked
function areAllCheckboxesChecked() {
	for (let i = 0; i < allCheckBoxes.length; i++) {
		if (!allCheckBoxes[i].checked) {
			return false;
		}
	}
	return true;
}

// Event listener for "Select All" checkbox
selectAllCheck.addEventListener("click", () => {
	const isChecked = selectAllCheck.checked;
	allCheckBoxes.forEach((checkbox) => {
		checkbox.checked = isChecked;
	});

	// Save state to localStorage
	localStorage.setItem("selectAllChecked", isChecked);
});

// Event listener for individual checkboxes
allCheckBoxes.forEach((checkbox) => {
	checkbox.addEventListener("change", () => {
		selectAllCheck.checked = areAllCheckboxesChecked();

		// Save state to localStorage
		localStorage.setItem("selectAllChecked", selectAllCheck.checked);
	});
});

// Restore checkbox state from localStorage
const selectAllChecked = localStorage.getItem("selectAllChecked");
if (selectAllChecked === "true") {
	selectAllCheck.checked = true;
}
