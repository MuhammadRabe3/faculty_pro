// Toggle Current Subject Details Button
// Details Button Toggle
let allSubjectDetailsButtons = document.querySelectorAll(
	".box .subject-details-btn"
);

allSubjectDetailsButtons.forEach((btn) => {
	btn.addEventListener("click", () => {
		let subjectDetails = btn.parentElement.parentElement.nextElementSibling;
		subjectDetails.classList.toggle("deactive");
	});
});
