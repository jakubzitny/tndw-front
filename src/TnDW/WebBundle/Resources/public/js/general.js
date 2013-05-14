function select_text(el) {
  if (document.selection) {
    var range = document.body.createTextRange();
    range.moveToElementText(el);
    range.select();
  } else if (window.getSelection) {
    var range = document.createRange();
    range.selectNode(el);
    window.getSelection().addRange(range);
  }
}
