function validation(form) {
  const required = form.querySelectorAll('.en__mandatory');

  Array.from(required).forEach(el => {
    const input = el.querySelector('input, select');
    input.addEventListener('change', e => {
      console.log('change', input.value, input);
      if (input.value) input.classList.remove('is-invalid');
    });
  });

  const validateBlock = function(block) {
    const required = block.querySelectorAll('.en__mandatory');
    let invalid = null;

    Array.from(required).forEach(el => {
      const input = el.querySelector('input, select');
      if (!input.value) {
        invalid = input;
        input.classList.add('is-invalid');
      }
    });

    if (invalid) {
      return false;
    }

    return true;
  };

  return validateBlock;
}

export default validation;
