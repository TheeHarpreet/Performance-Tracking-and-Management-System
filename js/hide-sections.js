function hideSection(num) {
    sectionName = "section-hide" + num ;
    buttonName = "toggle-button" + num;
    var section = document.getElementById(sectionName);
    var button = document.getElementById(buttonName);
    if (section.style.display === "none") {
        section.style.display = "block";
        button.innerHTML = "Hide";
    } else {
        section.style.display = "none";
        button.innerHTML = "Show";
    }
  }