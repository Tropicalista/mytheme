window.cookieconsent.initialise({
  container: document.getElementById("content"),
  palette:{
    popup: {background: "#fff"},
    button: {background: "#aa0000"},
  },
  content: {
    message: "Questo sito usa i cookie per una migliore esperienza utente.",
    dismiss: "Ok",
    link: "Leggi",
    href: window.location.protocol + "//" + window.location.host + "/privacy/"
  },
  dismissOnScroll: 100,
  onStatusChange: function(status) {
    if(this.element.parentNode)
    this.element.parentNode.removeChild(this.element);
  },
  law: {
    regionalLaw: false,
  },
  location: true,
});