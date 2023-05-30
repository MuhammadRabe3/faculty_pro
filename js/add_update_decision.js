const yesCheckBox = document.querySelector(".resp-yes");
const noCheckBox = document.querySelector(".resp-no");
const responseToRow = document.querySelector(".resp-to");
const isExcutiveRow = document.querySelector(".is-excu");

console.log(isExcutiveRow);

yesCheckBox.addEventListener("click", () => {
	responseToRow.classList.remove("deactive");
	isExcutiveRow.classList.remove("deactive");
});
noCheckBox.addEventListener("click", () => {
	responseToRow.classList.add("deactive");
	isExcutiveRow.classList.add("deactive");
});

if (yesCheckBox.checked === true) {
	responseToRow.classList.remove("deactive");
	isExcutiveRow.classList.remove("deactive");
}
