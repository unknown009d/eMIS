document.querySelectorAll(".print_area").forEach(items => {
    items.addEventListener('dblclick', e => {
        if(!confirm("Are you sure want to make this content editable?")) return;
        e.preventDefault();
        e.target.contentEditable = true;
    });
});


$("chkShowRemarks").addEventListener("change", e => {
  if($('extratext')){
    $('extratext').setAttribute("colspan", e.target.checked ? 6 : 7);
  }
  document.querySelectorAll(".item-remarks").forEach(d => {
      d.style.display = e.target.checked ? "none" : "table-cell";
  });
  localStorage.setItem('chkShowRemarks', e.target.checked);
});

const stateRestore = (checkboxid) => {
    const savedState = localStorage.getItem(checkboxid);
    if (savedState !== null) {
        $(checkboxid).checked = savedState === 'true';
        $(checkboxid).dispatchEvent(new Event('change'));
    }
};

stateRestore("chkShowRemarks");