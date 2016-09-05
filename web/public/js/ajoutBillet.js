// On recupere le div contenant le prototype des billets
var $container = $('div#reservation_billets');

// On definit le compteur de billets en comptant le nombre d'enfants de container
// car un enfant de container est un billet
var index = $container.children().length;

// On selection le bouton 'rajouter billet' et on ajout un billet au click
$('#ajout_billet').click(function(e){
  // On appelle la fonction ajoutant un billet
  ajoutBillet($container);

  // Afin d'eviter un "#" trainant dans l'url
  e.preventDefault();
  return false;
});

// La fonction qui ajoute des billets
function ajoutBillet($container) {

  // On va recuperer le data-prototype du container
  // Puis remplacer les __namee__label par billet +index
  // et les __name__ par index
  var template = $container.attr('data-prototype')
    .replace(/__name__label__/g, 'Billet n°'+(index+1))
    .replace(/__name__/g, index)
    ;

  // On crée un objet jquery contenant le template
  var $prototype = $(template);

  // On ajout un lien de suppresion
  addDeleteLink($prototype);

  // on ajout le prototype au container
  $container.append($prototype);

  // Enfin, on incremente le compteur
  index ++;
}

// La fonction supprimant le billet
function addDeleteLink($prototype) {
  //Création du lien
  var $deleteLink = $('<a href="#" class="btn btn-danger btn-xs">Supprimer</a>');
  var $divDeleteLink = $('<div class="text-center"></div');
  $divDeleteLink.append($deleteLink);

  // On ajoute le bouton au prototype
  $prototype.append($divDeleteLink);

  // Ajoute de la suppresion du billet au click
  $deleteLink.click(function(e) {
    $prototype.remove();

    e.preventDefault();
    return false;
  })
}
