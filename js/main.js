// Drop Down List Function Import

import { dropDownList } from "./dropdownlist.js";

dropDownList();

// *Code Your Js Here //

// Make The Link Active When Clicked

let currentLocation = location.href;
let allLinks = document.querySelectorAll("nav a");

allLinks.forEach((ele) => {
	if (ele.href === currentLocation) {
		ele.classList.add("active");
	}
});

// Making the upload icon fade when hovering it
let uploadLabel = document.querySelectorAll(".upload");
let uploadIcon = document.querySelectorAll(".fa-upload");

for (let i = 0; i < uploadLabel.length; i++) {
	uploadLabel[i].addEventListener("mouseenter", () => {
		uploadIcon[i].classList.add("fa-fade");
	});
	uploadLabel[i].addEventListener("mouseleave", () => {
		uploadIcon[i].classList.remove("fa-fade");
	});
}

const buttons = document.querySelectorAll(".upload-button");
const fileLists = document.querySelectorAll(".file-list");

buttons.forEach((button, i) => {
	button.addEventListener("change", function () {
		displayFile(this.files, fileLists[i]);
	});
});

function displayFile(files, fileList) {
	fileList.innerHTML = "";

	// Loop Through The Files And Display Each File Name and Preview
	for (let i = 0; i < files.length; i++) {
		const fileName = document.createElement("p");
		fileName.textContent = `اسم الملف:   ${files[i].name}`;
		fileList.appendChild(fileName);

		if (files[i].type === "image/jpeg" || files[i].type === "image/png") {
			const filePreview = document.createElement("img");
			filePreview.src = URL.createObjectURL(files[i]);
			fileList.appendChild(filePreview);
		}
	}
}

console.log("AYMAN");

// Links Toggler

const toggler = document.querySelector("nav ul .icon");
const linksDiv = document.querySelector("nav ul .links");
const linksDivOpen = document.querySelector("nav ul .links.open");

toggler.addEventListener("click", () => {
	linksDiv.classList.toggle("open");
});

// mohamed fathi
//dialog
const openButton = document.querySelectorAll("[data-open-modal]");
const closeButton = document.querySelector("[data-close-modal]");

openButton.forEach((openBtn) => {
	openBtn.addEventListener("click", () => {
		let modal = openBtn.nextElementSibling;
		modal.showModal();
	});
});
// closeButton.addEventListener("click", () => {
//   modal.close();
// });
