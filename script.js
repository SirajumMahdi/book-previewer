// Function to show the popup
function showPopup() {
  var popup = document.querySelector(".bp-popup");
  popup.style.display = "block";
}

// Function to close the popup
function closePopup() {
  var popup = document.querySelector(".bp-popup");
  popup.style.display = "none";
}

// Attach a click event to the close button
var closeButton = document.querySelector(".bp-close-popup");
closeButton.addEventListener("click", closePopup);

// Attach a click event to the open button
var openButton = document.querySelector(".bp-open-popup-button");
openButton.addEventListener("click", showPopup);
