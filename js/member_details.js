// Details Button Toggle
let allMemberDetailsButtons = document.querySelectorAll(
	".member-box .member-details-btn"
);

allMemberDetailsButtons.forEach((btn) => {
	btn.addEventListener("click", () => {
		let memberDetails = btn.parentElement.parentElement.nextElementSibling;
		memberDetails.classList.toggle("deactive");
	});
});
