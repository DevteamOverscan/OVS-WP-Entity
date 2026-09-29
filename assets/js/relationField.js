/**
 * Champ relation (src/fields/relation.php) : filtre la liste des contenus pendant la saisie
 */
document.addEventListener("input", function (e) {
  if (!e.target.matches(".row-relation .relation-search")) {
    return;
  }

  var query = e.target.value.trim().toLowerCase();
  var field = e.target.closest(".row-relation");

  field.querySelectorAll(".relation-item").forEach(function (item) {
    item.hidden = query !== "" && item.textContent.toLowerCase().indexOf(query) === -1;
  });
});

// Entrée dans la recherche : ne soumet pas le formulaire
document.addEventListener("keydown", function (e) {
  if (e.key === "Enter" && e.target.matches(".row-relation .relation-search")) {
    e.preventDefault();
  }
});
