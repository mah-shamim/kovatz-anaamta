document.addEventListener('DOMContentLoaded', function() {
  const showIf = document.querySelectorAll('.show-if');

  showIf.forEach(function(el) {
    const dataControl = el.dataset.control;
    const dataValue = el.dataset.controlValue;

    const control = document.querySelector(`[id="${dataControl}"]`);
    if (!control) {
      return;
    }

    if (control.value === dataValue) {
      el.style.display = 'block';
    } else {
      el.style.display = 'none';
    }

    control.addEventListener('change', function() {
      if (control.value === dataValue) {
        el.style.display = 'block';
      } else {
        el.style.display = 'none';
      }
    });
  });
});
