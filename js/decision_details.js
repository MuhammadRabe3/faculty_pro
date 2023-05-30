// Toggle Decision Details Button
let allDecisionDetailsButtons = document.querySelectorAll(
	".decision-buttons button.dec-details-btn"
);
let allDecisionStatusButtons = document.querySelectorAll(
	".decision-buttons button.dec-status-btn"
);
let allDecisionFilesButtons = document.querySelectorAll(
	".decision-buttons button.dec-files-btn"
);
allDecisionDetailsButtons.forEach((ele) => {
	ele.addEventListener("click", () => {
		ele.parentElement.parentElement.nextElementSibling.classList.toggle(
			"deactive"
		);
	});
});
allDecisionStatusButtons.forEach((ele) => {
	ele.addEventListener("click", () => {
		ele.parentElement.parentElement.nextElementSibling.nextElementSibling.classList.toggle(
			"deactive"
		);
	});
});
allDecisionFilesButtons.forEach((ele) => {
	ele.addEventListener("click", () => {
		ele.parentElement.parentElement.nextElementSibling.nextElementSibling.nextElementSibling.classList.toggle(
			"deactive"
		);
	});
});
